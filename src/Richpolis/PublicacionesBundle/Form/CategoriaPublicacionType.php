<?php

namespace Richpolis\PublicacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;

class CategoriaPublicacionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categoria','text',array('label'=>'Categoria','attr'=>array(
                'class'=>'validate[required] form-control placeholder',
                'placeholder'=>'Categoria',
                'data-bind'=>'value: categoria'
             )))
            ->add('tipoCategoria','choice',array(
                'label'=>'Tipo',
                'empty_value'=>false,
                'read_only'=> true,
                'choices'=>  CategoriaPublicacion::getArrayTipoCategoria(),
                'preferred_choices'=>  CategoriaPublicacion::getPreferedTipoCategoria(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo de categoria',
                    'data-bind'=>'value: tipoCategoria'
                )))    
            ->add('parent','entity',array(
                'class'=> 'PublicacionesBundle:CategoriaPublicacion',
                'label'=>'Antecesor',
                'required'=>false,
                'property'=>'nivelCategoria',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.nivel=:nivel')
                        ->orderBy('u.position', 'ASC')
                        ->setParameter('nivel',0);
                },
                'attr'=>array(
                    'class'=>'form-control placeholder',
                    'placeholder'=>'Parent',
                    'data-bind'=>'value: parent',
                    )
                ))
            ->add('isActive',null,array('label'=>'Activo?','attr'=>array(
                'class'=>'checkbox-inline',
                'placeholder'=>'Es activo',
                'data-bind'=>'value: isActive'
             )))    
            ->add('position','hidden')
            ->add('contPublicaciones','hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_publicacionesbundle_categoriapublicacion';
    }
}
