<?php

namespace Louvre\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Louvre\BilletterieBundle\Form\FacturationType;
use Louvre\BilletterieBundle\Form\BilletType;

class CommandeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', 'date', array(
                  'widget'       => 'single_text',
                  'input'        => 'datetime',
                  'format'       => 'dd/MM/yyyy',
                  'attr'         => array(
                  'class'        => 'dateReserv',
                  'placeholder'  => 'jj/mm/aaaa'),))
            ->add('demiJournee', ChoiceType::class, array(
                  'choices'      => array(
                  'Journée'      => false,
                  'Demi-journée' => true,),
                  'expanded'     => true,
                  'multiple'     => false,
                  'label'        => 'Type de billet'))
            ->add('qte',         IntegerType::class, array(
                  'disabled'     => true,
                  'attr' =>      array(
                      'min' => 0,
                      'max' => 9,)
                  ))
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
