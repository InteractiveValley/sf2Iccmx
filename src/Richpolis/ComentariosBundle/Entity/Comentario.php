<?php

namespace Richpolis\ComentariosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Comentario
 *
 * @ORM\Table(name="comentarios")
 * @ORM\Entity(repositoryClass="Richpolis\ComentariosBundle\Repository\ComentarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comentario
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
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 6,
     *     max = 300
     * )
     */
    private $message;
    
    /**
     * @var boolean
     * @todo Si la noticia es inactiva no es visible para el usuario. 
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=255, nullable=true)
     */
    private $archivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipoArchivo", type="integer")
     */
    private $tipoArchivo;

    /**
     * @var \Richpolis\PublicacionesBundle\Entity\Publicacion
     * @todo Comentarios de las noticias
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\PublicacionesBundle\Entity\Publicacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publicacion_id", referencedColumnName="id")
     * })
     */
    private $publicacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer")
     */
    private $nivel;

    /**
     * @ORM\ManyToOne(targetEntity="Comentario", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="parent")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $children;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    const STATUS_SPAM = -1;
    const STATUS_PENDIENTE = 0;
    const STATUS_APROBADO = 1;
        
    static public $sStatus=array(
        self::STATUS_SPAM=>'Spam',
        self::STATUS_PENDIENTE=>'Para aprobar',
        self::STATUS_APROBADO=>'Aprobado'
    );
    
    public function __construct() {
        $this->status = Comentario::STATUS_PENDIENTE;
        $this->publicacion = new \Doctrine\Common\Collections\ArrayCollection();
		$this->nivel = 0;
		$this->tipoArchivo = RpsStms::TIPO_ARCHIVO_IMAGEN;
		
    }
    
    public function getStringStatus(){
        return self::$sStatus[$this->getStatus()];
    }

    static function getArrayStatus(){
        return self::$sStatus;
    }

    static function getPreferedStatus(){
        return array(self::STATUS_PENDIENTE);
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
    }
	
	/**
     * @ORM\PrePersist
     */
    public function setUsernameAtValue()
    {
        if(!$this->getUsername())
        {
          $this->username = "Anonimo";
        }

        if(!$this->getEmail())
        {
          $this->email = "anonimo@sinemail.com";
        }

        if(!$this->getArchivo())
        {
          $this->archivo = null;
        }        
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
            $this->setTipoArchivo(RpsStms::getTipoArchivo($this->archivo));
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
        return '/uploads/usuarios';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../html'.$this->getUploadDir();
    }
    
    public function getWebPath()
    {
        return null === $this->archivo ? null : $this->getUploadDir().'/'.$this->archivo;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->archivo ? null : $this->getUploadRootDir().'/'.$this->archivo;
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
     * Set username
     *
     * @param string $username
     * @return Comentario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Comentario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Comentario
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Comentario
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
     * Set archivo
     *
     * @param string $archivo
     * @return Comentario
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
     * @return Comentario
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
     * Set nivel
     *
     * @param integer $nivel
     * @return Comentario
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Comentario
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
     * Set publicacion
     *
     * @param \Richpolis\PublicacionesBundle\Entity\Publicacion $publicacion
     * @return Comentario
     */
    public function setPublicacion(\Richpolis\PublicacionesBundle\Entity\Publicacion $publicacion = null)
    {
        $this->publicacion = $publicacion;

        return $this;
    }

    /**
     * Get publicacion
     *
     * @return \Richpolis\PubliacionesBundle\Entity\Publicacion 
     */
    public function getPublicacion()
    {
        return $this->publicacion;
    }

    /**
     * Set parent
     *
     * @param \Richpolis\ComentariosBundle\Entity\Comentario $parent
     * @return Comentario
     */
    public function setParent(\Richpolis\ComentariosBundle\Entity\Comentario $parent = null)
    {
        $this->parent = $parent;
        
        $this->setNivel($parent->getNivel()+1);

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Richpolis\ComentariosBundle\Entity\Comentario 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Richpolis\ComentariosBundle\Entity\Comentario $children
     * @return Comentario
     */
    public function addChild(\Richpolis\ComentariosBundle\Entity\Comentario $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Richpolis\ComentariosBundle\Entity\Comentario $children
     */
    public function removeChild(\Richpolis\ComentariosBundle\Entity\Comentario $children)
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
}
