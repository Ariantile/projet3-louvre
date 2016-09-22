<?php
// src/Louvre/BilletterieBundle/SendMail/LouvreSendMail.php

namespace Louvre\BilletterieBundle\SendMail;

class LouvreSendMail
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $doctrine)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
    }

    /**
     * Génération et envoi de mail
     *
     */
    public function sendMail($image, $commandeEnCours, $courriel)
    {
        $mail = \Swift_Message::newInstance();
            
        $logo = $mail->embed(\Swift_Image::fromPath($image));
            
        $mail->setSubject('Louvre billetterie - Vos billets')
             ->setFrom('billetterie@louvre.com')
             ->setTo($courriel)
             ->setBody(
                    $this->twig->render(
                        'LouvreBilletterieBundle:Billetterie:mailbillet.html.twig',
                        array('infos' => $commandeEnCours, 'logo' => $logo)
                    ),
                    'text/html'
                );
            
            $this->mailer->send($mail);
    }
    
    public function renvoiMail($commandes, $courriel, $image)
    {
        $em = $this->doctrine->getManager();
        
        foreach ($commandes as $commande) {

            $status = $commande->getStatus();
            
            if ($status === 'Valide') {
                    
                $idCommande = $commande->getId();
                    
                $commandeEnCours = $em
                    ->getRepository('LouvreBilletterieBundle:Billet')
                    ->getCommande($idCommande);
                    
                sendMail($image, $commandeEnCours, $courriel);
            }
        }   
    }
}