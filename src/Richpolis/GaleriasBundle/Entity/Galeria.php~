<?php

namespace Richpolis\GaleriasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use JMS\Serializer\Annotation as Serializer;

/**
 * Galeria
 *
 * @ORM\Table(name="galerias")
 * @ORM\Entity(repositoryClass="Richpolis\GaleriasBundle\Repository\GaleriaRepository")
 * @ORM\HasLifecycleCallbacks()
 * 
 * @Serializer\ExclusionPolicy("all")
 */
class Galeria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Serializer\Expose
     * @Serializer\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255,nullable=true)
     * 
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255,nullable=true)
     * 
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="string", length=255)
     * 
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $thumbnail;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=255)
     * 
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $archivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_archivo", type="integer")
     * 
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("tipoArchivo")
     */
    private $tipoArchivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     * 
     * @Serializer\Expose
     * @Serializer\Type("integer")
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     * 
     * @Serializer\Expose
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("isActive")
     */
    private $isActive;
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("stringTipo")
     * 
     */
    public function getStringTipoArchivo(){
        return RpsStms::$sTipoArchivo[$this->getTipoArchivo()];
    }

    static function getArrayTipoArchivos(){
        return RpsStms::$sTipoArchivos;
    }

    static function getPreferedTipoArchivo(){
        return array(RpsStms::TIPO_ARCHIVO_IMAGEN);    
    }

    /* Constructor */
    public function __construct(){
        $this->isActive = true;
    }
    
    public function __toString(){
        return $this->getTitulo();
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
     * @return Galeria
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
     * @return Galeria
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
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Galeria
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string 
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set archivo
     *
     * @param string $archivo
     * @return Galeria
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
     * Set tipoArchivo
     *
     * @param integer $tipoArchivo
     * @return Galeria
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
     * Set position
     *
     * @param integer $position
     * @return Galeria
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
     * @return Galeria
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
     * Regresa el titulo corto segun el maximo de caracteres solicitado
     * 
     * @return string
     * 
     */
    
    public function getTituloCorto($max=15){
        if($this->titulo)
            return substr($this->getTitulo(), 0, $max);
        else
            return "Sin titulo";
    }
    
    /*
     * Crea el thumbnail y lo guarda en un carpeta dentro del webPath thumbnails
     * 
     * @return void
     */
    public function crearThumbnail($width=120,$height=100,$path=""){
        $this->thumbnail=$this->archivo;
        $imagine= new \Imagine\Gd\Imagine();
        $mode= \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        $image     = $imagine->open($this->getAbsolutePath());
        $sizeImage = $image->getSize();
        if(strlen($path)==0){
            $path = $this->getAbosluteThumbnailPath();
        }
        if($height == null){
            $height = $sizeImage->getHeight();
            if($height>369){
                $height = 369;
            }
        }
        if($width == null){
            $width = $sizeImage->getWidth();
            if($width>638){
                $width = 638;
            }
        }
        $size=new \Imagine\Image\Box($width,$height);
        $image->thumbnail($size,$mode)->save($path);        
    }
    
    
    /*
     * Para guardar videos de youtube o vimeo
     * 
     */
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preSaveGaleria()
    {
      if($this->getTipoArchivo()== RpsStms::TIPO_ARCHIVO_LINK){
        $infoVideo=  RpsStms::getTitleAndImageVideoYoutube($this->getArchivo());
        $this->setThumbnail($infoVideo['thumbnail']);
        $this->setArchivo($infoVideo['urlVideo']);
        $this->setTitulo($infoVideo['title']);
        $this->setDescripcion($infoVideo['description']);
      }
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
            $this->thumbnail = $this->archivo;
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
        
        $this->crearThumbnail();
        
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
      if($thumbnail=$this->getAbosluteThumbnailPath()){
         if(file_exists($thumbnail)){
            unlink($thumbnail);
        }
      }
      
      if($medium=$this->getAbosluteMediumPath()){
         if(file_exists($medium)){
            unlink($medium);
        }
      }
      
      if($large=$this->getAbosluteLargePath()){
         if(file_exists($large)){
            unlink($large);
        }
      }
      
    }
    
    protected function getUploadDir()
    {
        return '/uploads/galerias';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    protected function getThumbnailRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir().'/thumbnails';
    }
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("webPath")
     * 
     */
    public function getWebPath()
    {
        if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_IMAGEN){
            return null === $this->archivo ? null : $this->getUploadDir().'/'.$this->archivo;
        }else if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_LINK){
            return $this->getArchivo();
        }
    }
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("thumbnailWebPath")
     * 
     */
    public function getThumbnailWebPath()
    {
        if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_IMAGEN){
            if(!file_exists($this->getAbosluteThumbnailPath())){
                if(file_exists($this->getAbsolutePath())){
                    $this->crearThumbnail();
                }
            }
            return null === $this->thumbnail ? null : $this->getUploadDir().'/thumbnails/'.$this->thumbnail;
        }else if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_LINK){
            return $this->getThumbnail();
        }
    }
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("mediumWebPath")
     * 
     */
    public function getMediumWebPath()
    {
        if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_IMAGEN){
            if(!file_exists($this->getAbosluteMediumPath())){
                if(file_exists($this->getAbsolutePath())){
                    $this->crearThumbnail(300,null,$this->getAbosluteMediumPath());
                }
            }
            return null === $this->thumbnail ? null : $this->getUploadDir().'/thumbnails/medium/'.$this->thumbnail;
        }else if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_LINK){
            return $this->getThumbnail();
        }
    }
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("largeWebPath")
     * 
     */
    public function getLargeWebPath()
    {
        if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_IMAGEN){
            if(!file_exists($this->getAbosluteLargePath())){
                if(file_exists($this->getAbsolutePath())){
                    $this->crearThumbnail(638,369,$this->getAbosluteLargePath());
                }
            }
            return null === $this->thumbnail ? null : $this->getUploadDir().'/thumbnails/large/'.$this->thumbnail;
        }else if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_LINK){
            return $this->getThumbnail();
        }
    }
    
    public function getAbsolutePath()
    {
        return null === $this->archivo ? null : $this->getUploadRootDir().'/'.$this->archivo;
    }
    
    public function getAbosluteThumbnailPath(){
        return null === $this->thumbnail ? null : $this->getUploadRootDir().'/thumbnails/'.$this->thumbnail;
    }
    
    public function getAbosluteMediumPath(){
        return null === $this->thumbnail ? null : $this->getUploadRootDir().'/thumbnails/medium/'.$this->thumbnail;
    }
    
    public function getAbosluteLargePath(){
        return null === $this->thumbnail ? null : $this->getUploadRootDir().'/thumbnails/large/'.$this->thumbnail;
    }
    
    /* nota: Actualizar para los nuevos thumbnails
     */
    public function actualizaThumbnail()
    {
      if($thumbnail=$this->getAbosluteThumbnailPath()){
         if(file_exists($thumbnail)){
            unlink($thumbnail);
         }
      }
      $this->crearThumbnail();
    }
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("archivoView")
     * 
     */
    public function getArchivoView(){
        $opciones=array(
            'tipo_archivo'  => RpsStms::getTipoArchivo($this->getArchivo()),
            'path'      =>  $this->getLargeWebPath(),
            'carpeta'   =>  'galerias',
            'width'     =>  638,
            'height'    =>  369,
        );
        
        return RpsStms::getArchivoView($opciones);
    }
    
    public function getWidth(){
    
        
    }
    public function getHeight(){
    
        
    }
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("isImagen")
     * 
     */
    public function getIsImagen(){
        if($this->getTipoArchivo()==RpsStms::TIPO_ARCHIVO_IMAGEN)
            return true;
        else
            return false;
    }
}
