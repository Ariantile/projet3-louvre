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
use Louvre\BilletterieBundle\Entity\Quantite;
use Louvre\BilletterieBundle\Entity\Tarifs;
use Louvre\BilletterieBundle\Entity\Facturation;
use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Commande;
use Louvre\BilletterieBundle\Form\QuantiteType;
use Louvre\BilletterieBundle\Form\TarifsType;
use Louvre\BilletterieBundle\Form\FacturationType;
use Louvre\BilletterieBundle\Form\BilletType;
use Louvre\BilletterieBundle\Form\CommandeType;
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
              
            $commande->setNumCommande(uniqid());
            $commande->setDateCommande(new DateTime('now'));
            
            $demiJournee = $commande->getDemiJournee();
            
            $commande->setStatus('En cours');
            
            $billets = $commande->getBillets();
            
            $total = 0;
            
            foreach ($billets as $billet) {
                $billet->setCodeReservation(uniqid());
                $billet->setCommande($commande);
                $prixBillet = $billet->getPrixBillet();
                $total = $total + $prixBillet;
            }
            
            if ($demiJournee === true) {
                $commande->setSousTotal($total/2);   
            } else {
                $commande->setSousTotal($total);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();
                
            //$request->getSession()->getFlashBag()->add('idCommande', $commande->getId());
   
            $session->set('idCommande'  , $commande->getId());
            $session->set('demiJournee' , $commande->getDemiJournee());
                
            return $this->redirectToRoute('louvre_billetterie_paiement');
        }
        
        return $this->render('LouvreBilletterieBundle:Billetterie:formacc.html.twig', array(
                'formcommande'  => $form->createView(),
                'dateConnexion' => $date,
        ));
    }
    
    public function paiementAction(Request $request)
    {
        $session = $request->getSession();
        
        $gatewayName = 'louvre_stripe_checkout';
        
        $idCommande = $session->get('idCommande');
        $demiJournee = $session->get('demiJournee');
        
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
        
        if ($request->isMethod('POST') && $request->request->get('stripeToken')) {
            
            $payment["card"] = $request->request->get('stripeToken');
            $storage->update($payment);
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
        //Stopper la session
        
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $identity = $token->getDetails();
        $model = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());
        
        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have token you can get the model from the storage directly. 
        //$identity = $token->getDetails();
        //$details = $payum->getStorage($identity->getClass())->find($identity);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $details = $status->getFirstModel();
        
        /*{"status":"captured","details":{"amount":3200,"currency":"eur","description":"Louvre Billetterie","card":"tok_18tdpyISiGMCWUEpmBiFXKuq","id":"ch_18tdq3ISiGMCWUEpYylkpVJf","object":"charge","amount_refunded":0,"application_fee":null,"balance_transaction":"txn_18tdq3ISiGMCWUEpXffMfSBg","captured":true,"created":1473861251,"customer":null,"destination":null,"dispute":null,"failure_code":null,"failure_message":null,"fraud_details":[],"invoice":null,"livemode":false,"metadata":[],"order":null,"paid":true,"receipt_email":null,"receipt_number":null,"refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"\/v1\/charges\/ch_18tdq3ISiGMCWUEpYylkpVJf\/refunds"},"shipping":null,"source":{"id":"card_18tdpyISiGMCWUEp5nvT6dNd","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":2,"exp_year":2020,"fingerprint":"BN7j0ZTinSSvlC0T","funding":"credit","last4":"4242","metadata":[],"name":"ariantile@hotmail.com","tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"status":"succeeded"}}*/
        
        
        
        
        
        
        // you have order and payment status 
        // so you can do whatever you want for example you can just print status and payment details.

        return new JsonResponse(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($details),
        ));
    }
    
    public function validationAction(Request $request)
    {        
        $commandeEnCours = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LouvreBilletterieBundle:Billet')
            ->getCommande(12);
        
        return $this->render('LouvreBilletterieBundle:Billetterie:validation.html.twig', array(
            'commandeEnCours' => $commandeEnCours
        ));
    }
    

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
    
}
