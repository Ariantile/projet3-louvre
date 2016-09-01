<?php

namespace Louvre\BilletterieBundle\Form;

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
            ->add('nomFacture', TextType::class)
            ->add('prenomFacture', TextType::class)
            ->add('pays', CountryType::class)
            ->add('naissanceFacture', 'date', array(
                  'widget'      => 'single_text',
                  'input'       => 'datetime',
                  'format'      => 'dd/MM/yyyy',
                  'attr'        => array(
                  'class'       => 'dateFacturation',
                  'placeholder' => 'jj/mm/aaaa'),))
            ->add('courriel', RepeatedType::class, array(
                  'type'            => EmailType::class,
                  'invalid_message' => 'Les deux adresses courriel doivent être identiques.',
                  'required'        => true,
                  'first_options'   => array('label' => 'Courriel'),
                  'second_options'  => array('label' => 'Confirmation courriel'),
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
