<?php
// src/Blogger/BlogBundle/Entity/Enquiry.php

namespace Richpolis\FrontendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Contacto
{
    /**
     * @Assert\NotBlank(message="El nombre no puede estar vacio.")
     */
    protected $name;

    /**
     * @Assert\Email(message="Email no es valido.")
     * @Assert\NotBlank(message="El email no puede estar vacio.")
     */
    protected $email;


    protected $subject;

    
    protected $telefono;
    
    /**
     * @Assert\Length(
     *     min=3,
     *     minMessage="El mensaje debe tener como minimo {{ limit }} caracteres."
     * )
     * @Assert\NotBlank(message="El mensaje no puede estar vacio.")
     */
    protected $body;
    
    

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    
    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
    
    
}