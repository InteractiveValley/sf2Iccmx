<?php

namespace Richpolis\PublicacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Publicacion
 *
 * @ORM\Table(name="publicaciones")
 * @ORM\Entity(repositoryClass="Richpolis\PublicacionesBundle\Repository\PublicacionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Publicacion
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
     * @todo Titulo de la noticia
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $titulo;
    
    /**
     * @var string
     * @todo Contenido de la noticia
     *
     * @ORM\Column(name="descripcion", type="text",nullable=false)
     * @Assert\NotBlank()
     */
    private $descripcion;
    
    /**
     * @var boolean
     * @todo Es para ponerla en la pagina de inicio como noticia principal
     *
     * @ORM\Column(name="is_principal", type="boolean")
     */
    private $isPrincipal;
    
    /**
     * @var boolean
     * @todo Agrega a la noticia en el carrusel de portada.
     *
     * @ORM\Column(name="is_carrusel", type="boolean")
     */
    private $isCarrusel;
    
    /**
     * @var string
     * @todo Para hacer busquedas.
     * 
     * @ORM\Column(name="tags", type="string", nullable=true)
     */
    private $tags;
    
    /**
     * @var string
     * @todo Portada de la noticia, si no existe se toma la primera imagen de las galerias.
     *
     * @ORM\Column(name="imagen", type="string", length=150, nullable=true)
     */
    private $imagen;
    
    /**
     * @var integer
     * @todo Posicion de la noticia entre el grupo de noticias.
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var integer
     * @todo Cuantas personas han leido esta publicacion.
     *
     * @ORM\Column(name="cont_visitas", type="integer")
     */
    private $contVisitas;

    /**
     * @var integer
     * @todo Cuantos comentarios tiene esta publicacion
     *
     * @ORM\Column(name="cont_comentarios", type="integer")
     */
    private $contComentarios;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="has_menu", type="boolean" )
     */
    private $hasMenu;

    /**
     * @var integer
     * @todo Estatus de la edicion de la publicacion. 
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * @var string
     * @todo Slug de la noticia
     *
     * @ORM\Column(name="slug", type="string", length=255,nullable=true)
     */
    private $slug;
    
    /**
     * @var Richpolis\BackendBundle\Entity\Usuario
     * @todo Autor de la noticia
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Usuario", inversedBy="publicaciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    
    /**
     * @var \CategoriaPublicacion
     * @todo Categoria de la noticia
     *
     * @ORM\ManyToOne(targetEntity="CategoriaPublicacion", inversedBy="publicaciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_publicacion_id", referencedColumnName="id")
     * })
     */
    private $categoria;

    /**
     * @var integer
     * @todo Galeria de la noticia. 
     *
     * @ORM\ManyToMany(targetEntity="Richpolis\GaleriasBundle\Entity\Galeria")
     * @ORM\JoinTable(name="publicaciones_galeria")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $galerias;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;
    
    /* para solo eventos */
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_event", type="date", nullable=true)
     */
    private $fechaEvento;
    
    /**
     * @var integer
     * @todo tipo de evento. 
     *
     * @ORM\Column(name="type_event", type="integer")
     */
    private $tipoEvento;
    
    /**
     * @var boolean
     * @todo Indica si un evento esta en inicio o no.
     *
     * @ORM\Column(name="in_inicio", type="boolean")
     */
    private $inInicio;
    
    /**
     * @var boolean
     * @todo Indica si un evento esta en patrocinio o no.
     *
     * @ORM\Column(name="in_patrocinio", type="boolean")
     */
    private $inPatrocinio;
    
    /**
     * @var string
     * @todo Direccion del evento
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccionEvento;
    

    const STATUS_BORRADO = -1;
    const STATUS_INCOMPLETO = 1;
    const STATUS_REVISAR = 2;
    const STATUS_APROBADO = 3;
    const STATUS_PUBLICADO = 4;
    const STATUS_CADUCADO = 5;
    
    const TIPO_EVENTO_TALLER = 1;
    const TIPO_EVENTO_SEMINARIO = 2;
    const TIPO_EVENTO_SESION = 3;
    const TIPO_EVENTO_DIPLOMADO = 4;
        
    static public $sStatus=array(
        self::STATUS_BORRADO=>'Borrado',
        self::STATUS_INCOMPLETO=>'En edición',
        self::STATUS_REVISAR=>'A revisión',
        self::STATUS_APROBADO=>'Validar',
        self::STATUS_PUBLICADO=>'Aprobado',
        self::STATUS_CADUCADO=>'Archivado',
    );
    
    static public $sTipoEvento=array(
        self::TIPO_EVENTO_TALLER=>'Taller',
        self::TIPO_EVENTO_SEMINARIO=>'Seminario',
        self::TIPO_EVENTO_SESION=>'Sesion',
        self::TIPO_EVENTO_DIPLOMADO=>'Diplomado',
    );
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->galerias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isCarrusel = false;
        $this->isPrincipal = false;
        $this->inInicio = false;
        $this->inPatrocinio = false;
        $this->hasMenu = true;
        $this->status = self::STATUS_INCOMPLETO;
        $this->contVisitas = 0;
        $this->contComentarios = 0;
    }
    
    public function getStringTipoEvento(){
        return self::$sTipoEvento[$this->getTipoEvento()];
    }

    static function getArrayTipoEvento(){
        return self::$sTipoEvento;
    }

    static function getPreferedTipoEvento(){
        return array(self::TIPO_EVENTO_TALLER);
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

      
    
    /*
     * Timestable
     */
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
        if(!$this->getUpdatedAt())
        {
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
    
    /*
     * Slugable
     */
    
    /*
     * Esta funcion es para slugar el valor. 
     */
    public function setSlugAtValue()
    {
        $this->slug = RpsStms::slugify($this->getTitulo());
    }

    /*** uploads ***/
    
    /**
     * @Assert\File(maxSize="2M",maxSizeMessage="El archivo es demasiado grande. El tamaño máximo permitido es {{ limit }}")
     */
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
        if (isset($this->imagen)) {
            // store the old name to delete after the update
            $this->temp = $this->imagen;
            $this->imagen = null;
        } else {
            $this->imagen = 'initial';
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
            $this->imagen = $filename.'.'.$this->getFile()->guessExtension();
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
        $this->getFile()->move($this->getUploadRootDir(), $this->imagen);

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
    
    /*
     * Crea el thumbnail y lo guarda en un carpeta dentro del webPath thumbnails
     * 
     * @return void
     */
    public function crearThumbnail($width=250,$height=200,$path=""){
        $imagine    = new \Imagine\Gd\Imagine();
        $collage    = $imagine->create(new \Imagine\Image\Box(250, 200));
        $mode       = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
        $image      = $imagine->open($this->getAbsolutePath());
        $sizeImage  = $image->getSize();
        if(strlen($path)==0){
            $path = $this->getAbosluteThumbnailPath();
        }
        if($height == null){
            $height = $sizeImage->getHeight();
            if($height>200){
                $height = 200;
            }
        }
        if($width == null){
            $width = $sizeImage->getWidth();
            if($width>250){
                $width = 250;
            }
        }
        $size   =new \Imagine\Image\Box($width,$height);
        $image->thumbnail($size,$mode)->save($path);
        $image      = $imagine->open($path);
        $size = $image->getSize();
        if((250 - $size->getWidth())>1){
            $width = ceil((250 - $size->getWidth())/2);
        }else{
            $width = 0;
        }
        if((200 - $size->getHeight())>1){
            $height = ceil((200 - $size->getHeight())/2);
        }else{
            $height =0;
        }    
        $centrado = new \Imagine\Image\Point($width, $height);
        $collage->paste($image,$centrado);
        $collage->save($path);        
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
    }
    
    protected function getUploadDir()
    {
        return '/uploads/publicaciones';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../html'.$this->getUploadDir();
    }
    
    public function getWebPath()
    {
        return null === $this->imagen ? null : $this->getUploadDir().'/'.$this->imagen;
    }
    
    public function getThumbnailWebPath()
    {
        if(!file_exists($this->getAbosluteThumbnailPath())){
            if(file_exists($this->getAbsolutePath())){
                $this->crearThumbnail();
            }
        }
        return null === $this->imagen ? null : $this->getUploadDir().'/thumbnails/'.$this->imagen;
        
    }
    
    public function getAbsolutePath()
    {
        return null === $this->imagen ? null : $this->getUploadRootDir().'/'.$this->imagen;
    }
    
    public function getAbosluteThumbnailPath(){
        return null === $this->imagen ? null : $this->getUploadRootDir().'/thumbnails/'.$this->imagen;
    }

    public function getDescripcionCorta($max = 250){
        return RpsStms::cut_string2(RpsStms::strip_html_tags($this->getDescripcion()),250);
    }
    

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Publicacion
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
     * @return Publicacion
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
     * Set isPrincipal
     *
     * @param boolean $isPrincipal
     * @return Publicacion
     */
    public function setIsPrincipal($isPrincipal)
    {
        $this->isPrincipal = $isPrincipal;

        return $this;
    }

    /**
     * Get isPrincipal
     *
     * @return boolean 
     */
    public function getIsPrincipal()
    {
        return $this->isPrincipal;
    }

    /**
     * Set isCarrusel
     *
     * @param boolean $isCarrusel
     * @return Publicacion
     */
    public function setIsCarrusel($isCarrusel)
    {
        $this->isCarrusel = $isCarrusel;

        return $this;
    }

    /**
     * Get isCarrusel
     *
     * @return boolean 
     */
    public function getIsCarrusel()
    {
        return $this->isCarrusel;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Publicacion
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Publicacion
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Publicacion
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
     * Set status
     *
     * @param integer $status
     * @return Publicacion
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Publicacion
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Publicacion
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
     * @return Publicacion
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
     * Set usuario
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuario
     * @return Publicacion
     */
    public function setUsuario(\Richpolis\BackendBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Richpolis\BackendBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set categoria
     *
     * @param \Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $categoria
     * @return Publicacion
     */
    public function setCategoria(\Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Add galerias
     *
     * @param \Richpolis\GaleriasBundle\Entity\Galeria $galerias
     * @return Publicacion
     */
    public function addGaleria(\Richpolis\GaleriasBundle\Entity\Galeria $galerias)
    {
        $this->galerias[] = $galerias;

        return $this;
    }

    /**
     * Remove galerias
     *
     * @param \Richpolis\GaleriasBundle\Entity\Galeria $galerias
     */
    public function removeGaleria(\Richpolis\GaleriasBundle\Entity\Galeria $galerias)
    {
        $this->galerias->removeElement($galerias);
    }

    /**
     * Get galerias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGalerias()
    {
        return $this->galerias;
    }

    /**
     * Set contVisitas
     *
     * @param integer $contVisitas
     * @return Publicacion
     */
    public function setContVisitas($contVisitas)
    {
        $this->contVisitas = $contVisitas;

        return $this;
    }

    /**
     * Get contVisitas
     *
     * @return integer 
     */
    public function getContVisitas()
    {
        return $this->contVisitas;
    }

    /**
     * Set contComentarios
     *
     * @param integer $contComentarios
     * @return Publicacion
     */
    public function setContComentarios($contComentarios)
    {
        $this->contComentarios = $contComentarios;

        return $this;
    }

    /**
     * Get contComentarios
     *
     * @return integer 
     */
    public function getContComentarios()
    {
        return $this->contComentarios;
    }

    /**
     * Set hasMenu
     *
     * @param boolean $hasMenu
     * @return Publicacion
     */
    public function setHasMenu($hasMenu)
    {
        $this->hasMenu = $hasMenu;

        return $this;
    }

    /**
     * Get hasMenu
     *
     * @return boolean 
     */
    public function getHasMenu()
    {
        return $this->hasMenu;
    }

    /**
     * Set fechaEvento
     *
     * @param \DateTime $fechaEvento
     * @return Publicacion
     */
    public function setFechaEvento($fechaEvento)
    {
        $this->fechaEvento = $fechaEvento;

        return $this;
    }

    /**
     * Get fechaEvento
     *
     * @return \DateTime 
     */
    public function getFechaEvento()
    {
        return $this->fechaEvento;
    }

    /**
     * Set tipoEvento
     *
     * @param integer $tipoEvento
     * @return Publicacion
     */
    public function setTipoEvento($tipoEvento)
    {
        $this->tipoEvento = $tipoEvento;

        return $this;
    }

    /**
     * Get tipoEvento
     *
     * @return integer 
     */
    public function getTipoEvento()
    {
        return $this->tipoEvento;
    }

    /**
     * Set inInicio
     *
     * @param boolean $inInicio
     * @return Publicacion
     */
    public function setInInicio($inInicio)
    {
        $this->inInicio = $inInicio;

        return $this;
    }

    /**
     * Get inInicio
     *
     * @return boolean 
     */
    public function getInInicio()
    {
        return $this->inInicio;
    }

    /**
     * Set inPatrocinio
     *
     * @param boolean $inPatrocinio
     * @return Publicacion
     */
    public function setInPatrocinio($inPatrocinio)
    {
        $this->inPatrocinio = $inPatrocinio;

        return $this;
    }

    /**
     * Get inPatrocinio
     *
     * @return boolean 
     */
    public function getInPatrocinio()
    {
        return $this->inPatrocinio;
    }

    /**
     * Set direccionEvento
     *
     * @param string $direccionEvento
     * @return Publicacion
     */
    public function setDireccionEvento($direccionEvento)
    {
        $this->direccionEvento = $direccionEvento;

        return $this;
    }

    /**
     * Get direccionEvento
     *
     * @return string 
     */
    public function getDireccionEvento()
    {
        return $this->direccionEvento;
    }
}
