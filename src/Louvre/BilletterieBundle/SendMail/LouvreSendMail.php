<?php
// src/Louvre/BilletterieBundle/SendMail/LouvreSendMail.php

namespace Louvre\BilletterieBundle\SendMail;

class LouvreSendMail
{
    private $mailer;
    private $twig;
    private $doctrine;
    private $container;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $doctrine, $container)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

    /**
     * Génération et envoi de mail
     *
     */
    public function sendMail($commandeEnCours, $courriel)
    {
        $image = $this->container->get('kernel')->getRootDir().'/../web/bundles/louvrebilletterie/images/logo.png';
        
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
    
    public function sendContact($courriel, $titre, $message, $nom)
    {
        $image = $this->container->get('kernel')->getRootDir().'/../web/bundles/louvrebilletterie/images/logo.png';
        
        $mail = \Swift_Message::newInstance();
            
        $logo = $mail->embed(\Swift_Image::fromPath($image));
            
        $mail->setSubject('Louvre billetterie - Message de ' . $nom)
             ->setFrom($courriel)
             ->setTo($this->container->getParameter('mail_contact'))
             ->setBody(
                    $this->twig->render(
                        'LouvreBilletterieBundle:Billetterie:mailcontact.html.twig',
                        array('titre' => $titre, 'message' => $message, 'logo' => $logo)
                    ),
                    'text/html'
                );
            
        $this->mailer->send($mail);
    }
    
    public function renvoiMail($commandes, $courriel)
    {
        $em = $this->doctrine->getManager();
        
        foreach ($commandes as $commande) {

            $status = $commande->getStatus();
            
            if ($status === 'Valide') {
                    
                $idCommande = $commande->getId();
                    
                $commandeEnCours = $em
                    ->getRepository('LouvreBilletterieBundle:Billet')
                    ->getCommande($idCommande);
                    
                $this->sendMail($commandeEnCours, $courriel);
            }
        }   
    }
}
