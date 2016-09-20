<?php

namespace Louvre\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    
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
            ->add('recherche',      SubmitType::class, array(
                  'label'       => 'louvre.recover.submit'
            ));
    }
}
