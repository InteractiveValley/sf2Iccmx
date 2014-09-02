<?php

namespace Richpolis\PublicidadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicidadBundle\Entity\Publicidad;
use Richpolis\PublicidadBundle\Form\PublicidadType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Publicidad controller.
 *
 * @Route("/publicidad")
 */
class PublicidadController extends Controller
{
    
    private $tipos = null;

    protected function getFilters() {
        return $this->get('session')->get('filters', array());
    }

    protected function setFilters($filtros) {
        $this->get('session')->set('filters', $filtros);
    }

    protected function getTipoPublicidadDefault() {
        $filters = $this->getFilters();
        $tipoPublicidad = null;
        if (isset($filters['tipoPublicidad'])) {
            $this->tipos = Publicidad::getArrayTipoPublicidad();
            foreach ($this->tipos as $key => $value) {
                if ($key == $filters['tipoPublicidad']) {
                    $tipoPublicidad = $key;
                    break;
                }
            }
        } else {
            $this->tipos = Publicidad::getArrayTipoPublicidad();
			$keys = array_keys($this->tipos);
            $this->setFilters(array('tipoPublicidad' => $keys[0]));
            $tipoPublicidad = $keys[0];
        }
        return $tipoPublicidad;
    }

    
    /**
     * Lists all Publicidad entities.
     *
     * @Route("/", name="publicidad")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tipoPublicidad = $this->getTipoPublicidadDefault();
        $entities = $em->getRepository('PublicidadBundle:Publicidad')
                        ->getPublicidadPorTipo($tipoPublicidad);

        return array(
            'entities' => $entities,
            'tipos'=> Publicidad::getArrayTipoPublicidad(),
            'tipo_publicidad' => $tipoPublicidad,
        );
    }
    
    /**
     * Lists all Publicidad entities for tipo de publicidad.
     *
     * @Route("/tipo/{tipoPublicidad}", name="publicidad_por_tipo",requirements={"tipoPublicidad" = "\d+"})
     * @Method("GET")
     * @Template("PublicidadBundle:Publicidad:index.html.twig")
     */
    public function tipoPublicidadAction($tipoPublicidad) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicidadBundle:Publicidad')
                        ->getPublicidadPorTipo($tipoPublicidad);

        $filters = $this->getFilters();
        $filters['tipoPublicidad'] = $tipoPublicidad;
        $this->setFilters($filters);

        return array(
            'entities' => $entities,
            'tipos'=> Publicidad::getArrayTipoPublicidad(),
            'tipo_publicidad' => $tipoPublicidad,
        );
    }
    
    /**
     * Get list tipos de publicidad.
     *
     * @Route("/ul", name="publicidad_lista_ul")
     * @Method("GET")
     * @Template("PublicidadBundle:Publicidad:ul.html.twig")
     */
    public function ulAction() {
       
        return array(
            'tipos' => Publicidad::getArrayTipoPublicidad(),
        );
    }
    
    /**
     * Creates a new Publicidad entity.
     *
     * @Route("/", name="publicidad_create")
     * @Method("POST")
     * @Template("PublicidadBundle:Publicidad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Publicidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('publicidad_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to create a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'action' => $this->generateUrl('publicidad_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Publicidad entity.
     *
     * @Route("/new", name="publicidad_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Publicidad();
		
        $max = $this->getDoctrine()->getRepository('PublicidadBundle:Publicidad')
                ->getMaxPosicion();
        
		$tipoPublicidad = $this->getTipoPublicidadDefault();
        
		if (!is_null($max)) {
            $entity->setPosition($max + 1);
        } else {
            $entity->setPosition(1);
        }
		
        $entity->setTipoPublicidad($tipoPublicidad);    
		
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Publicidad entity.
     *
     * @Route("/{id}", name="publicidad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Publicidad entity.
     *
     * @Route("/{id}/edit", name="publicidad_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'    => $entity,
            'form'   	=> $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm)
        );
    }

    /**
    * Creates a form to edit a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'action' => $this->generateUrl('publicidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Publicidad entity.
     *
     * @Route("/{id}", name="publicidad_update")
     * @Method("PUT")
     * @Template("PublicidadBundle:Publicidad:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('publicidad_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
	    'errores' => RpsStms::getErrorMessages($editForm)
        );
    }
    /**
     * Deletes a Publicidad entity.
     *
     * @Route("/{id}", name="publicidad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Publicidad entity.');
            }

            $em->remove($entity);
            $em->flush();
        //}

        return $this->redirect($this->generateUrl('publicidad'));
    }

    /**
     * Creates a form to delete a Publicidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('publicidad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
	
	/**
     * Ordenar las posiciones de los autobuses.
     *
     * @Route("/ordenar/registros", name="publicidad_ordenar")
     * @Method("PATCH")
     */
    public function ordenarRegistrosAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em = $this->getDoctrine()->getManager();
            $result['ok'] = true;
            foreach ($registro_order as $order => $id) {
                $registro = $em->getRepository('PublicidadBundle:Publicidad')->find($id);
                if ($registro->getPosition() != ($order + 1)) {
                    try {
                        $registro->setPosition($order + 1);
                        $em->flush();
                    } catch (Exception $e) {
                        $result['mensaje'] = $e->getMessage();
                        $result['ok'] = false;
                    }
                }
            }

            $response = new \Symfony\Component\HttpFoundation\JsonResponse();
            $response->setData($result);
            return $response;
        } else {
            $response = new \Symfony\Component\HttpFoundation\JsonResponse();
            $response->setData(array('ok' => false));
            return $response;
        }
    }
    
    /**
     * Activa o inactiva si una publicidad.
     *
     * @Route("/{id}/is/active", name="publicidad_is_active", requirements={"id" = "\d+"})
     * @Method("PATCH")
     */
    public function isActiveAction($id) 
    {
        $em = $this->getDoctrine()->getManager();
        $publicidad = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$publicidad) {
            throw $this->createNotFoundException('Unable to find publicidad entity.');
        }
        $publicidad->setIsActive(!$publicidad->getIsActive());
        $em->flush();
        
        return $this->render("PublicidadBundle:Publicidad:item.html.twig",array(
            'entity'=>$publicidad
        ));
    }
}
