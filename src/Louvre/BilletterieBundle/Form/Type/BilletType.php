<?php

namespace Louvre\BilletterieBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
    
class BilletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomBillet', TextType::class, array(
                  'label'       => 'louvre.accueil.label.nom',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'class'       => 'nom')))
            ->add('prenomBillet', TextType::class, array(
                  'label'       => 'louvre.accueil.label.prenom',
                  'attr'        => array(
                  'maxlength'   => '30',
                  'class'       => 'prenom')))
            ->add('paysBillet', CountryType::class, array(
                  'label'       => 'louvre.accueil.label.pays',
                  'placeholder' => 'louvre.accueil.label.pays_place'))
            ->add('naissanceBillet', DateType::class, array(
                  'widget'      => 'single_text',
                  'input'       => 'datetime',
                  'format'      => 'dd/MM/yyyy',
                  'label'       => 'louvre.accueil.label.date_nais',
                  'attr'        => array(
                  'class'       => 'dateBillets',
                  'placeholder' => 'louvre.accueil.choix.date_place',
                  'maxlength'   => '10'),))
            ->add('tarifReduit', CheckboxType::class, array (
                  'mapped'      => false,
                  'required'    => false,
                  'label'       => 'louvre.accueil.choix.tarif_red',))
            ->add('prixBillet', NumberType::class, array(
                  'label'       => false,
                  'scale'       => 2,
                  'attr'        => array (
                  'class'       => 'hprtr',
                  'readonly'    => 'true')))
            ->add('tarifBillet', TextType::class, array(
                  'label'       => false,
                  'attr'        => array (
                  'class'       => 'hprtr',
                  'readonly'    => 'true')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Louvre\BilletterieBundle\Entity\Billet'
        ));
    }
}
