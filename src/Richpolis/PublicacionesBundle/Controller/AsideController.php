<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\Aside;
use Richpolis\PublicacionesBundle\Form\AsideType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

use Richpolis\BackendBundle\Utils\qqFileUploader;
use Richpolis\GaleriasBundle\Entity\Galeria;

/**
 * Aside controller.
 *
 * @Route("/asides")
 */
class AsideController extends Controller
{

    /**
     * Lists all Aside entities.
     *
     * @Route("/", name="asides")
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
     * @Route("/", name="asides_create")
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

            return $this->redirect($this->generateUrl('asides_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
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
            'action' => $this->generateUrl('asides_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Aside entity.
     *
     * @Route("/new", name="asides_new")
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
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Aside entity.
     *
     * @Route("/{id}", name="asides_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Aside')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aside entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'get_galerias' => $this->generateUrl('asides_galerias', array('id' => $entity->getId())),
            'post_galerias' => $this->generateUrl('asides_galerias_upload', array('id' => $entity->getId())),
            'post_galerias_link_video' => $this->generateUrl('asides_galerias_link_video', array('id' => $entity->getId())),
            'url_delete' => $this->generateUrl('asides_galerias_delete', array('id' => $entity->getId(), 'idGaleria' => '0')),
        );
    }

    /**
     * Displays a form to edit an existing Aside entity.
     *
     * @Route("/{id}/edit", name="asides_edit", requirements={"id" = "\d+"})
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
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm)
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
            'action' => $this->generateUrl('asides_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Aside entity.
     *
     * @Route("/{id}", name="asides_update", requirements={"id" = "\d+"})
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

            return $this->redirect($this->generateUrl('asides_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores'     => RpsStms::getErrorMessages($editForm)
        );
    }
    /**
     * Deletes a Aside entity.
     *
     * @Route("/{id}", name="asides_delete", requirements={"id" = "\d+"})
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

        return $this->redirect($this->generateUrl('asides'));
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
            ->setAction($this->generateUrl('asides_delete', array('id' => $id)))
            ->setMethod('DELETE')
            /*->add('submit', 'submit', array(
                'label' => 'Eliminar',
                'attr'=>array(
                    'class'=>'btn btn-danger'
            )))*/
            ->getForm()
        ;
    }
    
    /**
     * Lists all Aside galerias entities.
     *
     * @Route("/{id}/galerias", name="asides_galerias", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function galeriasAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $autobus = $em->getRepository('PublicacionesBundle:Aside')->find($id);
        
        $galerias = $autobus->getGalerias();
        $get_galerias = $this->generateUrl('asides_galerias',array('id'=>$autobus->getId()));
        $post_galerias = $this->generateUrl('asides_galerias_upload', array('id'=>$autobus->getId()));
		$post_galerias_link_video = $this->generateUrl('asides_galerias_link_video',array('id'=>$autobus->getId()));
        $url_delete = $this->generateUrl('asides_galerias_delete',array('id'=>$autobus->getId(),'idGaleria'=>'0'));
        
        return $this->render('GaleriasBundle:Galeria:galerias.html.twig', array(
            'galerias'=>$galerias,
            'get_galerias' =>$get_galerias,
            'post_galerias' =>$post_galerias,
			'post_galerias_link_video' =>$post_galerias_link_video,
            'url_delete' => $url_delete,
        ));
    }
    
    /**
     * Crea una galeria de una autobus.
     *
     * @Route("/{id}/galerias", name="asides_galerias_upload", requirements={"id" = "\d+"})
     * @Method("POST")
     */
    public function galeriasUploadAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $autobus=$em->getRepository('PublicacionesBundle:Aside')->find($id);
       
        if(!$request->request->has('tipoArchivo')){ 
            // list of valid extensions, ex. array("jpeg", "xml", "bmp")
            $allowedExtensions = array("jpeg","png","gif","jpg");
            // max file size in bytes
            $sizeLimit = 6 * 1024 * 1024;
            $uploader = new qqFileUploader($allowedExtensions, $sizeLimit,$request->server);
            $uploads= $this->container->getParameter('richpolis.uploads');
            $result = $uploader->handleUpload($uploads."/galerias/");
            // to pass data through iframe you will need to encode all html tags
            /*****************************************************************/
            //$file = $request->getParameter("qqfile");
            $max = $em->getRepository('GaleriasBundle:Galeria')->getMaxPosicion();
            if($max == null){
                $max=0;
            }
            if(isset($result["success"])){
                $registro = new Galeria();
                $registro->setArchivo($result["filename"]);
                $registro->setThumbnail($result["filename"]);
                $registro->setTitulo($result["titulo"]);
                $registro->setIsActive(true);
                $registro->setPosition($max+1);
                $registro->setTipoArchivo(RpsStms::TIPO_ARCHIVO_IMAGEN);
                //unset($result["filename"],$result['original'],$result['titulo'],$result['contenido']);
                $em->persist($registro);
                $registro->crearThumbnail();
                $autobus->getGalerias()->add($registro);
                $em->flush();
            }
        }else{
            $result = $request->request->all(); 
            $registro = new Galeria();
            $registro->setArchivo($result["archivo"]);
            $registro->setIsActive($result['isActive']);
            $registro->setPosition($result['position']);
            $registro->setTipoArchivo($result['tipoArchivo']);
            $em->persist($registro);
            $autobus->getGalerias()->add($registro);
            $em->flush();  
        }
        
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * Crea una galeria link video de una autobus.
     *
     * @Route("/{id}/galerias/link/video", name="asides_galerias_link_video", requirements={"id" = "\d+"})
     * @Method({"POST","GET"})
     */
    public function galeriasLinkVideoAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $autobus=$em->getRepository('PublicacionesBundle:Aside')->find($id);
        $parameters = $request->request->all();
      
        if(isset($parameters['archivo'])){ 
            $registro = new Galeria();
            $registro->setArchivo($parameters['archivo']);
            $registro->setIsActive($parameters['isActive']);
            $registro->setPosition($parameters['position']);
            $registro->setTipoArchivo($parameters['tipoArchivo']);
            $em->persist($registro);
            $autobus->getGalerias()->add($registro);
            $em->flush();  
        }
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($parameters);
        return $response;
    }
    
    /**
     * Deletes una Galeria entity de una Aside.
     *
     * @Route("/{id}/galerias/{idGaleria}", name="asides_galerias_delete", requirements={"id" = "\d+","idGaleria"="\d+"})
     * @Method("DELETE")
     */
    public function deleteGaleriaAction(Request $request, $id, $idGaleria)
    {
            $em = $this->getDoctrine()->getManager();
            $autobus = $em->getRepository('PublicacionesBundle:Aside')->find($id);
            $galeria = $em->getRepository('GaleriasBundle:Galeria')->find(intval($idGaleria));

            if (!$autobus) {
                throw $this->createNotFoundException('Unable to find Aside entity.');
            }
            
            $autobus->getGalerias()->removeElement($galeria);
            $em->remove($galeria);
            $em->flush();
        

        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData(array("ok"=>true));
        return $response;
    }
	
    /**
     * Ordenar las posiciones de los asides.
     *
     * @Route("/ordenar/registros", name="asides_ordenar")
     * @Method("PATCH")
     */
    public function ordenarRegistrosAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em = $this->getDoctrine()->getManager();
            $result['ok'] = true;
            foreach ($registro_order as $order => $id) {
                $registro = $em->getRepository('PublicacionesBundle:Aside')->find($id);
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

}