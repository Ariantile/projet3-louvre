<?php

namespace Louvre\BilletterieBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Louvre\BilletterieBundle\Form\Type\FacturationType;
use Louvre\BilletterieBundle\Form\Type\BilletType;

class CommandeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', DateType::class , array(
                  'widget'       => 'single_text',
                  'input'        => 'datetime',
                  'format'       => 'dd/MM/yyyy',
                  'attr'         => array(
                  'class'        => 'dateReserv',
                  'maxlength'    => '10',
                  'readonly'     => 'true'),))
            ->add('demiJournee', ChoiceType::class, array(
                  'choices'      => array(
                  'louvre.accueil.choix.jour' => false,
                  'louvre.accueil.choix.demi' => true,),
                  'expanded'     => true,
                  'multiple'     => false,
                  'label'        => 'Type de billet'))
            ->add('qte',         IntegerType::class, array(
                  'label'        => false,
                  'attr'         => array(
                  'min'          => 0,
                  'max'          => 9,
                  'readonly'     => 'true')))
            ->add('facturation', FacturationType::class)
            ->add('billets', CollectionType::class, array(
                  'entry_type'   => BilletType::class,
                  'allow_add'    => true,
                  'allow_delete' => true
                  ))
            ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Louvre\BilletterieBundle\Entity\Commande'
        ));
    }
}
