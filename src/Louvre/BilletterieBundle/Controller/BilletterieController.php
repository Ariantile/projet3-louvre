<?php
// src/Louvre/BilletterieBundle/Controller/BilletterieController.php

namespace Louvre\BilletterieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Louvre\BilletterieBundle\Entity\Quantite;
use Louvre\BilletterieBundle\Form\QuantiteType;
use Louvre\BilletterieBundle\Entity\Tarifs;
use Louvre\BilletterieBundle\Form\TarifsType;
use Symfony\Component\HttpFoundation\Request;

class BilletterieController extends Controller
{
    public function formaccAction()
    {        
        $repository = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('LouvreBilletterieBundle:Tarifs');

        $listTarifs = $repository->findAll();
        
        $i = count($listTarifs);
        /*
        for ($c = 0; $c < $i; $c++)
        {
            $quantite.$c = new Quantite;
        }
        */
        return $this->render('LouvreBilletterieBundle:Billetterie:formacc.html.twig', array(
               'listTarifs' => $listTarifs,
        ));
    }
    
    public function paiementAction()
    {
        return $this->render('LouvreBilletterieBundle:Billetterie:paiement.html.twig');
    }
}
