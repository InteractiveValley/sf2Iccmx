<?php

namespace Richpolis\GaleriasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GaleriaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo','text',array('label'=>'Titulo','attr'=>array(
                'class'=>'validate[required] form-control placeholder',
                'placeholder'=>'Titulo',
                'data-bind'=>'value: titulo'
             )))
            ->add('descripcion',null,array(
                'label'=>'Descripcion',
                'required'=>false,
                'attr'=>array(
                    'class'=>'cleditor form-control placeholder',
                    )
                ))
            ->add('file','file',array('label'=>'Archivo'))    
            ->add('isActive',null,array('label'=>'Activo?','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'Es activo',
                'data-bind'=>'value: isActive'
                )))
            ->add('archivo','hidden')
            ->add('tipoArchivo','hidden')    
            ->add('position','hidden')    
            ->add('thumbnail','hidden')
                
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\GaleriasBundle\Entity\Galeria',
            'csrf_protection' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'galeria';
    }
}
