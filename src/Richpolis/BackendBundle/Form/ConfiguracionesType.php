<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Richpolis\BackendBundle\Entity\Configuraciones;

class ConfiguracionesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('configuracion','text',array(
                'label'=>'Configuracion',
                'required'=>true,
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Configuracion',
                    'data-bind'=>'value: configuracion'
             )))
            ->add('file','file',array(
                'label'=>'Archivo',
                'required'=>false,
            ))
            ->add('archivo','text',array('label'=>'String'))
            ->add('tipoArchivo','choice',array(
                'label'=>'Tipo',
                'empty_value'=>false,
                'choices'=>Configuraciones::getArrayTipoArchivo(),
                'preferred_choices'=>Configuraciones::getPreferedTipoArchivo()
                ))
            ->add('texto','genemu_tinymce',array(
                    'attr'=>array('cols' => 50,'rows' => 10,))
                 )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Configuraciones'
        ));
    }

    public function getName()
    {
        return 'richpolis_backendbundle_configuracionestype';
    }
}
