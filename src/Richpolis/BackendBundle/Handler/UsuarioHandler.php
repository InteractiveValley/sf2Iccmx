<?php

namespace Richpolis\BackendBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Richpolis\BackendBundle\Entity\Usuario;
use Richpolis\BackendBundle\Form\UsuarioType;

use Richpolis\BackendBundle\Exception\InvalidFormException;

class UsuarioHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $encoderFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory,EncoderFactoryInterface $encoderFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Get a Usuario.
     *
     * @param mixed $id
     *
     * @return Usuario
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Usuarios.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Usuario.
     *
     * @param array $parameters
     *
     * @return Usuario
     */
    public function post(array $parameters)
    {
        $usuario = $this->createUsuario();

        return $this->processForm($usuario, $parameters, 'POST');
    }

    /**
     * Edit a Usuario.
     *
     * @param Usuario $usuario
     * @param array   $parameters
     *
     * @return Usuario
     */
    public function put(Usuario $usuario, array $parameters)
    {
        return $this->processForm($usuario, $parameters, 'PUT');
    }

    /**
     * Partially update a Usuario.
     *
     * @param Usuario $usuario
     * @param array   $parameters
     *
     * @return Usuario
     */
    public function patch(Usuario $usuario, array $parameters)
    {
        return $this->processForm($usuario, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param Usuario $usuario
     * @param array   $parameters
     * @param String  $method
     *
     * @return Usuario
     *
     * @throws \Richpoois\BackendBundle\Exception\InvalidFormException
     */
    private function processForm(Usuario $usuario, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new UsuarioType(), $usuario, array('method' => $method));
        
        if(isset($parameters['_method'])){
            unset($parameters['_method']);
        }
        
        if(isset($parameters['id'])){
            unset($parameters['id']);
        }
        
        if($method=="PUT" || $method == "PATCH"){
            $current_pass = $usuario->getPassword();
        }
        
        $form->submit($parameters, 'PATCH' !== $method);
        
        if ($form->isValid()) {

            $user = $form->getData();
            if(null == $user->getPassword() && ($method == "PUT" || $method == "PATCH")) {
                $user->setPassword($current_pass);
            }else{
                $this->setSecurePassword($user);
            }
            
            $this->om->persist($user);
            $this->om->flush($user);

            return $user;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createUsuario()
    {
        return new $this->entityClass();
    }
    
    private function setSecurePassword(&$entity) {
        // encoder
        $encoder = $this->encoderFactory->getEncoder($entity);
        $passwordCodificado = $encoder->encodePassword(
                    $entity->getPassword(),
                    $entity->getSalt()
        );
        $entity->setPassword($passwordCodificado);
    }
}
