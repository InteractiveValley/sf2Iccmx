<?php

namespace Richpolis\BackendBundle\Twig;

class StatusComentarioExtension extends \Twig_Extension
{
    const STATUS_SPAM = -1;
    const STATUS_PENDIENTE = 0;
    const STATUS_APROBADO = 1;
    
    public function getFilters()
    {
        return array(
            'status_comentario' => new \Twig_Filter_Method($this, 'statusComentarioFilter'),
        );
    }

    public function statusComentarioFilter($status)
    {
        switch($status){
            case self::STATUS_SPAM:      $resp = "<span class='label label-danger status-borrado'>Borrado</span>"; break;
            case self::STATUS_PENDIENTE:   $resp = "<span class='label label-info status-pendiente'>Por revisar</span>"; break;
            case self::STATUS_APROBADO:    $resp = "<span class='label label-success status-publicado'>Revisado</span>"; break;
        }
        return $resp;
    }

    public function getName()
    {
        return 'status_comentario_extension';
    }
}