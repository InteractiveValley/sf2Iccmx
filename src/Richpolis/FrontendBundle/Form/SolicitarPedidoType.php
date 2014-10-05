<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SolicitarPedidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',array('label'=>'Nombre'))
            ->add('email','email')
            ->add('subject','hidden')
            ->add('telefono','text',array('label'=>'Telefono'))    
            ->add('empresa','text',array('label'=>'Empresa'))
            ->add('body','hidden')     
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Contacto'
        ));
    }

    public function getName()
    {
        return 'solicitar_pedido';
    }
}
