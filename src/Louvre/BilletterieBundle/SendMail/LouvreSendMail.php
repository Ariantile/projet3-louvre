<?php
// src/Louvre/BilletterieBundle/SendMail/LouvreSendMail.php

namespace Louvre\BilletterieBundle\SendMail;

class LouvreSendMail
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * Génération et envoi de mail
     *
     */
    public function sendMail($image, $idCommande, $commandeEnCours, $courriel)
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
}