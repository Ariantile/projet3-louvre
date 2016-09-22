<?php
// src/Louvre/BilletterieBundle/PreparationPaiement/LouvrePreparationPaiement.php

namespace Louvre\BilletterieBundle\PreparationPaiement;

use Payum\Core\Request\GetHumanStatus;

class LouvrePreparationPaiement
{

    protected $doctrine;
    
    public function __construct($doctrine, $payum, $sendmail)
    {      
        $this->doctrine = $doctrine;
        $this->payum = $payum;
        $this->sendmail = $sendmail;
    }
    
    public function getCommandeEnCours($idCommande)
    {
        $commandeEnCours = $this
            ->doctrine
            ->getManager()
            ->getRepository('LouvreBilletterieBundle:Billet')
            ->getCommande($idCommande);
        
        return $commandeEnCours;
    }
    
    /**
     * Prépation de la page de paiement
     *
     */
    public function preparePayment($demiJournee, $commandeEnCours)
    {
        $total = 0;
        
        foreach ($commandeEnCours as $billet)
        {
            $prix = $billet->getPrixBillet();
            $total = $prix + $total;
        }
        
        if ($demiJournee === true) {
            $total = $total/2;
        }
        
        $storage = $this->payum->getStorage('Louvre\BilletterieBundle\Entity\Payment');
        
        /** @var $payment PaymentDetails */
        $payment = $storage->create();
        $payment["amount"] = $total * 100;
        $payment["currency"] = 'EUR';
        $payment["description"] = 'Louvre Billetterie';
        $payment["metadata"] = array ("numero_commande" => $commandeEnCours[0]->getCommande()->getNumCommande());
        
        return $payment;
    }
    
    public function postPayment($gatewayName, $payment, $route, $idCommande)
    {
        $storage = $this->payum->getStorage('Louvre\BilletterieBundle\Entity\Payment');
        
        $storage->update($payment);
        
        $em = $this->doctrine->getManager();
        $update = $em->getRepository('LouvreBilletterieBundle:Commande')->find($idCommande);
        
        $update->setPaymentId($payment->getId());
            
        $em->flush();
            
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            $route
        );
        
        return $captureToken;
    }
    
    public function retourToken($token)
    {
        $identity = $token->getDetails();
        $gateway = $this->payum->getGateway($token->getGatewayName());
        $gateway->execute($status = new GetHumanStatus($token));
        
        return $status;
    }
    
    public function paiementValide($idCommande, $commandeEnCours, $image)
    {
            $em = $this->doctrine->getManager();
            $update = $em->getRepository('LouvreBilletterieBundle:Commande')->find($idCommande);  
            $courriel = $update->getFacturation()->getCourriel();
            $envoiMail = $this->sendmail;
            $envoiMail->sendMail($image, $commandeEnCours, $courriel);
            $update->setStatus('Valide');
            $update->setNumCommande($update->getNumCommande() . $idCommande);
            
            foreach ($commandeEnCours as $billet) {
                $billet->setCodeReservation($billet->getCodeReservation() . $idCommande);
            }
            
            $em->flush();
    }
    
    public function paiementFailed($idCommande)
    {
        $em = $this->doctrine->getManager();        
        $update = $em->getRepository('LouvreBilletterieBundle:Commande')->find($idCommande);
        $update->setStatus('Failed');
        
        $em->flush();
    }
}