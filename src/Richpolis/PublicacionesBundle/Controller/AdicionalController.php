<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\Adicional;
use Richpolis\PublicacionesBundle\Form\AdicionalType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Adicional controller.
 *
 * @Route("/adicionales")
 */
class AdicionalController extends Controller
{

    /**
     * Lists all Adicional entities.
     *
     * @Route("/", name="adicionales")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicacionesBundle:Adicional')->findBy(
        array(),
        array(
            'publicacion'=>'ASC'
        ));

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Creates a new Adicional entity.
     *
     * @Route("/", name="adicionales_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:Adicional:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Adicional();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('adicionales_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Creates a form to create a Adicional entity.
     *
     * @param Adicional $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Adicional $entity)
    {
        $form = $this->createForm(new AdicionalType(), $entity, array(
            'action' => $this->generateUrl('adicionales_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Adicional entity.
     *
     * @Route("/new", name="adicionales_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Adicional();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Adicional entity.
     *
     * @Route("/{id}", name="adicionales_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Adicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Adicional entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Adicional entity.
     *
     * @Route("/{id}/edit", name="adicionales_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Adicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Adicional entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to edit a Adicional entity.
    *
    * @param Adicional $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Adicional $entity)
    {
        $form = $this->createForm(new AdicionalType(), $entity, array(
            'action' => $this->generateUrl('adicionales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Adicional entity.
     *
     * @Route("/{id}", name="adicionales_update")
     * @Method("PUT")
     * @Template("PublicacionesBundle:Adicional:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Adicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Adicional entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('adicionales_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }
    /**
     * Deletes a Adicional entity.
     *
     * @Route("/{id}", name="adicionales_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:Adicional')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Adicional entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('adicionales'));
    }

    /**
     * Creates a form to delete a Adicional entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adicionales_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
