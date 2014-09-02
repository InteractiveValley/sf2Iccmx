<?php
namespace Richpolis\BackendBundle\Twig;

class TwitterTimeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'twitter_time' => new \Twig_Filter_Method($this, 'twitterTimeFilter'),
        );
    }

    public function twitterTimeFilter($datetime)
    {
        $datetime->setTimezone(new \DateTimeZone('America/Mexico_City'));
        $fecha=$datetime->format('Y-m-d H:i:s');
        $resultado=  \Richpolis\BackendBundle\Utils\Richsys::twitter_time($fecha);
        return $resultado;
    }

    public function getName()
    {
        return 'twitter_time_extension';
    }
}