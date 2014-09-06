<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\Aside;
use Richpolis\PublicacionesBundle\Form\AsideType;

/**
 * Aside controller.
 *
 * @Route("/backend/asides")
 */
class AsideController extends Controller
{

    /**
     * Lists all Aside entities.
     *
     * @Route("/", name="backend_asides")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicacionesBundle:Aside')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Aside entity.
     *
     * @Route("/", name="backend_asides_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:Aside:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Aside();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_asides_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Aside entity.
     *
     * @param Aside $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Aside $entity)
    {
        $form = $this->createForm(new AsideType(), $entity, array(
            'action' => $this->generateUrl('backend_asides_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Aside entity.
     *
     * @Route("/new", name="backend_asides_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Aside();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Aside entity.
     *
     * @Route("/{id}", name="backend_asides_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Aside')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aside entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Aside entity.
     *
     * @Route("/{id}/edit", name="backend_asides_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Aside')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aside entity.');
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
    * Creates a form to edit a Aside entity.
    *
    * @param Aside $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Aside $entity)
    {
        $form = $this->createForm(new AsideType(), $entity, array(
            'action' => $this->generateUrl('backend_asides_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Aside entity.
     *
     * @Route("/{id}", name="backend_asides_update")
     * @Method("PUT")
     * @Template("PublicacionesBundle:Aside:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Aside')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aside entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_asides_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Aside entity.
     *
     * @Route("/{id}", name="backend_asides_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:Aside')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Aside entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_asides'));
    }

    /**
     * Creates a form to delete a Aside entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_asides_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
