<?php

namespace Richpolis\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Configuraciones
 *
 * @ORM\Table(name="configuraciones")
 * @ORM\Entity(repositoryClass="Richpolis\BackendBundle\Repository\ConfiguracionesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Configuraciones
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
     * @ORM\Column(name="configuracion", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $configuracion;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_archivo", type="integer", length=1)
     * @Assert\NotBlank()
     */
    private $tipoConfiguracion;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="text",nullable=true)
     */
    private $texto;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;    

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255,nullable=true)
     */
    private $slug;

    static public $IMAGEN=1;
    static public $LINK_VIDEO=2;
    static public $TEXTO_LARGO=3;
    static public $TEXTO_CORTO=4;
    
    
    
    static private $sCategorias=array(
        1=>'Imagen',
        2=>'Link_video',
        3=>'Texto largo',
        4=>'Texto corto',
    );
    
    public function getStringTipoConfiguracion(){
        return self::$sCategorias[$this->getTipoConfiguracion()];
    }

    static function getArrayTipoConfiguracion(){
        return self::$sCategorias;
    }

    static function getPreferedTipoConfiguracion(){
        return array(self::$TEXTO_CORTO);
    }
    
    public function __construct() {
        $this->isActive = true;
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
     * Set configuracion
     *
     * @param string $configuracion
     * @return Configuraciones
     */
    public function setConfiguracion($configuracion)
    {
        $this->configuracion = $configuracion;
        
        return $this;
    }

    /**
     * Get configuracion
     *
     * @return string 
     */
    public function getConfiguracion()
    {
        return $this->configuracion;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return Configuraciones
     */
    public function setTipoConfiguracion($tipo)
    {
        $this->tipoConfiguracion = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipoConfiguracion()
    {
        return $this->tipoConfiguracion;
    }
    
    /**
     * Set texto
     *
     * @param string $texto
     * @return Configuraciones
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    
        return $this;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Configuraciones
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
    
    /*
     * Slugable
     */
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setSlugAtValue()
    {
        $this->slug = \Richpolis\BackendBundle\Utils\Richsys::slugify($this->getConfiguracion());
    }
    
    
    /**
    ** @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preSaveLinkVideo()
    {
      if ($this->getTipoConfiguracion()==self::$LINK_VIDEO) {
        $infoVideo=  \Richpolis\BackendBundle\Utils\Richsys::getTitleAndImageVideoYoutube($this->getTexto());
        $this->setTexto($infoVideo['urlVideo']);
      }
    }
    
    /*
     * uploads file
     */
    
    public $file;
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preUpload()
    {
      if (null !== $this->file) {
        // do whatever you want to generate a unique name
        $this->texto = uniqid().'.'.$this->file->guessExtension();
      }
    }

    /**
    * @ORM\PostPersist
    * @ORM\PostUpdate
    */
    public function upload()
    {
      if (null === $this->file) {
        return;
      }

      // if there is an error when moving the file, an exception will
      // be automatically thrown by move(). This will properly prevent
      // the entity from being persisted to the database on error
      $this->file->move($this->getUploadRootDir(), $this->texto);

      
      unset($this->file);
    }

    /**
    * @ORM\PostRemove
    */
    public function removeUpload()
    {
      if($this->getTipoConfiguracion()==self::$IMAGEN){  
        if ($file = $this->getAbsolutePath()) {
          if(file_exists($file)){
              unlink($file);
          }
        }
      }
    }
    
    protected function getUploadDir()
    {
        return '/uploads/configuraciones';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    protected function getThumbnailRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir().'/thumbnails';
    }
        
    public function getWebPath()
    {
        return null === $this->texto ? null : $this->getUploadDir().'/'.$this->texto;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->texto ? null : $this->getUploadRootDir().'/'.$this->texto;
    }
    
    public function isImagen(){
        return $this->getTipoConfiguracion() == Configuraciones::$IMAGEN;
    }
    
    public function isLinkVideo(){
        return $this->getTipoConfiguracion() == Configuraciones::$LINK_VIDEO;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Configuraciones
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
}
