<?php

// Rest Full Api Categorias
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
use Richpolis\PublicacionesBundle\Entity\Categoria;
use Richpolis\PublicacionesBundle\Form\CategoriaType;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class CategoriasController extends FOSRestController
{
    
    
    /**
     * List all categorias.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing categorias.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many categorias to return.")
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Categorias:index.html.twig",
     *  templateVar = "categorias"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getCategoriasAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        //$handler = $this->container->get('apirest.categoria.handler');
        //return $handler->all($limit, $offset);
        $entities = $this->getDoctrine()->getRepository('PublicacionesBundle:Categoria')
                ->getCategoriasActivas(0,"",true,true);
        return $entities;
    }

    /**
     * Get single Categoria.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Categoria for a given id",
     *   output = "Richpolis\PublicacionesBundle\Entity\Categoria",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the categoria is not found"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Categorias:show.html.twig",
     *  templateVar="categoria"
     * )
     *
     * @param int     $id      the categoria id
     *
     * @return array
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function getCategoriaAction($id)
    {
        if(is_numeric($id)){
            $categoria = $this->getOr404($id);
        }else{
            $categoria = $this->getForSlugOr404($id);
        }
        return $categoria;
    }
    
    /**
     * Get publicacines for one categoria.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets list de publicaciones a Categoria for a given id",
     *   output = "Richpolis\PublicacionesBundle\Entity\Categoria",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the categoria is not found"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Publicaciones:index.html.twig",
     *  templateVar = "publicaciones"
     * )
     *
     * @param int     $id      the categoria id
     *
     * @return array
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function getCategoriaPublicacionesAction($id)
    {
        if(is_numeric($id)){
            $categoria = $this->getOr404($id);
        }else{
            $categoria = $this->getForSlugOr404($id);
        }
        return $categoria->getPublicaciones();
    }

    /**
     * Presents the form to use to create a new categoria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Categorias:new.html.twig",
     *  templateVar = "form"
     * )
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface
     */
    public function newCategoriaAction()
    {
        $categoria = new Categoria();
        $max=$this->getDoctrine()->getRepository('PublicacionesBundle:Categoria')
                ->getMaxPosicion();
        
        if(!is_null($max)){
            $categoria->setPosition($max +1);
        }else{
            $categoria->setPosition(1);
        }
        
        return $this->createForm(new CategoriaType(),$categoria);
    }

    /**
     * Create a Categoria from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new categoria from the submitted data.",
     *   input = "Richpolis\PublicacionesBundle\Form\CategoriaType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Categorias:new.html.twig",
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
    public function postCategoriaAction(Request $request)
    {
        try {
            $newCategoria = $this->container->get('apirest.categoria.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newCategoria->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_categoria', $routeOptions, Codes::HTTP_CREATED);
            }else{
                return $this->handleView($this->view($newCategoria,Codes::HTTP_CREATED));
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
     *  template = "PublicacionesBundle:Categorias:edit.html.twig",
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
    public function editCategoriaAction($id)
    {
        $categoria = $this->findOr404($id);
        return $this->createForm(new CategoriaType(),$categoria);

    }

    /**
     * Update existing categoria from the submitted data or create a new categoria at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\PublicacionesBundle\Form\CategoriaType",
     *   statusCodes = {
     *     201 = "Returned when the Categoria is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Categorias:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the categoria id
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function putCategoriaAction(Request $request, $id)
    {
        try {
            if (!($categoria = $this->container->get('apirest.categoria.handler')->find($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $categoria = $this->container->get('apirest.categoria.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $categoria = $this->container->get('apirest.categoria.handler')->put(
                    $categoria,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $categoria->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_categoria', $routeOptions, $statusCode);
            }else{
                return $this->handleView($this->view(null,$statusCode));
            }
        
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing categoria from the submitted data or create a new categoria at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\PublicacionesBundle\Form\CategoriaType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Categorias:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the categoria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function patchCategoriaAction(Request $request, $id)
    {
        try {
            $categoria = $this->container->get('apirest.categoria.handler')->patch(
                $this->findOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $categoria->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_categoria', $routeOptions, Codes::HTTP_NO_CONTENT);
            }else{
                return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
            }
            

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Removes a categoria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the categoria is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the categoria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function deleteCategoriaAction(Request $request, $id)
    {
        $categoria = $this->findOr404($id);
        $em = $this->getDoctrine()->getManager();

        foreach($categoria->getPublicaciones() as $publicacion){
            foreach($publicacion->getGalerias() as $galeria){
                $em->remove($galeria);
            }
            $em->remove($publicacion);
        }

        $em->remove($categoria);
        $em->flush();
        $routeOptions = array(
            '_format' => $request->getRequestFormat()
        );        
        if($routeOptions['_format']=="html"){
            return $this->routeRedirectView('get_categorias', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Fetch a Categoria or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return CategoriaInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404($id)
    {
        if (!($categoria = $this->container->get('apirest.categoria.handler')->find($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $categoria;
    }
    
    /**
     * Fetch a Categoria or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return CategoriaInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($categoria = $this->container->get('apirest.categoria.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $categoria;
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
        if (!($categoria = $this->container->get('apirest.categoria.handler')->getForSlug($slug))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$slug));
        }

        return $categoria;
    }
    
    
    /**
     * Subir de posicion una categoria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the categoria is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the categoria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function patchCategoriaUpAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroUp = $this->findOr404($id);
        
        if ($registroUp) {
            $registroDown=$em->getRepository('PublicacionesBundle:Categoria')
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
            return $this->routeRedirectView('get_categorias', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Subir de posicion una categoria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the categoria is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the categoria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when categoria not exist
     */
    public function patchCategoriaDownAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroDown = $this->findOr404($id);
        
        if ($registroDown) {
            $registroUp=$em->getRepository('PublicacionesBundle:Categoria')
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
            return $this->routeRedirectView('get_categorias', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Ordenar las categorias.
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
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when publicacion not exist
     */
    public function patchCategoriasOrdenarRegistrosAction()
    {
        $request=$this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em=$this->getDoctrine()->getManager();
            $result['ok']="ok";
            foreach($registro_order as $order=>$idCategoria)
            {
                $registro=$em->getRepository('PublicacionesBundle:Categoria')->find($idCategoria);
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
}
