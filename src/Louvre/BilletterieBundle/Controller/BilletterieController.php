<?php
// src/Louvre/BilletterieBundle/Controller/BilletterieController.php

namespace Louvre\BilletterieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Louvre\BilletterieBundle\Entity\Commande;
use Louvre\BilletterieBundle\Entity\Recherche;
use Louvre\BilletterieBundle\Entity\Contact;
use Louvre\BilletterieBundle\Form\Type\CommandeType;
use Louvre\BilletterieBundle\Form\Type\RechercheType;
use Louvre\BilletterieBundle\Form\Type\ContactType;
use \DateTime;

class BilletterieController extends Controller
{
    public function formaccAction(Request $request)
    {        
        $session = new Session();
        $commande = new Commande();
        $date = new \DateTime('now');
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);
        $em = $this->getDoctrine()->getManager();
        $countBillet = $em->getRepository('LouvreBilletterieBundle:Billet')->countBillet($commande->getDateReservation());
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if ($countBillet >= 1000) {
                $session->getFlashBag()->add('erreur', 'louvre.flash.erreur.billet');
            } else {
                $demiJournee = $commande->getDemiJournee();
                $creationCommade = $this->container->get('louvre_commande.create');
                $creationCommade->createCommande($commande, $demiJournee);
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
        $prepPaiement = $this->container->get('louvre_paiement.prepare');
        $commandeEnCours = $prepPaiement->getCommandeEnCours($idCommande);
        $payment = $prepPaiement->preparePayment($demiJournee, $commandeEnCours);
        if ($request->isMethod('POST') && $request->request->get('stripeToken')) {
            $payment["card"] = $request->request->get('stripeToken');
            $captureToken = $prepPaiement->postPayment($gatewayName, $payment, 'louvre_payment_done', $idCommande);
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
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);
        $paiementValidation = $this->container->get('louvre_paiement.prepare');
        $status = $paiementValidation->retourToken($token);
        if ($status->isCaptured()) {
            $session->invalidate();
            $commandeEnCours = $paiementValidation->getCommandeEnCours($idCommande);
            $paiementValidation->paiementValide($idCommande, $commandeEnCours);
        } else if ($status->isPending() || $status->isFailed()) {
            $paiementValidation->paiementFailed($idCommande);
            $session->getFlashBag()->add('erreur', 'louvre.done.erreur');
            return $this->redirectToRoute('louvre_billetterie_paiement');
        }
        return $this->render('LouvreBilletterieBundle:Billetterie:remerciement.html.twig', array(
            'commandeEnCours' => $commandeEnCours));
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
                $envoiMail = $this->container->get('louvre_send.mail');
                $envoiMail->renvoiMail($commandes, $courriel);
                $session->getFlashBag()->add('erreur', 'louvre.recover.reussite');
            }
        }
        return $this->render('LouvreBilletterieBundle:Billetterie:recover.html.twig', array(
            'form' => $form->createView() ));
    }
    
    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $contact = new Contact();
        $form = $this->get('form.factory')->create(ContactType::class, $contact);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $courriel = $contact->getEmail();
            $titre = $contact->getTitre();
            $message = $contact->getMessage();
            $nom = $contact->getNom();
            $envoiContact = $this->container->get('louvre_send.mail');
            $envoiContact->sendContact($courriel, $titre, $message, $nom);
            $session->getFlashBag()->add('erreur', 'louvre.contact.reussite');
        }
        return $this->render('LouvreBilletterieBundle:Billetterie:contact.html.twig', array(
            'form' => $form->createView() ));
    }
    
    public function infoAction(Request $request)
    {
        return $this->render('LouvreBilletterieBundle:Billetterie:infoprat.html.twig');
    }
}
