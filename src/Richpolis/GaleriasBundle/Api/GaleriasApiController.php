<?php

// Rest Full Api Galerias
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
use Richpolis\PublicacionesBundle\Entity\Galeria;
use Richpolis\PublicacionesBundle\Form\GaleriaType;
use Richpolis\PublicacionesBundle\Form\GaleriaLinkVideoType;
use Richpolis\BackendBundle\Utils\qqFileUploader;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class GaleriasController extends FOSRestController
{
    
    
    /**
     * List all galerias.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing galerias.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many galerias to return.")
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Galerias:index.html.twig",
     *  templateVar = "galerias"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getGaleriasAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        //$handler = $this->container->get('apirest.galeria.handler');
        //return $handler->all($limit, $offset);
        $entities = $this->getDoctrine()->getRepository('PublicacionesBundle:Galeria')
                ->getGaleriasActivas(0,"",true,true);
        return $entities;
    }

    /**
     * Get single Galeria.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Galeria for a given id",
     *   output = "Richpolis\PublicacionesBundle\Entity\Galeria",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the galeria is not found"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Galerias:show.html.twig",
     *  templateVar="galeria"
     * )
     *
     * @param int     $id      the galeria id
     *
     * @return array
     *
     * @throws NotFoundHttpException when galeria not exist
     */
    public function getGaleriaAction($id)
    {
        $galeria = $this->getOr404($id);

        return $galeria;
    }

    /**
     * Presents the form to use to create a new galeria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Galerias:new.html.twig",
     *  templateVar = "form"
     * )
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface
     */
    public function newGaleriaAction(Request $request)
    {
        $galeria = new Galeria();
        $max=$this->getDoctrine()->getRepository('PublicacionesBundle:Galeria')
                ->getMaxPosicion();
        
        if(!is_null($max)){
            $galeria->setPosition($max +1);
        }else{
            $galeria->setPosition(1);
        }
        
        $tipo = $request->query->get('tipo');
        
        $galeria->setTipoArchivo($tipo);
        
        if($tipo == Galeria::IMAGEN){
            return $this->createForm(new GaleriaType(),$galeria);
        }elseif($tipo == Galeria::LINK_VIDEO){
            return $this->createForm(new GaleriaLinkVideoType(),$galeria);
        }
    }

    /**
     * Create a Galeria from the submitted data.
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
     *  template = "PublicacionesBundle:Galerias:new.html.twig",
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
    public function postGaleriaAction(Request $request)
    {
        try {
            $parameters = $request->request->all();
            $parameters['publicacion']=$this->getDoctrine()->getRepository('PublicacionesBundle:Publicacion')
                ->find($parameters['publicacion']);
            /*$galeria = $this->container->get('apirest.galeria.handler')->post(
                $parameters
            );*/
            
            $galeria = new Galeria();
            
            $max=$this->getDoctrine()->getRepository('PublicacionesBundle:Galeria')
                ->getMaxPosicion();
        
            if(!is_null($max)){
                $galeria->setPosition($max +1);
            }else{
                $galeria->setPosition(1);
            }
            
            $galeria->setTitulo($parameters['titulo']);
            $galeria->setDescripcion($parameters['descripcion']);
            $galeria->setTipoArchivo($parameters['tipoArchivo']);
            $galeria->setThumbnail($parameters['thumbnail']);
            $galeria->setArchivo($parameters['archivo']);
            $galeria->setIsActive(true);
            $galeria->setPublicacion($parameters['publicacion']);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($galeria);
            $em->flush();
            
            if($galeria->getTipoArchivo()==Galeria::IMAGEN){
                $avalancheService = $this->get('imagine.cache.path.resolver');
                $cachedImage = $avalancheService->getBrowserPath($object->getWebPath(), 'publicaciones_galeria_1');
                
            }

            $routeOptions = array(
                'id' => $galeria->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_galeria', $routeOptions, Codes::HTTP_CREATED);
            }else{
                return $this->handleView($this->view($galeria,Codes::HTTP_CREATED));
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
     *  template = "PublicacionesBundle:Galerias:edit.html.twig",
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
    public function editGaleriaAction($id)
    {
        $galeria = $this->findOr404($id);
        $galeria->setPublicacion(null);
        return $this->createForm(new GaleriaType(),$galeria);

    }

    /**
     * Update existing galeria from the submitted data or create a new galeria at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\PublicacionesBundle\Form\GaleriaType",
     *   statusCodes = {
     *     201 = "Returned when the Galeria is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Galerias:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the galeria id
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when galeria not exist
     */
    public function putGaleriaAction(Request $request, $id)
    {
        try {
            $parameters = $request->request->all();
            $parameters['publicacion']=$this->getDoctrine()->getRepository('PublicacionesBundle:Publicacion')
                ->find($parameters['publicacion']);
            $galeria = $this->container->get('apirest.galeria.handler')->find($id);
            /*if (!($galeria = $this->container->get('apirest.galeria.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $galeria = $this->container->get('apirest.galeria.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $galeria = $this->container->get('apirest.galeria.handler')->put(
                    $galeria,
                    $request->request->all()
                );
            }*/
            $statusCode = Codes::HTTP_NO_CONTENT;
            $galeria->setTitulo($parameters['titulo']);
            $galeria->setDescripcion($parameters['descripcion']);
            $galeria->setTipoArchivo($parameters['tipoArchivo']);
            $galeria->setThumbnail($parameters['thumbnail']);
            $galeria->setArchivo($parameters['archivo']);
            $galeria->setIsActive($parameters['isActive']);
            $galeria->setPosition($parameters['position']);
            $galeria->setPublicacion($parameters['publicacion']);
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $routeOptions = array(
                'id' => $galeria->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_galeria', $routeOptions, $statusCode);
            }else{
                return $this->handleView($this->view(null,$statusCode));
            }
        
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing galeria from the submitted data or create a new galeria at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\PublicacionesBundle\Form\GaleriaType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "PublicacionesBundle:Galerias:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the galeria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when galeria not exist
     */
    public function patchGaleriaAction(Request $request, $id)
    {
        try {
            $galeria = $this->container->get('apirest.galeria.handler')->patch(
                $this->findOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $galeria->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('api_1_get_galeria', $routeOptions, Codes::HTTP_NO_CONTENT);
            }else{
                return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
            }
            

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Removes a galeria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the galeria is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the galeria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when galeria not exist
     */
    public function deleteGaleriaAction(Request $request, $id)
    {
        $galeria = $this->findOr404($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($galeria);
        $em->flush();
        $routeOptions = array(
            '_format' => $request->getRequestFormat()
        );        
        if($routeOptions['_format']=="html"){
            return $this->routeRedirectView('get_galerias', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Fetch a Galeria or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return GaleriaInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404($id)
    {
        if (!($galeria = $this->container->get('apirest.galeria.handler')->find($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $galeria;
    }
    
    /**
     * Fetch a Galeria or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return GaleriaInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($galeria = $this->container->get('apirest.galeria.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $galeria;
    }
    
    /**
     * Subir de posicion una galeria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the galeria is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the galeria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when galeria not exist
     */
    public function patchGaleriaUpAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroUp = $this->findOr404($id);
        
        if ($registroUp) {
            $registroDown=$em->getRepository('PublicacionesBundle:Galeria')
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
            return $this->routeRedirectView('get_galerias', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    /**
     * Subir de posicion una galeria.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the galeria is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the galeria id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when galeria not exist
     */
    public function patchGaleriaDownAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroDown = $this->findOr404($id);
        
        if ($registroDown) {
            $registroUp=$em->getRepository('PublicacionesBundle:Galeria')
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
            return $this->routeRedirectView('get_galerias', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }
    
    
}
