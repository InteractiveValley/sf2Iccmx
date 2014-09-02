<?php

namespace Richpolis\PaginasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaginaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pagina','text',array('label'=>'Pagina','attr'=>array(
                'class'=>'validate[required] form-control placeholder',
                'placeholder'=>'Pagina',
                'data-bind'=>'value: pagina'
             )))
            ->add('contenido',null,array(
                'label'=>'Contenido',
                'required'=>true,
                'attr'=>array(
                    'class'=>'tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('file','file',array('label'=>'Imagen','attr'=>array(
                'class'=>'form-control placeholder',
                'placeholder'=>'Imagen pagina',
                'data-bind'=>'value: imagen pagina'
             )))  
            ->add('imagen','hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PaginasBundle\Entity\Pagina'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_paginasbundle_pagina';
    }
}
