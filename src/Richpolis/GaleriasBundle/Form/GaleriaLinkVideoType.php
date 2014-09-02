<?php

namespace Richpolis\GaleriasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GaleriaLinkVideoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('archivo','url',array('label'=>'Link Video','attr'=>array(
                'class'=>'validate[required] form-control placeholder',
                'placeholder'=>'Link Video',
                'data-bind'=>'value: link video'
             )))
            ->add('isActive','hidden')
            ->add('titulo','hidden')
            ->add('descripcion','hidden')
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
