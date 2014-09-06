<?php

namespace Richpolis\PublicacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormatoInscripcion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FormatoInscripcion
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
     * @ORM\Column(name="pdf", type="string", length=255)
     */
    private $pdf;

    /**
     * @var string
     *
     * @ORM\Column(name="imagenHeader", type="string", length=255)
     */
    private $imagenHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="precios", type="text")
     */
    private $precios;

    /**
     * @var string
     *
     * @ORM\Column(name="formato", type="string", length=255)
     */
    private $formato;

    /**
     * @var string
     *
     * @ORM\Column(name="emailEnvio", type="string", length=255)
     */
    private $emailEnvio;


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
     * Set pdf
     *
     * @param string $pdf
     * @return FormatoInscripcion
     */
    public function setPdf($pdf)
    {
        $this->pdf = $pdf;

        return $this;
    }

    /**
     * Get pdf
     *
     * @return string 
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Set imagenHeader
     *
     * @param string $imagenHeader
     * @return FormatoInscripcion
     */
    public function setImagenHeader($imagenHeader)
    {
        $this->imagenHeader = $imagenHeader;

        return $this;
    }

    /**
     * Get imagenHeader
     *
     * @return string 
     */
    public function getImagenHeader()
    {
        return $this->imagenHeader;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return FormatoInscripcion
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
     * Set precios
     *
     * @param string $precios
     * @return FormatoInscripcion
     */
    public function setPrecios($precios)
    {
        $this->precios = $precios;

        return $this;
    }

    /**
     * Get precios
     *
     * @return string 
     */
    public function getPrecios()
    {
        return $this->precios;
    }

    /**
     * Set formato
     *
     * @param string $formato
     * @return FormatoInscripcion
     */
    public function setFormato($formato)
    {
        $this->formato = $formato;

        return $this;
    }

    /**
     * Get formato
     *
     * @return string 
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * Set emailEnvio
     *
     * @param string $emailEnvio
     * @return FormatoInscripcion
     */
    public function setEmailEnvio($emailEnvio)
    {
        $this->emailEnvio = $emailEnvio;

        return $this;
    }

    /**
     * Get emailEnvio
     *
     * @return string 
     */
    public function getEmailEnvio()
    {
        return $this->emailEnvio;
    }
}
