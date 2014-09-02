<?php

// Rest Full Api Publicaciones
namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Richpolis\BackendBundle\Exception\InvalidFormException;
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Form\PublicacionType;
use Richpolis\BackendBundle\Utils\qqFileUploader;
use Richpolis\PublicacionesBundle\Entity\Galeria;
use Richpolis\PublicacionesBundle\Entity\ResultGaleria;
use Richpolis\PublicacionesBundle\Entity\PublicacionCollection;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class PublicacionesController extends FOSRestController
{
    
    
    /**
     * List all publicaciones.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing publicaciones.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many publicaciones to return por pagina($limit).")
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:index.html.twig",
     *  templateVar = "publicaciones"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getPublicacionsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        //$handler = $this->container->get('apirest.publicacion.handler');
        //return $handler->all($limit, $offset);
        $entities = $this->getDoctrine()->getRepository('PublicacionesBundle:Publicacion')
                ->getPublicacionesActivas(0,"",true,true);
        return $entities;
        
    }
    
    /**
     * find publicaciones.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\QueryParam(name="categoria", default="portada", description="Categoria que se busca, slug o id, si se omite, se busca portada por default.")
     * @Annotations\QueryParam(name="campo_order", default="createdAt", description="Campo por el cual se ordenan los registros.")
     * @Annotations\QueryParam(name="order", default="DESC", description="Orden ASC o DESC.")
     * @Annotations\QueryParam(name="page", requirements="\d+", nullable=true, description="Page from which to start listing publicaciones.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="4", description="How many publicaciones to return.")
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:index.html.twig",
     *  templateVar = "publicaciones"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getPublicacionsCategoriasAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $page =       $paramFetcher->get('page');
        $page =       null == $page ? 1 : $page;
        $limit =        $paramFetcher->get('limit');
        $categoria =    $paramFetcher->get('categoria');
        $campo_order =  $paramFetcher->get('campo_order');
        $order =        $paramFetcher->get('order');
        
        $em = $this->getDoctrine()->getManager();
        
        if($categoria == "portada"){
            $query = $em->getRepository('PublicacionesBundle:Publicacion')
                    ->getQueryPortada();
        }elseif(!is_null($categoria)){
            $query = $em->getRepository('PublicacionesBundle:Publicacion')
                    ->getQueryPublicaciones(false,true,$categoria,$campo_order,$order);
        }else{
            return array();
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query,$page,$limit);
        $data = $pagination->getPaginationData();
        $publicaciones = $pagination->getItems();
        
        //return $pagination;
        return new PublicacionCollection($publicaciones,$data);
    }

    /**
     * Get single Publicacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Publicacion for a given id",
     *   output = "Richpolis\PublicacionesBundle\Entity\Publicacion",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the publicacion is not found"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="contador", default="false", description="Activar el contador.")
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:show.html.twig",
     *  templateVar="publicacion"
     * )
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * @param int|string    $id      the publicacion id or slug
     *
     * @return array
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function getPublicacionAction(ParamFetcherInterface $paramFetcher,$id)
    {
        $contador =        $paramFetcher->get('contador');
        
        if(is_numeric($id)){
            $publicacion = $this->getOr404($id);
        }else{
            $publicacion = $this->getForSlugOr404($id);
        }
        
        if($contador){
            $publicacionesSession = $this->getPublicacionesSession();
            if(!isset($publicacionesSession[$publicacion->getSlug()])){
                $publicacion->setVisitas($publicacion->getVisitas()+1);
                $this->getDoctrine()->getManager()->flush();
                $publicacionesSession[$publicacion->getSlug()]=true;
                $this->setPublicacionesSession($publicacionesSession);
            }
        }
        
        return $publicacion;
    }

    /**
     * Presents the form to use to create a new publicacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:new.html.twig",
     *  templateVar = "form"
     * )
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @param Request $request the request object
     * @return FormTypeInterface
     */
    public function newPublicacionAction(Request $request)
    {
        $publicacion = new Publicacion();
        $max=$this->getDoctrine()->getRepository('PublicacionesBundle:Publicacion')
                ->getMaxPosicion();
        
        if(!is_null($max)){
            $publicacion->setPosition($max +1);
        }else{
            $publicacion->setPosition(1);
        }
        
        /*$usuario = $request->query->get('usuario');
        
        $user = $this->getDoctrine()->getRepository('BackendBundle:Usuario')
                ->find($usuario);
        $publicacion->setUsuario($user);*/
        $publicacion->setVisitas(0);
        
        return $this->createForm(new PublicacionType(),$publicacion);
    }

    /**
     * Create a Publicacion from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new publicacion from the submitted data.",
     *   input = "Richpolis\PublicacionesBundle\Form\PublicacionType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:new.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postPublicacionAction(Request $request)
    {
        try {
            $parameters = $request->request->all();
            $parameters['usuario']=$this->getDoctrine()->getRepository('BackendBundle:Usuario')
                ->find($parameters['usuario']);
            $parameters['categoria']=$this->getDoctrine()->getRepository('PublicacionesBundle:Categoria')
                ->find($parameters['categoria']);
            $parameters['slug']= \Richpolis\BackendBundle\Utils\Richsys::slugify($parameters['titulo']);
            /*$publicacion = $this->container->get('apirest.publicacion.handler')->post(
                $parameters
            );*/
            
            $publicacion = new Publicacion();
            $publicacion->setTitulo($parameters['titulo']);
            $publicacion->setDescripcionCorta($parameters['descripcionCorta']);
            $publicacion->setContenido($parameters['contenido']);
            $publicacion->setCategoria($parameters['categoria']);
            $publicacion->setUsuario($parameters['usuario']);
            $publicacion->setPosition($parameters['position']);
            $publicacion->setInCarrusel(($parameters['inCarrusel']=="1"));
            $publicacion->setIsActive(($parameters['isActive']=="1"));
            $publicacion->setVisitas($parameters['visitas']);
            $publicacion->setSlug($parameters['slug']);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($publicacion);
            $em->flush();

            $routeOptions = array(
                'id' => $publicacion->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_publicacion', $routeOptions, Codes::HTTP_CREATED);
            }else{
                return $this->handleView($this->view($publicacion,Codes::HTTP_CREATED));
            }
            

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Presents the form to use to update an existing note.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the note is not found"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the note id
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function editPublicacionAction($id)
    {
        $publicacion = $this->findOr404($id);
        $publicacion->setCategoria(null);
        $publicacion->setUsuario(null);
        return $this->createForm(new PublicacionType(),$publicacion);

    }

    /**
     * Update existing publicacion from the submitted data or create a new publicacion at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\PublicacionesBundle\Form\PublicacionType",
     *   statusCodes = {
     *     201 = "Returned when the Publicacion is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the publicacion id
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function putPublicacionAction(Request $request, $id)
    {
        try {
            
            $parameters = $request->request->all();
            $parameters['usuario']=$this->getDoctrine()->getRepository('BackendBundle:Usuario')
                ->find($parameters['usuario']);
            $parameters['categoria']=$this->getDoctrine()->getRepository('PublicacionesBundle:Categoria')
                ->find($parameters['categoria']);
            $parameters['slug']= \Richpolis\BackendBundle\Utils\Richsys::slugify($parameters['titulo']);
            
            $publicacion = $this->container->get('apirest.publicacion.handler')->find($id);
            
            /*if (!($publicacion = $this->container->get('apirest.publicacion.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $publicacion = $this->container->get('apirest.publicacion.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $publicacion = $this->container->get('apirest.publicacion.handler')->put(
                    $publicacion,
                    $request->request->all()
                );
            }*/
            
            $statusCode = Codes::HTTP_NO_CONTENT;
            
            $publicacion->setTitulo($parameters['titulo']);
            $publicacion->setDescripcionCorta($parameters['descripcionCorta']);
            $publicacion->setContenido($parameters['contenido']);
            $publicacion->setCategoria($parameters['categoria']);
            $publicacion->setUsuario($parameters['usuario']);
            $publicacion->setPosition($parameters['position']);
            $publicacion->setInCarrusel(($parameters['inCarrusel']=="1"));
            $publicacion->setIsActive(($parameters['isActive']=="1"));
            $publicacion->setVisitas($parameters['visitas']);
            $publicacion->setSlug($parameters['slug']);
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $routeOptions = array(
                'id' => $publicacion->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_publicacion', $routeOptions, $statusCode);
            }else{
                return $this->handleView($this->view(null,$statusCode));
            }
        
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing publicacion from the submitted data or create a new publicacion at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\PublicacionesBundle\Form\PublicacionType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the publicacion id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function patchPublicacionAction(Request $request, $id)
    {
        try {
            $publicacion = $this->container->get('apirest.publicacion.handler')->patch(
                $this->findOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $publicacion->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_publicacion', $routeOptions, Codes::HTTP_NO_CONTENT);
            }else{
                return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
            }
            

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Removes a publicacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the publicacion is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the publicacion id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function deletePublicacionAction(Request $request, $id)
    {
        $publicacion = $this->findOr404($id);
        $em = $this->getDoctrine()->getManager();

        foreach($publicacion->getGalerias() as $galeria){
            $em->remove($galeria);
        }

        $em->remove($publicacion);
        $em->flush();
        $routeOptions = array(
            '_format' => $request->getRequestFormat()
        );        
        if($routeOptions['_format']=="html"){
            return $this->routeRedirectView('get_publicaciones', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Fetch a Publicacion or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return PublicacionInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404($id)
    {
        if (!($publicacion = $this->container->get('apirest.publicacion.handler')->find($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $publicacion;
    }
    
    /**
     * Fetch a Publicacion or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return PublicacionInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($publicacion = $this->container->get('apirest.publicacion.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $publicacion;
    }
    
    /**
     * Fetch a Publicacion or throw an 404 Exception.
     *
     * @param mixed $slug
     *
     * @return PublicacionInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getForSlugOr404($slug)
    {
        if (!($publicacion = $this->container->get('apirest.publicacion.handler')->getForSlug($slug))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$slug));
        }

        return $publicacion;
    }
    
    /**
     * Subir de posicion una publicacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the publicacion is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the publicacion id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function patchPublicacionUpAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroUp = $this->findOr404($id);
        
        if ($registroUp) {
            $registroDown=$em->getRepository('PublicacionesBundle:Publicacion')
                    ->getRegistroUpOrDown($registroUp,true);
            if ($registroDown) {
                $posicion=$registroUp->getPosition();
                $registroUp->setPosition($registroDown->getPosition());
                $registroDown->setPosition($posicion);
                $em->flush();
            }
        }
        
        $routeOptions = array(
            '_format' => $request->getRequestFormat()
        );
        
        if($routeOptions['_format']=="html"){
            return $this->routeRedirectView('get_publicaciones', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Subir de posicion una publicacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the publicacion is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the publicacion id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function patchPublicacionDownAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroDown = $this->findOr404($id);
        
        if ($registroDown) {
            $registroUp=$em->getRepository('PublicacionesBundle:Publicacion')
                    ->getRegistroUpOrDown($registroDown,false);
            if ($registroUp) {
                $posicion=$registroUp->getPosition();
                $registroUp->setPosition($registroDown->getPosition());
                $registroDown->setPosition($posicion);
                $em->flush();
            }
        }
        
        $routeOptions = array(
            '_format' => $request->getRequestFormat()
        );
        
        if($routeOptions['_format']=="html"){
            return $this->routeRedirectView('get_publicaciones', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * List all galerias de una publicacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="active", default=true, description="Visualizar galerias activas o inactivas.")
     * @Annotations\QueryParam(name="all", default=true, description="Muestra todas las imagenes inactivas o activas.")
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:galerias.html.twig",
     *  templateVar = "galerias"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getPublicacionGaleriasAction($id){
        $em = $this->getDoctrine()->getManager();
        $entities=$em->getRepository('PublicacionesBundle:Galeria')
                ->getGaleriasConPublicacionPorId($id);
        
        return $entities;
    }
    
    /**
     * Ordenar las galerias de una publiacion.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the publicacion is not found"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="registro", nullable=false, description="Registros para ordenar.")
     * 
     * @param Request $request the request object
     * @param int     $id      the publicacion id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function patchPublicacionGaleriasOrdenarAction($id)
    {
        $request=$this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $publicacion = $this->findOr404($id);
            $registro_order = $request->query->get('registro');
            $em=$this->getDoctrine()->getManager();
            $result['ok']="ok";
            foreach($registro_order as $order=>$idGaleria)
            {
                $registro=$em->getRepository('PublicacionesBundle:Galeria')->find($idGaleria);
                if($registro->getPosition()!=($order+1)){
                    try{
                        $registro->setPosition($order+1);
                        $em->flush();
                    }catch(Exception $e){
                        $result['ok']=$e->getMessage();
                    }    
                }
            }
            
            $routeOptions = array(
            '_format' => $request->getRequestFormat()
            );

            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('api_1_get_publicaciones', $routeOptions, Codes::HTTP_NO_CONTENT);
            }else{
                return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
            }
        }
        else {
            return null;
        }
    }
    
    /**
     * Create a Galeria from the submitted data for one publication.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new galeria from the submitted data.",
     *   input = "Richpolis\PublicacionesBundle\Form\GaleriaType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Galerias:resultUpload.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "result"
     * )
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postPublicacionGaleriaAction(Request $request,$id){
        
       // list of valid extensions, ex. array("jpeg", "xml", "bmp")
       $allowedExtensions = array("jpeg","png","gif","jpg","flv","mp4");
       // max file size in bytes
       $sizeLimit = 6 * 1024 * 1024;
       $uploader = new qqFileUploader($allowedExtensions, $sizeLimit,$request->server);
       $uploads= $this->container->getParameter('richpolis.uploads');
       $result = $uploader->handleUpload($uploads."/galerias/");
       
       // to pass data through iframe you will need to encode all html tags
       /*****************************************************************/
       //$file = $request->getParameter("qqfile");
       $em = $this->getDoctrine()->getManager();
       $max = $em->getRepository('PublicacionesBundle:Galeria')->getMaxPosicion();
       $publicacion=$em->getRepository('PublicacionesBundle:Publicacion')->find($id);
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
           $registro->setPublicacion($publicacion);
           $registro->setTipoArchivo(Galeria::IMAGEN);
           
           //unset($result["filename"],$result['original'],$result['titulo'],$result['contenido']);
           $em->persist($registro);
           $registro->crearThumbnail();
           $em->flush();
        }
        
        $request->setFormat('json','application/json');
        $request->setRequestFormat('json');
        
        $routeOptions = array(
            'id' => $publicacion->getId(),
            '_format' => $request->getRequestFormat(),
        );

        if ($routeOptions['_format'] == "html") {
            return $this->routeRedirectView('api_1_get_publicacion', $routeOptions, Codes::HTTP_CREATED);
        } else {
            //return $this->handleView($this->view(new ResultGaleria($result), Codes::HTTP_CREATED));
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
       
    }
    
    public function contadorPublicacion($publicacion_id){ 

   	$archivo = $this->getAbsolutePath("publicacion".$publicacion_id.".txt"); 
   	$info = array(); 

   	//comprobar si existe el archivo 
   	if (file_exists($archivo)){ 
      	 // abrir archivo de texto y introducir los datos en el array $info 
      	 $fp = fopen($archivo,"r"); 
      	 $contador = fgets($fp, 26); 
      	 $info = explode(" ",$contador); 
      	 fclose($fp); 

      	 // poner nombre a cada dato 
      	 $mes_actual = date("m"); 
      	 $mes_ultimo = $info[0]; 
      	 $visitas_mes = $info[1]; 
      	 $visitas_totales = $info[2]; 
   	}else{ 
      	 // inicializar valores 
      	 $mes_actual = date("m"); 
      	 $mes_ultimo = "0"; 
      	 $visitas_mes = 0; 
      	 $visitas_totales = 0; 
   	} 

   	// incrementar las visitas del mes según si estamos en el mismo 
   	// mes o no que el de la ultima visita, o ponerlas a cero 
   	if ($mes_actual==$mes_ultimo){ 
      	 $visitas_mes++; 
   	}else{ 
      	 $visitas_mes=1; 
   	} 
   	$visitas_totales++; 

   	// reconstruir el array con los nuevos valores 
   	$info[0] = $mes_actual; 
   	$info[1] = $visitas_mes; 
   	$info[2] = $visitas_totales; 

   	// grabar los valores en el archivo de nuevo 
   	$info_nueva = implode(" ",$info); 
   	$fp = fopen($archivo,"w+"); 
   	fwrite($fp, $info_nueva, 26); 
   	fclose($fp); 
        
        return $info;
    }
    
    public function getAbsolutePath($archivo)
    {
        return $this->getUploadRootDir().'/publicaciones/'.$archivo;
    }
    
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../html'.$this->getUploadDir();
    }
    
    protected function getPublicacionesSession()
    {
        return $this->get('session')->get('publicaciones', array());
    }
    
    protected function setPublicacionesSession($publicaciones)
    {
        return $this->get('session')->set('publicaciones', $publicaciones);
    }
    
}
