<?php

namespace Richpolis\PublicidadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PublicidadType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('empresa','text',array(
                'label'=>'Empresa','required'=>true,'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Empresa',
                    'data-bind'=>'value: empresa'
                    )
                ))
            ->add('file','file',array('label'=>'Archivo','attr'=>array(
                'class'=>'form-control placeholder',
                'placeholder'=>'Archivo',
                'data-bind'=>'value: archivo'
             )))
            ->add('link','url',array(
                'label'=>'Link','required'=>false,'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Link de pagina (solo imagenes)',
                    'data-bind'=>'value: link'
                    )
                ))    
            ->add('isActive',null,array('label'=>'Activo?','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'Es activo',
                'data-bind'=>'value: isActive'
                )))
            ->add('archivo','hidden')
            ->add('position','hidden')
            ->add('tipoPublicidad','hidden')
            ->add('tipoArchivo','hidden')    
            ->add('vigencia')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicidadBundle\Entity\Publicidad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_publicidadbundle_publicidad';
    }
}
