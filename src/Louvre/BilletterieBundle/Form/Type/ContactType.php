<?php

namespace Louvre\BilletterieBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
    
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
                  'label'       => 'louvre.accueil.label.nom',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'required'    => true,
                  'class'       => 'nom')))
            ->add('titre', TextType::class, array(
                  'label'       => 'louvre.contact.label.titre',
                  'attr'        => array(
                  'maxlength'   => '150',
                  'required'    => true,
                  'class'       => 'nom')))
            ->add('message', TextareaType::class, array(
                  'label'       => 'louvre.contact.label.message',
                  'attr'        => array(
                  'maxlength'   => '500',
                  'required'    => true,
                  'class'       => 'nom',
                  'rows'        => '5')))
            ->add('email', EmailType::class, array(
                  'label'       => 'louvre.accueil.facturation.mail',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'required'    => true,
                  'class'       => 'mail')))
            ->add('recaptcha', EWZRecaptchaType::class)
            ->add('Envoyer',      SubmitType::class, array(
                  'label'       => 'louvre.contact.submit'
            ));
    }
}
