<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Entity\Usuario;

class NewsletterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username','hidden')
            ->add('password', 'hidden')
            ->add('nombre','text',array('label'=>'Nombre','attr'=>array(
                'class'=>'validate[required] form-control placeholder',
                'data-bind'=>'value: nombre'
             )))    
            ->add('email','email',array('label'=>'Email','attr'=>array(
                'class'=>'validate[required] form-control placeholder',
                'data-bind'=>'value: email'
             )))   
            ->add('twitter','hidden')
            ->add('facebook','hidden')  
            ->add('grupo','hidden')
            ->add('newsletter','hidden')
            ->add('salt','hidden')
            ->add('imagen','hidden')   
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /*$resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Usuario',
            'csrf_protection' => true
        ));*/
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Usuario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_backendbundle_newslettertype';
    }
}


