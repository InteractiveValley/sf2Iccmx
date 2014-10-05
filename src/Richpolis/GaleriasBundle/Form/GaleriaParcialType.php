<?php

namespace Richpolis\GaleriasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GaleriaParcialType extends AbstractType
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
            ->add('descripcion','textarea',array(
                'label'=>'Contenido/Link Pagina',
                'required'=>false,
                'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Descripcion o Link a pagina',
                    'data-bind'=>'value: titulo'
                 )))
             ->add('isActive','hidden')
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
        return 'galeria_parcial';
    }
}
