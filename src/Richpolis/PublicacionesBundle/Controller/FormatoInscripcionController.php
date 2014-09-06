<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\FormatoInscripcion;
use Richpolis\PublicacionesBundle\Form\FormatoInscripcionType;

/**
 * FormatoInscripcion controller.
 *
 * @Route("/backend/formatos")
 */
class FormatoInscripcionController extends Controller
{

    /**
     * Lists all FormatoInscripcion entities.
     *
     * @Route("/", name="backend_formatos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicacionesBundle:FormatoInscripcion')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new FormatoInscripcion entity.
     *
     * @Route("/", name="backend_formatos_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:FormatoInscripcion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new FormatoInscripcion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_formatos_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a FormatoInscripcion entity.
     *
     * @param FormatoInscripcion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FormatoInscripcion $entity)
    {
        $form = $this->createForm(new FormatoInscripcionType(), $entity, array(
            'action' => $this->generateUrl('backend_formatos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FormatoInscripcion entity.
     *
     * @Route("/new", name="backend_formatos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new FormatoInscripcion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a FormatoInscripcion entity.
     *
     * @Route("/{id}", name="backend_formatos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:FormatoInscripcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FormatoInscripcion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FormatoInscripcion entity.
     *
     * @Route("/{id}/edit", name="backend_formatos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:FormatoInscripcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FormatoInscripcion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a FormatoInscripcion entity.
    *
    * @param FormatoInscripcion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FormatoInscripcion $entity)
    {
        $form = $this->createForm(new FormatoInscripcionType(), $entity, array(
            'action' => $this->generateUrl('backend_formatos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FormatoInscripcion entity.
     *
     * @Route("/{id}", name="backend_formatos_update")
     * @Method("PUT")
     * @Template("PublicacionesBundle:FormatoInscripcion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:FormatoInscripcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FormatoInscripcion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_formatos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a FormatoInscripcion entity.
     *
     * @Route("/{id}", name="backend_formatos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:FormatoInscripcion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FormatoInscripcion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_formatos'));
    }

    /**
     * Creates a form to delete a FormatoInscripcion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_formatos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
