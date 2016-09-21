<?php
namespace Louvre\BilletterieBundle\Entity;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

class Recherche
{
    public $courriel;
    
    /**
     * @Recaptcha\IsTrue
     */
    public $recaptcha;
    
    /**
     * Get $courriel
     *
     */
    public function getCourriel()
    {
        return $this->courriel;
    }
}