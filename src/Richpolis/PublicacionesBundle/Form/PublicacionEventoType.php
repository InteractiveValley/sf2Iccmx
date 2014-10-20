<?php

namespace Richpolis\PublicacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Richpolis\PublicacionesBundle\Entity\Publicacion;

class PublicacionEventoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo','text',array(
                'label'=>'Evento','required'=>true,'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Evento',
                    'data-bind'=>'value: evento'
                    )
                ))    
            ->add('descripcion',null,array(
                'label'=>'Descripcion',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('fechaEvento',null,array('label'=>'Fecha evento','attr'=>array(
                'class'=>'form-control '
             )))
            ->add('tipoEvento','choice',array(
                'label'=>'Tipo evento',
                'empty_value'=>false,
                'read_only'=> false,
                'choices'=>  Publicacion::getArrayTipoEvento(),
                'preferred_choices'=>  Publicacion::getPreferedTipoEvento(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo de evento',
                    'data-bind'=>'value: tipoEvento'
                )))
            ->add('inInicio',null,array('label'=>'En inicio?','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'En inicio',
                'data-bind'=>'value: inInicio'
             )))
            ->add('inPatrocinio',null,array('label'=>'En patrocinio?','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'En patrocinio',
                'data-bind'=>'value: inPatrocinio'
             )))
            ->add('inComisionesTrabajo',null,array('label'=>'En comisiones de trabajo','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'En comisiones de trabajo',
                'data-bind'=>'value: inComisionesTrabajo'
             )))
            ->add('direccionEvento','text',array(
                'label'=>'Localidad','required'=>true,'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Localidad',
                    'data-bind'=>'value: localidad'
                    )
                ))     
            ->add('categoria','entity',array(
                'class'=> 'PublicacionesBundle:CategoriaPublicacion',
                'label'=>'Categoria',
                'required'=>true,
                'read_only'=>true,
                'property'=>'nivelCategoria',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.parent', 'ASC')
                        ->addOrderBy('u.position','ASC');
                },
                'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Categoria',
                    'data-bind'=>'value: categoria',
                    )
                ))
            ->add('usuario',null,array(
                'label'=>'Usuario',
                'required'=>true,
                'read_only'=>true,                
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Usuario',
                    'data-bind'=>'value: usuario',
                    )
                ))
            ->add('file','file',array('label'=>'Portada','attr'=>array(
                'class'=>'form-control placeholder',
                'placeholder'=>'Portada',
                'data-bind'=>'value: portada'
             )))
            ->add('hasMenu',null,array('label'=>'Con menu?','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'Con menu',
                'data-bind'=>'value: hasMenu'
             )))            
            ->add('imagen','hidden')
            ->add('position','hidden')
            ->add('slug','hidden')
            ->add('status','hidden')
            ->add('contVisitas','hidden')
            ->add('contComentarios','hidden')
            ->add('descripcionCorta','hidden')
            //->add('galerias','hidden')    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicacionesBundle\Entity\Publicacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_publicacionesbundle_evento';
    }
}
