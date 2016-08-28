<?php

namespace Louvre\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class BilletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomBillet', TextType::class)
            ->add('prenomBillet', TextType::class)
            ->add('paysBillet', CountryType::class)
            ->add('naissanceBillet', 'date', array(
                  'widget'      => 'single_text',
                  'input'       => 'datetime',
                  'format'      => 'dd/MM/yyyy',
                  'attr'        => array(
                  'class'       => 'date',
                  'placeholder' => 'jj/mm/aaaa'),))
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
