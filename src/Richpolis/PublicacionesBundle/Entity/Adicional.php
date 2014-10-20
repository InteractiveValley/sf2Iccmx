<?php

namespace Richpolis\PublicacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Adicional
 *
 * @ORM\Table(name="adicionales")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Adicional
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @todo Titulo
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $titulo;
    
    /**
     * @var string
     * @todo Contenido adicional
     *
     * @ORM\Column(name="descripcion", type="text",nullable=false)
     * @Assert\NotBlank()
     */
    private $descripcion;

    /**
     * @var \Publicacion
     * @todo Publicacion del contenido adicional
     *
     * @ORM\ManyToOne(targetEntity="Publicacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publicacion_id", referencedColumnName="id")
     * })
     */
    private $publicacion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=150)
     */
    private $slug;
    
    /*
     * Slugable
     */
    
    /**
     * @ORM\PrePersist
     */
    public function setSlugAtValue()
    {
        $this->slug = RpsStms::slugify($this->getTitulo());
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Adicional
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Adicional
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Adicional
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set publicacion
     *
     * @param \Richpolis\PublicacionesBundle\Entity\Publicacion $publicacion
     * @return Adicional
     */
    public function setPublicacion(\Richpolis\PublicacionesBundle\Entity\Publicacion $publicacion = null)
    {
        $this->publicacion = $publicacion;

        return $this;
    }

    /**
     * Get publicacion
     *
     * @return \Richpolis\PublicacionesBundle\Entity\Publicacion 
     */
    public function getPublicacion()
    {
        return $this->publicacion;
    }
}
