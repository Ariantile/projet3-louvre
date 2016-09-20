<?php
// src/Louvre/BilletterieBundle/Controller/BilletterieController.php

namespace Louvre\BilletterieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Louvre\BilletterieBundle\Entity\Payment;
use Louvre\BilletterieBundle\Entity\Tarifs;
use Louvre\BilletterieBundle\Entity\Facturation;
use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Commande;
use Louvre\BilletterieBundle\Entity\Recherche;
use Louvre\BilletterieBundle\Form\QuantiteType;
use Louvre\BilletterieBundle\Form\TarifsType;
use Louvre\BilletterieBundle\Form\FacturationType;
use Louvre\BilletterieBundle\Form\BilletType;
use Louvre\BilletterieBundle\Form\CommandeType;
use Louvre\BilletterieBundle\Form\RechercheType;
use Payum\Core\Payum;
use Payum\Core\Security\SensitiveValue;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Bridge\Symfony\Form\Type\CreditCardType;
use Payum\Core\Model\CreditCardInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Request\GetHumanStatus;
use Payum\Stripe\Request\Api\CreatePlan;
use Louvre\BilletterieBundle\Entity\PaymentDetails;
use \DateTime;

class BilletterieController extends Controller
{
    public function formaccAction(Request $request)
    {        
        $session = new Session();
        
        $commande = new Commande();
        
        $date = new \DateTime('now');

        $form = $this->get('form.factory')->create(CommandeType::class, $commande);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $countBillet = $em->getRepository('LouvreBilletterieBundle:Billet')->countBillet($commande->getDateReservation());
            
            if ($countBillet >= 1000) {
                $session->getFlashBag()->add('erreur', 'louvre.flash.erreur.billet');
            } else {
                
                $session->set('countB' , $countBillet);
                
                $commande->setNumCommande(uniqid());
                $commande->setDateCommande(new DateTime('now'));
            
                $demiJournee = $commande->getDemiJournee();
            
                $commande->setStatus('Ongoing');
            
                $billets = $commande->getBillets();
            
                $total = 0;
            
                $i = 1;
            
                foreach ($billets as $billet) {
                    $billet->setCodeReservation($commande->getNumCommande() . $i);
                    $billet->setCommande($commande);
                    $prixBillet = $billet->getPrixBillet();
                    $total = $total + $prixBillet;
                    $i++;
                }
            
                if ($demiJournee === true) {
                    $commande->setSousTotal($total/2);   
                } else {
                    $commande->setSousTotal($total);
                }
            
                $em->persist($commande);
                $em->flush();
   
                $session->set('idCommande'  , $commande->getId());
                $session->set('demiJournee' , $commande->getDemiJournee());
                
                return $this->redirectToRoute('louvre_billetterie_paiement');
            }
        }
        
