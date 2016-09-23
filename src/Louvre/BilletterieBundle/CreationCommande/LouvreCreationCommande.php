<?php
// src/Louvre/BilletterieBundle/CreationCommande/LouvreCreationCommande.php

namespace Louvre\BilletterieBundle\CreationCommande;

use \DateTime;

class LouvreCreationCommande
{

    protected $doctrine;
    
    public function __construct($doctrine)
    {      
        $this->doctrine = $doctrine;
    }

    /**
     * Génération et envoi de la commande
     *
     */
    public function createCommande($commande, $demiJournee)
    {
        $em = $this->doctrine->getManager();
        
        $commande->setNumCommande(uniqid());
        $commande->setDateCommande(new DateTime('now'));
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
    }
}
