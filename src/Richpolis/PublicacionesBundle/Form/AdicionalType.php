<?php

namespace Richpolis\PublicacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdicionalType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo','text',array(
                'label'=>'Titulo','required'=>true,'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Titulo',
                    'data-bind'=>'value: titulo'
                    )
                ))
            ->add('descripcion',null,array(
                'label'=>'Contenido',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('slug','hidden')
            ->add('publicacion',null,array(
                'label'=>'Publicacion',
                'required'=>true,
                'read_only'=>false,                
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Publicacion',
                    'data-bind'=>'value: publicacion',
                    )
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicacionesBundle\Entity\Adicional'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_publicacionesbundle_adicional';
    }
}
