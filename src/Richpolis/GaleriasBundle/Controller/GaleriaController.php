<?php

namespace Richpolis\GaleriasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Richpolis\GaleriasBundle\Entity\Galeria;
use Richpolis\GaleriasBundle\Form\GaleriaType;
use Richpolis\GaleriasBundle\Form\GaleriaLinkVideoType;
use Richpolis\GaleriasBundle\Form\GaleriaParcialType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Galeria controller.
 *
 * @Route("/galerias")
 */
class GaleriaController extends Controller
{
	
	
    protected function getFilters() {
        return $this->get('session')->get('galerias', array());
    }

    protected function setFilters($filtros) {
        $this->get('session')->set('galerias', $filtros);
    }	
	

    /**
     * Lists all Galeria entities.
     *
     * @Route("/", name="galerias")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GaleriasBundle:Galeria')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Galeria entity.
     *
     * @Route("/", name="galerias_create")
     * @Method("POST")
     * @Template("GaleriasBundle:Galeria:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Galeria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('galerias_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Galeria entity.
    *
    * @param Galeria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Galeria $entity, $tipo = "imagen")
    {
        if($tipo == "imagen"){
            $form = $this->createForm(new GaleriaType(), $entity, array(
                'action' => $this->generateUrl('galerias_create'),
                'method' => 'POST',
             ));
        }else if($tipo=="link_video"){
            $form = $this->createForm(new GaleriaLinkVideoType(), $entity, array(
                'action' => $this->generateUrl('galerias_create'),
                'method' => 'POST',
            ));
        }

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Galeria entity.
     *
     * @Route("/new", name="galerias_new")
     * @Method("GET")
     */
    public function newAction(Request $request)
    {
        $entity = new Galeria();
        $tipo = "imagen";
        $return = $this->generateUrl('galerias');
        if($request->query->has('tipo')){
            $tipo = $request->query->get('tipo');
            if($tipo == "link_video"){
                $entity->setTipoArchivo(RpsStms::TIPO_ARCHIVO_LINK);
            }
        }
        
        $em = $this->getDoctrine()->getManager();
        $max = $em->getRepository('GaleriasBundle:Galeria')->getMaxPosicion();
        if($max == null){
            $max=0;
        }
        $entity->setPosition($max+1);
        $form   = $this->createCreateForm($entity, $tipo);
        
        if($request->isXmlHttpRequest()){
            return $this->render("GaleriasBundle:Galeria:form.html.twig",array(
               'form' => $form->createView() 
            ));
        }else{
            return $this->render('GaleriasBundle:Galeria:new.html.twig' ,array(
                'entity' => $entity,
                'form'   => $form->createView(),
		'tipo'   => $tipo,
                'return' =>$return
            ));
        }
    }

    /**
     * Finds and displays a Galeria entity.
     *
     * @Route("/{id}", name="galerias_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GaleriasBundle:Galeria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galeria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Galeria entity.
     *
     * @Route("/{id}/edit", name="galerias_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GaleriasBundle:Galeria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galeria entity.');
        }
        
        $tipo = $request->query->get('tipo','imagen');
        
        $editForm = $this->createEditForm($entity,$tipo);
        $deleteForm = $this->createDeleteForm($id);
        
        if($request->isXmlHttpRequest()){
            return $this->render("GaleriasBundle:Galeria:form.html.twig",array(
               'form' => $editForm->createView() 
            ));
        }else{
            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            );
        }
    }

    /**
    * Creates a form to edit a Galeria entity.
    *
    * @param Galeria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Galeria $entity, $tipo="imagen")
    {
        if($tipo == "imagen"){
            $form = $this->createForm(new GaleriaType(), $entity, array(
                'action' => $this->generateUrl('galerias_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            ));
        }else if($tipo== "link_video"){
            $form = $this->createForm(new GaleriaLinkVideoType(), $entity, array(
                'action' => $this->generateUrl('galerias_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            ));
        }else if($tipo=="parcial"){
            $form = $this->createForm(new GaleriaParcialType(), $entity, array(
                'action' => $this->generateUrl('galerias_actualizar', array('id' => $entity->getId())),
                'method' => 'PATCH',
            ));
        }

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing Galeria entity.
     *
     * @Route("/{id}", name="galerias_update")
     * @Method("PUT")
     * @Template("GaleriasBundle:Galeria:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GaleriasBundle:Galeria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galeria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('galerias_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Edits an existing Galeria entity.
     *
     * @Route("/{id}", name="galerias_actualizar")
     * @Method("PATCH")
     */
    public function actualizarGaleriaAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GaleriasBundle:Galeria')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galeria entity.');
        }
        $editForm = $this->createEditForm($entity,"parcial");
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
           $em->flush();
           $response = new JsonResponse();
           $response->setData(array("ok"=>true));
           return $response;
        }
        $response = new JsonResponse();
        $response->setData(array("ok"=>false));
        return $response;
    }
    
    /**
     * Deletes a Galeria entity.
     *
     * @Route("/{id}", name="galerias_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GaleriasBundle:Galeria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Galeria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('galerias'));
    }

    /**
     * Creates a form to delete a Galeria entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('galerias_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Creates a new Galeria entity.
     *
     * @Route("/ordenar", name="galerias_ordenar")
     * @Method("POST")
     */
    public function ordenarRegistrosAction()
    {
        $request=$this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em=$this->getDoctrine()->getManager();
            $result['ok']=true;
            foreach($registro_order as $order=>$id)
            {
                $registro=$em->getRepository('GaleriasBundle:Galeria')->find($id);
                if($registro->getPosition()!=($order+1)){
                    try{
                        $registro->setPosition($order+1);
                        $em->flush();
                    }catch(Exception $e){
                        $result['mensaje']=$e->getMessage();
                    }    
                }
            }
            $response = new JsonResponse();
            $response->setData($result);
            return $response;
        }
        else {
            $response = new JsonResponse();
            $response->setData(array('ok'=>false));
            return $response;
        }
    }
}