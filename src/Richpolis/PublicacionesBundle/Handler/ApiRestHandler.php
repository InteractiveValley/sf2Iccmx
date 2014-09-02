<?php

namespace Richpolis\PublicacionesBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

use Richpolis\PublicacionesBundle\Entity\Categoria;
use Richpolis\PublicacionesBundle\Form\CategoriaType;
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Form\PublicacionType;
use Richpolis\PublicacionesBundle\Entity\Galeria;
use Richpolis\PublicacionesBundle\Form\GaleriaType;



use Richpolis\BackendBundle\Exception\InvalidFormException;

class ApiRestHandler
{
    private $om;
    private $entityClass;
    private $entityFormClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, $entityFormClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->entityFormClass = $entityFormClass;
        $this->formFactory = $formFactory;
    }
    
    /**
     * Get a EntityClass sin objetos.
     *
     * @param mixed $id
     *
     * @return entityClass
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }
    
    /**
     * Get a EntityClass con objetos.
     *
     * @param mixed $id
     *
     * @return entityClass
     */
    public function get($id)
    {
        return $this->repository->findConObjetos($id);
    }
    
    /**
     * Get a EntityClass.
     *
     * @param mixed $id
     *
     * @return entityClass
     */
    public function getForSlug($slug)
    {
        return $this->repository->findForSlugConObjetos($slug);
    }

    /**
     * Get a list of EntityClasss.
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
     * Get a list of EntityClasss.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function findBy($where = array(),$order = array(),$limit = 5, $offset = 0)
    {
        return $this->repository->findBy($where, $order, $limit, $offset);
    }
    
    /**
     * Create a new EntityClass.
     *
     * @param array $parameters
     *
     * @return EntityClass
     */
    public function post(array $parameters)
    {
        $entity = $this->createEntity();

        return $this->processForm($entity, $parameters, 'POST');
    }

    /**
     * Edit a EntityClass.
     *
     * @param EntityClass $entity
     * @param array   $parameters
     *
     * @return EntityClass
     */
    public function put($entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PUT');
    }

    /**
     * Partially update a EntityClass.
     *
     * @param EntityClass $entity
     * @param array   $parameters
     *
     * @return EntityClass
     */
    public function patch($entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param entityClass $entity
     * @param array   $parameters
     * @param String  $method
     *
     * @return entityClass
     *
     * @throws \Richpoois\BackendBundle\Exception\InvalidFormException
     */
    private function processForm($entity, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create($this->createEntityForm(), $entity, array('method' => $method));
        
        if(isset($parameters['_method'])){
            unset($parameters['_method']);
        }
        
        if(isset($parameters['id'])){
            unset($parameters['id']);
        }
        
        $form->submit($parameters, 'PATCH' !== $method);
        
        if ($form->isValid()) {

            $entityData = $form->getData();
            $this->om->persist($entityData);
            $this->om->flush($entityData);

            return $entityData;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createEntity()
    {
        return new $this->entityClass();
    }
    
    private function createEntityForm()
    {
        return new $this->entityFormClass();
    }
}
