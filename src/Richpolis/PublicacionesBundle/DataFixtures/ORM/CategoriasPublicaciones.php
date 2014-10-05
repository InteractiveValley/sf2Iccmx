<?php

/*
 * 
 * Creado por Ricardo Alcantara <richpolis@gmail.com>
 *
 */

namespace Richpolis\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;

/**
 * Fixtures de la entidad CategoriaPublicacion.
 * Crea los tres tipos de categorias de las publicaciones.
 */
class CategoriasPublicaciones extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 20;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Crear las categorias de las publicaciones

        $categoria1 = new CategoriaPublicacion();
        $categoria1->setCategoria("Categoria 1");
        $categoria1->setPosition(1);
        
        $manager->persist($categoria1);

        $categoria2 = new CategoriaPublicacion();
        $categoria2->setCategoria("Categoria 2");
        $categoria2->setPosition(2);
        
        $manager->persist($categoria2);        
        
        $manager->flush();
        
    }

    
}