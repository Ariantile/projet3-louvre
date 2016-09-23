<?php

namespace Louvre\BilletterieBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class FacturationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomFacture', TextType::class, array(
                  'label'       => 'louvre.accueil.label.nom',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'class'       => 'nom')))
            ->add('prenomFacture', TextType::class, array(
                  'label'       => 'louvre.accueil.label.prenom',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'class'       => 'prenom')))
            ->add('pays', CountryType::class, array(
                  'label'       => 'louvre.accueil.label.pays',
                  'placeholder' => 'louvre.accueil.label.pays_place'))
            ->add('naissanceFacture', DateType::class, array(
                  'label'       => 'louvre.accueil.label.date_nais',
                  'widget'      => 'single_text',
                  'input'       => 'datetime',
                  'format'      => 'dd/MM/yyyy',
                  'attr'        => array(
                  'class'       => 'dateFacturation',
                  'placeholder' => 'louvre.accueil.choix.date_place',
                  'maxlength'   => '10'),))
            ->add('courriel', RepeatedType::class, array(
                  'type'            => EmailType::class,
                  'invalid_message' => 'louvre.accueil.courriel.error',
                  'required'        => true,
                  'first_options'   => array('label' => 'louvre.accueil.facturation.mail'),
                  'second_options'  => array('label' => 'louvre.accueil.facturation.mail_cf'),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Louvre\BilletterieBundle\Entity\Facturation'
        ));
    }
}
