<?php

namespace Richpolis\PublicacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;



/**
 * CategoriaPublicacion
 *
 * @ORM\Table(name="categorias_publicaciones")
 * @ORM\Entity(repositoryClass="Richpolis\PublicacionesBundle\Repository\CategoriaPublicacionRepository")
 * @ORM\HasLifecycleCallbacks()
 * 
 */
class CategoriaPublicacion
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
     *
     * @ORM\Column(name="categoria", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $categoria;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_categoria", type="integer")
     */
    private $tipoCategoria;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=150)
     */
    private $slug;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer")
     */
    private $nivel;
    
    /**
     * @ORM\ManyToOne(targetEntity="CategoriaPublicacion", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="CategoriaPublicacion", mappedBy="parent")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children;
        
    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="Publicacion",mappedBy="categoria")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $publicaciones;

    /**
     * @var integer
     *
     * @ORM\Column(name="cont_publicaciones", type="integer")
     */
    private $contPublicaciones;

    const TIPO_CATEGORIA_PUBLICACION=1;
    const TIPO_CATEGORIA_LLAMADOS=2;
    const TIPO_CATEGORIA_HERALDO_TV=3;
    const TIPO_CATEGORIA_TU_ESPACIO=4;
        
    static public $sTipoCategoria=array(
        self::TIPO_CATEGORIA_PUBLICACION=>'Noticia',
        self::TIPO_CATEGORIA_LLAMADOS=>'Noticia en llamados',
        self::TIPO_CATEGORIA_HERALDO_TV=>'Heraldo Tv',
        self::TIPO_CATEGORIA_TU_ESPACIO=>'Tu espacio',
    );
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->publicaciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
        $this->tipoCategoria = self::TIPO_CATEGORIA_PUBLICACION;
        $this->nivel=0;
        $this->contPublicaciones=0;
    }
    
    public function getStringTipoCategoria(){
        return self::$sTipoCategoria[$this->getTipoCategoria()];
    }

    static function getArrayTipoCategoria(){
        return self::$sTipoCategoria;
    }

    static function getPreferedTipoCategoria(){
        return array(self::TIPO_CATEGORIA_PUBLICACION);
    }
    
    public function __toString(){
        return $this->getCategoria();
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
     * Add publicaciones
     *
     * @param \Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones
     * @return Categoria
     */
    public function addPublicacione(\Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones)
    {
        $this->publicaciones[] = $publicaciones;

        return $this;
    }

    /**
     * Remove publicaciones
     *
     * @param \Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones
     */
    public function removePublicacione(\Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones)
    {
        $this->publicaciones->removeElement($publicaciones);
    }

    /**
     * Get publicaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPublicaciones()
    {
        return $this->publicaciones;
    }

    /*
     * Slugable
     */
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setSlugAtValue()
    {
        $this->slug = RpsStms::slugify($this->getCategoria());
    }

    
    

    /**
     * Set categoria
     *
     * @param string $categoria
     * @return CategoriaPublicacion
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return string 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return CategoriaPublicacion
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return CategoriaPublicacion
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return CategoriaPublicacion
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
     * Set nivel
     *
     * @param integer $nivel
     * @return CategoriaPublicacion
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    public function getNivelCategoria(){
        if($this->getNivel()>0){
            //return str_pad($this->getCategoria(), strlen($this->getCategoria())+$this->getNivel(), "-", STR_PAD_LEFT);
            return $this->getParent() . " > " . $this->getCategoria();
        }else{
            return $this->getCategoria();
        }   
    }
    
    public function getNivelCategoriaSinParent(){
        if($this->getNivel()>0){
            //return str_pad($this->getCategoria(), strlen($this->getCategoria())+$this->getNivel(), "-", STR_PAD_LEFT);
            return " > " . $this->getCategoria();
        }else{
            return $this->getCategoria();
        }   
    }

    /**
     * Add children
     *
     * @param \Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $children
     * @return CategoriaPublicacion
     */
    public function addChild(\Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $children
     */
    public function removeChild(\Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $parent
     * @return CategoriaPublicacion
     */
    public function setParent(\Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $parent = null)
    {
        $this->parent = $parent;

        if(null !== $parent ){
            $this->setNivel($parent->getNivel()+1);
        }else{
            $this->setNivel();
        }

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set tipoCategoria
     *
     * @param integer $tipoCategoria
     * @return CategoriaPublicacion
     */
    public function setTipoCategoria($tipoCategoria)
    {
        $this->tipoCategoria = $tipoCategoria;

        return $this;
    }

    /**
     * Get tipoCategoria
     *
     * @return integer 
     */
    public function getTipoCategoria()
    {
        return $this->tipoCategoria;
    }

    /**
     * Set contPublicaciones
     *
     * @param integer $contPublicaciones
     * @return CategoriaPublicacion
     */
    public function setContPublicaciones($contPublicaciones)
    {
        $this->contPublicaciones = $contPublicaciones;

        return $this;
    }

    /**
     * Get contPublicaciones
     *
     * @return integer 
     */
    public function getContPublicaciones()
    {
        return $this->contPublicaciones;
    }
}
