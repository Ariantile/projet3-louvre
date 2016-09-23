<?php

namespace Louvre\BilletterieBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
    
class RechercheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('courriel', EmailType::class, array(
                  'label'       => 'louvre.recover.label',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'required'    => true,
                  'class'       => 'mail')))
            ->add('recaptcha', EWZRecaptchaType::class)
            ->add('recherche',      SubmitType::class, array(
                  'label'       => 'louvre.recover.submit'
            ));
    }
}
