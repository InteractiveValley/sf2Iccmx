<?php

namespace Richpolis\PaginasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Richpolis\PaginasBundle\Entity\Pagina;
use Richpolis\PaginasBundle\Form\PaginaType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

use Richpolis\BackendBundle\Utils\qqFileUploader;
use Richpolis\GaleriasBundle\Entity\Galeria;


/**
 * Pagina controller.
 *
 * @Route("/paginas")
 */
class PaginaController extends Controller
{

    /**
     * Lists all Pagina entities.
     *
     * @Route("/", name="paginas")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PaginasBundle:Pagina')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Pagina entity.
     *
     * @Route("/", name="paginas_create")
     * @Method("POST")
     * @Template("PaginasBundle:Pagina:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Pagina();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('paginas_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to create a Pagina entity.
    *
    * @param Pagina $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Pagina $entity)
    {
        $form = $this->createForm(new PaginaType(), $entity, array(
            'action' => $this->generateUrl('paginas_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pagina entity.
     *
     * @Route("/new", name="paginas_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pagina();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Pagina entity.
     *
     * @Route("/{id}", name="paginas_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PaginasBundle:Pagina')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pagina entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'get_galerias' =>$this->generateUrl('paginas_galerias',array('id'=>$entity->getId()),true),
            'post_galerias' =>$this->generateUrl('paginas_galerias_upload', array('id'=>$entity->getId()),true),
			'post_galerias_link_video' =>$this->generateUrl('paginas_galerias_link_video', array('id'=>$entity->getId()),true),
            'url_delete' => $this->generateUrl('paginas_galerias_delete',array('id'=>$entity->getId(),'idGaleria'=>'0'),true),
        );
    }

    /**
     * Displays a form to edit an existing Pagina entity.
     *
     * @Route("/{id}/edit", name="paginas_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PaginasBundle:Pagina')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pagina entity.');
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
    * Creates a form to edit a Pagina entity.
    *
    * @param Pagina $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pagina $entity)
    {
        $form = $this->createForm(new PaginaType(), $entity, array(
            'action' => $this->generateUrl('paginas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pagina entity.
     *
     * @Route("/{id}", name="paginas_update")
     * @Method("PUT")
     * @Template("PaginasBundle:Pagina:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PaginasBundle:Pagina')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pagina entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        //$editForm->submit($request->request->get($editForm->getName()),false);

        if ($editForm->isValid()) {
            $entity->preUpload();
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('paginas_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores'     => RpsStms::getErrorMessages($editForm)
        );
    }
    
    /**
     * Deletes a Pagina entity.
     *
     * @Route("/{id}", name="paginas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PaginasBundle:Pagina')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pagina entity.');
            }

            $em->remove($entity);
            $em->flush();
        //}

        return $this->redirect($this->generateUrl('paginas'));
    }

    /**
     * Creates a form to delete a Pagina entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paginas_delete', array('id' => $id)))
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
     * Lists all Pagina galerias entities.
     *
     * @Route("/{id}/galerias", name="paginas_galerias")
     * @Method("GET")
     */
    public function galeriasAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pagina = $em->getRepository('PaginasBundle:Pagina')->find($id);
        
        $galerias = $pagina->getGalerias();
        $get_galerias = $this->generateUrl('paginas_galerias',array('id'=>$pagina->getId()),true);
        $post_galerias = $this->generateUrl('paginas_galerias_upload', array('id'=>$pagina->getId()),true);
		$post_galerias_link_video = $this->generateUrl('paginas_galerias_link_video', array('id'=>$pagina->getId()),true);
        $url_delete = $this->generateUrl('paginas_galerias_delete',array('id'=>$pagina->getId(),'idGaleria'=>'0'),true);
        
        return $this->render('GaleriasBundle:Galeria:galerias.html.twig', array(
            'galerias'=>$galerias,
            'get_galerias' =>$get_galerias,
            'post_galerias' =>$post_galerias,
			'post_galerias_link_video' =>$post_galerias_link_video,
            'url_delete' => $url_delete,
        ));
    }
    
    /**
     * Crea una galeria de una pagina.
     *
     * @Route("/{id}/galerias", name="paginas_galerias_upload")
     * @Method("POST")
     */
    public function galeriasUploadAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $pagina=$em->getRepository('PaginasBundle:Pagina')->find($id);
       
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
                if($pagina->getPagina()=="clientes"){
                    $this->crearThumbnailClientes($registro);
                }else{
                    $registro->crearThumbnail();    
                }
                $pagina->getGalerias()->add($registro);
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
            $pagina->getGalerias()->add($registro);
            $em->flush();  
        }
        
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * Crea una galeria link video de una pagina.
     *
     * @Route("/{id}/galerias/link/video", name="paginas_galerias_link_video", requirements={"id" = "\d+"})
     * @Method({"POST","GET"})
     */
    public function galeriasLinkVideoAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $autobus=$em->getRepository('PaginasBundle:Pagina')->find($id);
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
     * Deletes una Galeria entity de una Pagina.
     *
     * @Route("/{id}/galerias/{idGaleria}", name="paginas_galerias_delete")
     * @Method("DELETE")
     */
    public function deleteGaleriaAction(Request $request, $id, $idGaleria)
    {
            $em = $this->getDoctrine()->getManager();
            $pagina = $em->getRepository('PaginasBundle:Pagina')->find($id);
            $galeria = $em->getRepository('GaleriasBundle:Galeria')->find(intval($idGaleria));

            if (!$pagina) {
                throw $this->createNotFoundException('Unable to find Pagina entity.');
            }
            
            $pagina->getGalerias()->removeElement($galeria);
            $em->remove($galeria);
            $em->flush();
        

        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        $response->setData(array("ok"=>true));
        return $response;
    }

    /*
     * Crea el thumbnail especifico para la pagina de clientes
     * 
     * @return void
     */
    public function crearThumbnailClientes(Galeria $galeria,$width=123,$height=123,$path=""){
        $imagine    = new \Imagine\Gd\Imagine();
        $collage    = $imagine->create(new \Imagine\Image\Box(123, 123));
        $mode       = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
        $image      = $imagine->open($galeria->getAbsolutePath());
        $sizeImage  = $image->getSize();
        if(strlen($path)==0){
            $path = $galeria->getAbosluteThumbnailPath();
        }
        if($height == null){
            $height = $sizeImage->getHeight();
            if($height>123){
                $height = 123;
            }
        }
        if($width == null){
            $width = $sizeImage->getWidth();
            if($width>123){
                $width = 123;
            }
        }
        $size   =new \Imagine\Image\Box($width,$height);
        $image->thumbnail($size,$mode)->save($path);
        $image = $imagine->open($path);
        $size = $image->getSize();
        if((123 - $size->getWidth())>1){
            $width = ceil((123 - $size->getWidth())/2);
        }else{
            $width = 0;
        }
        if((123 - $size->getHeight())>1){
            $height = ceil((123 - $size->getHeight())/2);
        }else{
            $height =0;
        }    
        $centrado = new \Imagine\Image\Point($width, $height);
        $collage->paste($image,$centrado);
        $collage->save($path);        
    }
    
}
