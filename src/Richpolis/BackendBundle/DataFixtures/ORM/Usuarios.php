<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 * 
 * Modificado por Ricardo Alcantara <richpolis@gmail.com>
 *
 */

namespace Richpolis\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Richpolis\BackendBundle\Entity\Usuario;

/**
 * Fixtures de la entidad Usuario.
 * Crea 500 usuarios de prueba con información muy realista.
 */
class Usuarios extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 10;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Obtener todas las ciudades de la base de datos
        
        // usuario Richpolis 
        $richpolis = new Usuario();
        
        $richpolis->setUsername('richpolis');
        $richpolis->setNombre("Ricardo Alcantara Gomez");
        $richpolis->setEmail('richpolis@gmail.com');
        $richpolis->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'sfR0xC4s';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($richpolis);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $richpolis->getSalt());
        $richpolis->setPassword($passwordCodificado);
        $richpolis->setGrupo(Usuario::GRUPO_SUPER_ADMIN);
        $richpolis->setTwitter('Richpolis');
        $richpolis->setFacebook('RICHPOLIS');
        $manager->persist($richpolis);
        
            
        // usuario Administrador
        $usuarioAdmin = new Usuario();
        
        $usuarioAdmin->setUsername('Admin');
        $usuarioAdmin->setNombre("Administrador general");
        $usuarioAdmin->setEmail('admin@heraldodetoluca.com');
        $usuarioAdmin->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'admin';
        $encorder = $this->container->get('security.encoder_factory')->getEncoder($usuarioAdmin);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $usuarioAdmin->getSalt());
        $usuarioAdmin->setPassword($passwordCodificado);
        $usuarioAdmin->setGrupo(Usuario::GRUPO_ADMIN);
        $usuarioAdmin->setTwitter('admin');
        $manager->persist($usuarioAdmin);
        
        // usuario Normal
        $usuarioNormal = new Usuario();
        
        $usuarioNormal->setUsername('Usuario1');
        $usuarioNormal->setNombre("Usuario 1");
        $usuarioNormal->setEmail('usuario1@heraldodetoluca.com');
        $usuarioNormal->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = '12345678';
        $encorder = $this->container->get('security.encoder_factory')->getEncoder($usuarioNormal);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $usuarioNormal->getSalt());
        $usuarioNormal->setPassword($passwordCodificado);
        $usuarioNormal->setGrupo(Usuario::GRUPO_USUARIOS);
        $manager->persist($usuarioNormal);
        $usuarioNormal->setFacebook("usuarioNormal");
        $manager->flush();
    }

    
}