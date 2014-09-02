<?php

namespace Richpolis\PublicidadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Publicidad
 *
 * @ORM\Table(name="publicidad")
 * @ORM\Entity(repositoryClass="Richpolis\PublicidadBundle\Repository\PublicidadRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Publicidad
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
     * @ORM\Column(name="empresa", type="string", length=255, nullable=true)
     */
    private $empresa;
    
    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=255, nullable=false)
     */
    private $archivo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipo_archivo", type="integer")
     */
    private $tipoArchivo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipo_publicidad", type="integer")
     */
    private $tipoPublicidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vigencia", type="date", nullable=true)
     */
    private $vigencia;
    
    const TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO=1;
    const TIPO_PUBLICIDAD_ENCABEZADO_DERECHO=2;
    const TIPO_PUBLICIDAD_ASIDE_ARRIBA=3;
    const TIPO_PUBLICIDAD_ASIDE_ABAJO=4;
        
    static public $sTipoPublicidad=array(
        self::TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO=>'Supper Banner 728 X 90',
        self::TIPO_PUBLICIDAD_ENCABEZADO_DERECHO=>'Micro Banner 230 X 90',
        self::TIPO_PUBLICIDAD_ASIDE_ARRIBA=>'Box Banner 1 300 X 250',
        self::TIPO_PUBLICIDAD_ASIDE_ABAJO=>'Box Banner 2 300 X 250',
    );
    
	
    public function __construct() {
        $this->isActive = true;
        $this->vigencia = new \DateTime("+1 month");
        $this->tipoArchivo = RpsStms::TIPO_ARCHIVO_IMAGEN;  
	$this->tipoPublicidad = Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_DERECHO;
    }
    
    public function getStringTipoArchivo(){
        return RpsStms::$sTipoArchivo[$this->getTipoArchivo()];
    }

    static function getArrayTipoArchivos(){
        return RpsStms::$sTipoArchivo;
    }

    static function getPreferedTipoArchivo(){
        return array(RpsStms::$TIPO_ARCHIVO_IMAGEN);
    }

    public function getStringTipoPublicidad(){
        return self::$sTipoPublicidad[$this->getTipoPublicidad()];
    }

    static function getArrayTipoPublicidad(){
        return self::$sTipoPublicidad;
    }

    static function getPreferedTipoPublicidad(){
        return array(self::TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO);
    }
    
    
    public function isVigenciaCorrecta(){
        
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
     * Set archivo
     *
     * @param string $archivo
     * @return Publicidad
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * Get archivo
     *
     * @return string 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Publicidad
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
     * @return Publicidad
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Publicidad
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Publicidad
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set vigencia
     *
     * @param \DateTime $vigencia
     * @return Publicidad
     */
    public function setVigencia($vigencia)
    {
        $this->vigencia = $vigencia;

        return $this;
    }

    /**
     * Get vigencia
     *
     * @return \DateTime 
     */
    public function getVigencia()
    {
        return $this->vigencia;
    }

    /**
     * Set empresa
     *
     * @param string $empresa
     * @return Publicidad
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return string 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Publicidad
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set tipoArchivo
     *
     * @param integer $tipoArchivo
     * @return Publicidad
     */
    public function setTipoArchivo($tipoArchivo)
    {
        $this->tipoArchivo = $tipoArchivo;

        return $this;
    }

    /**
     * Get tipoArchivo
     *
     * @return integer 
     */
    public function getTipoArchivo()
    {
        return $this->tipoArchivo;
    }

    /**
     * Set tipoPublicidad
     *
     * @param integer $tipoPublicidad
     * @return Publicidad
     */
    public function setTipoPublicidad($tipoPublicidad)
    {
        $this->tipoPublicidad = $tipoPublicidad;

        return $this;
    }

    /**
     * Get tipoPublicidad
     *
     * @return integer 
     */
    public function getTipoPublicidad()
    {
        return $this->tipoPublicidad;
    }
    
    
    /*
     * Timestable
     */
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt()){
          $this->createdAt = new \DateTime();
        }
        if(!$this->getUpdatedAt()){
          $this->updatedAt = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }
	
    
    /*** uploads ***/
    
    public $file;
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->archivo)) {
            // store the old name to delete after the update
            $this->temp = $this->archivo;
            $this->archivo = null;
        } else {
            $this->archivo = 'initial';
        }
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preUpload()
    {
      if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->archivo = $filename.'.'.$this->getFile()->guessExtension();
            $this->setTipoArchivo(RpsStms::getTipoArchivo($this->getArchivo()));
        }
    }

    /**
    * @ORM\PostPersist
    * @ORM\PostUpdate
    */
    public function upload()
    {
      if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->archivo);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        
        if($this->getIsImagen()){
            $this->crearThumbnail($this->getWidth(),$this->getHeight());
        }
        
        $this->file = null;
    }
    
    
    
    /**
    * @ORM\PostRemove
    */
    public function removeUpload()
    {
      if ($file = $this->getAbsolutePath()) {
        if(file_exists($file)){
            unlink($file);
        }
      }
    }
    
    protected function getUploadDir()
    {
        return '/uploads/publicidad';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    public function getWebPath()
    {
        return null === $this->archivo ? null : $this->getUploadDir().'/'.$this->archivo;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->archivo ? null : $this->getUploadRootDir().'/'.$this->archivo;
    }
    
    public function getArchivoMiniView(){
        $opciones=array(
            'tipo_archivo'  => RpsStms::getTipoArchivo($this->getArchivo()),
            'path'      =>  $this->getWebPath(),
            'carpeta'   =>  'publicidad',
            'width'     =>  $this->getWidth()/2,
            'height'    =>  $this->getHeight()/2,
        );
        
        return RpsStms::getArchivoView($opciones);
    }
	
	public function getArchivoView(){
        $opciones=array(
            'tipo_archivo'  => RpsStms::getTipoArchivo($this->getArchivo()),
            'path'      =>  $this->getWebPath(),
            'carpeta'   =>  'publicidad',
            'width'     =>  $this->getWidth(),
            'height'    =>  $this->getHeight(),
        );
        
        return RpsStms::getArchivoView($opciones);
    }
    
    public function getWidth(){
        switch($this->getTipoPublicidad()){
            case Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO:
                return 738;
            case Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_DERECHO:
                return 230;
            case Publicidad::TIPO_PUBLICIDAD_ASIDE_ARRIBA:
            case Publicidad::TIPO_PUBLICIDAD_ASIDE_ABAJO:
                return 300;
        }
    }
    
    public function getHeight(){
        switch($this->getTipoPublicidad()){
            case Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO:
            case Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_DERECHO:
                return 90;
            case Publicidad::TIPO_PUBLICIDAD_ASIDE_ARRIBA:
            case Publicidad::TIPO_PUBLICIDAD_ASIDE_ABAJO:
                return 250;
        }
    }
    
    public function getIsImagen(){
        if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_IMAGEN)
            return true;
        else
            return false;
    }
    
    /*
     * Crea el thumbnail si por alguna razon no es del tamaño.
     * 
     * @return void
     */
    public function crearThumbnail($width=120,$height=100){
        $imagine 	= 	new \Imagine\Gd\Imagine();
        $mode		=  	\Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        $image     	= 	$imagine->open($this->getAbsolutePath());
        $size		=	new \Imagine\Image\Box($width,$height);
        $image->thumbnail($size,$mode)->save($this->getAbsolutePath());        
    }
}