        return $this->render('LouvreBilletterieBundle:Billetterie:formacc.html.twig', array(
                'formcommande'  => $form->createView(),
                'dateConnexion' => $date,
        ));
    }
    
    public function paiementAction(Request $request)
    {
        $session = $request->getSession();
        
        if ($session->has('idCommande')) {
            $idCommande = $session->get('idCommande');
            $demiJournee = $session->get('demiJournee');
        } else {
            $session->getFlashBag()->add('erreur', 'louvre.flash.erreur.session');
            return $this->redirectToRoute('louvre_core_homepage');
        }
        
        $gatewayName = 'louvre_stripe_checkout';
        
        $commandeEnCours = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LouvreBilletterieBundle:Billet')
            ->getCommande($idCommande);
        
        $total = 0;
        
        foreach ($commandeEnCours as $billet)
        {
            $prix = $billet->getPrixBillet();
            $total = $prix + $total;
        }
        
        if ($demiJournee === true) {
            $total = $total/2;
        }
        
        $storage = $this->getPayum()->getStorage('Louvre\BilletterieBundle\Entity\Payment');
        
        /** @var $payment PaymentDetails */
        $payment = $storage->create();
        $payment["amount"] = $total * 100;
        $payment["currency"] = 'EUR';
        $payment["description"] = 'Louvre Billetterie';
        $payment["metadata"] = array ("numero_commande" => $commandeEnCours[0]->getCommande()->getNumCommande());
        
        if ($request->isMethod('POST') && $request->request->get('stripeToken')) {
            
            $payment["card"] = $request->request->get('stripeToken');
            $storage->update($payment);
            $em = $this->getDoctrine()->getManager();
            $update = $em->getRepository('LouvreBilletterieBundle:Commande')->find($idCommande);
            $update->setPaymentId($payment->getId());
            
            $em->flush();
            
            $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken(
                $gatewayName,
                $payment,
                'louvre_payment_done'
            );
            
            return $this->redirect($captureToken->getTargetUrl());
            
        }
        
        return $this->render('LouvreBilletterieBundle:Billetterie:paiementstripe.html.twig', array(
            'publishable_key' => $this->container->getParameter('stripe.publishable_key'),
            'model' => $payment,
            'gatewayName' => $gatewayName,
            'commandeEnCours' => $commandeEnCours
        ));
    }
    
    public function doneAction(Request $request)
    {
        $session = $request->getSession();
        
        if ($session->has('idCommande')) {
            $idCommande = $session->get('idCommande');
        } else {
            $session->getFlashBag()->add('erreur', 'louvre.flash.erreur.session');
            return $this->redirectToRoute('louvre_core_homepage');
        }
        
        $idCommande = $session->get('idCommande');
        
        $em = $this->getDoctrine()->getManager();
        
        $commandeEnCours = $em
            ->getRepository('LouvreBilletterieBundle:Billet')
            ->getCommande($idCommande);
        
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $identity = $token->getDetails();
        $model = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());
        
        $gateway->execute($status = new GetHumanStatus($token));
        $details = $status->getFirstModel();
        
        if ($status->isCaptured()) {
            
            $session->invalidate();
            $update = $em->getRepository('LouvreBilletterieBundle:Commande')->find($idCommande);
            $update->setStatus('Valide');
            $update->setNumCommande($update->getNumCommande() . $idCommande);
            
            foreach ($commandeEnCours as $billet) {
                $billet->setCodeReservation($billet->getCodeReservation() . $idCommande);
            }
            
            $em->flush();
                        
            $image = $this->container->get('kernel')->getRootDir().'/../web/bundles/louvrebilletterie/images/logo.png';
            
            $mail = \Swift_Message::newInstance();
            
            $logo = $mail->embed(\Swift_Image::fromPath($image));
            
            $mail->setSubject('Louvre billetterie - Vos billets')
                ->setFrom('billetterie@louvre.com')
                ->setTo($update->getFacturation($idCommande)->getCourriel())
                ->setBody(
                    $this->renderView(
                        'LouvreBilletterieBundle:Billetterie:mailbillet.html.twig',
                        array('infos' => $commandeEnCours, 'logo' => $logo)
                    ),
                    'text/html'
                );
            
            $this->get('mailer')->send($mail);
            
        } else if ($status->isPending() || $status->isFailed()) {
            
            $session->invalidate();
            $update = $em->getRepository('LouvreBilletterieBundle:Commande')->find($idCommande);
            $update->setStatus('Failed');
            
            $em->flush();
            
        }
        
        return $this->render('LouvreBilletterieBundle:Billetterie:remerciement.html.twig', array(
            'commandeEnCours' => $commandeEnCours
        ));
        
        return new JsonResponse(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($details),
        ));
    }
    
    public function recoverAction(Request $request) 
    {
        $session = $request->getSession();
        
        $recherche = new Recherche();
        
        $form = $this->get('form.factory')->create(RechercheType::class, $recherche);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            $courriel = $recherche->getCourriel();
            
            $commandes = $em->getRepository('LouvreBilletterieBundle:Commande')->renvoiBillet($courriel);
            
            if (!$commandes) {
                $session->getFlashBag()->add('erreur', 'louvre.recover.erreur');
            } else {
            
                $image = $this->container->get('kernel')->getRootDir().'/../web/bundles/louvrebilletterie/images/logo.png';
            
                foreach ($commandes as $commande) {
                    
                    $status = $commande->getStatus();
                    
                    if ($status === 'Valide') {
                    
                        $idCommande = $commande->getId();
                
                        $commandeEnCours = $em
                            ->getRepository('LouvreBilletterieBundle:Billet')
                            ->getCommande($idCommande);
                
                        $mail = \Swift_Message::newInstance();
            
                        $logo = $mail->embed(\Swift_Image::fromPath($image));
            
                        $mail->setSubject('Louvre billetterie - Vos billets')
                            ->setFrom('billetterie@louvre.com')
                            ->setTo($courriel)
                            ->setBody(
                                $this->renderView(
                                    'LouvreBilletterieBundle:Billetterie:mailbillet.html.twig',
                                    array('infos' => $commandeEnCours, 'logo' => $logo)
                                ),
                                'text/html'
                            );
            
                        $this->get('mailer')->send($mail);
                    }
                }
            
                $session->getFlashBag()->add('erreur', 'louvre.recover.reussite');

            }
        }
        
        return $this->render('LouvreBilletterieBundle:Billetterie:recover.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function affichageAction(Request $request) 
    {        
        return $this->render('LouvreBilletterieBundle:Billetterie:recoveraffichage.html.twig');
    }
    
    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
    
}
