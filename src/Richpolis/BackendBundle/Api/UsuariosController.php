<?php

// Rest Full Api Usuarios
namespace Richpolis\BackendBundle\Controller;

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
use Richpolis\BackendBundle\Entity\Usuario;
use Richpolis\BackendBundle\Form\UsuarioType;
use Richpolis\BackendBundle\Utils\qqFileUploader;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class UsuariosController extends FOSRestController
{
    
    
    /**
     * List all usuarios.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing usuarios.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="100", description="How many usuarios to return.")
     *
     * @Annotations\View(
     *  template = "BackendBundle:Usuario:index.html.twig",
     *  templateVar = "usuarios"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getUsuariosAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $handler = $this->container->get('richpolis_backend.usuario.handler');
        return $handler->all($limit, $offset);
    }

    /**
     * Get single Usuario.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Usuario for a given id",
     *   output = "Richpolis\BackendBundle\Entity\Usuario",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the usuario is not found"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "BackendBundle:Usuario:show.html.twig",
     *  templateVar="usuario"
     * )
     *
     * @param int     $id      the usuario id
     *
     * @return array
     *
     * @throws NotFoundHttpException when usuario not exist
     */
    public function getUsuarioAction($id)
    {
        $usuario = $this->getOr404($id);

        return $usuario;
    }

    /**
     * Presents the form to use to create a new usuario.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "BackendBundle:Usuario:new.html.twig",
     *  templateVar = "form"
     * )
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface
     */
    public function newUsuarioAction()
    {
        return $this->createForm(new UsuarioType(),new Usuario());
    }

    /**
     * Create a Usuario from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new usuario from the submitted data.",
     *   input = "Richpolis\BackendBundle\Form\UsuarioType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "BackendBundle:Usuario:new.html.twig",
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
    public function postUsuarioAction(Request $request)
    {
        try {
            $parameters = $request->request->all();
            if(isset($parameters['file']))unset($parameters['file']);
            
            $newUsuario = $this->container->get('richpolis_backend.usuario.handler')->post(
                $parameters
                    
            );

            $routeOptions = array(
                'id' => $newUsuario->getId(),
                '_format' => $request->getRequestFormat()
            );
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('backend_usuarios', $routeOptions, Codes::HTTP_CREATED);
            }else{
                return $this->handleView($this->view($newUsuario,Codes::HTTP_CREATED));
            }
            //return $this->routeRedirectView('get_usuario', $routeOptions, Codes::HTTP_CREATED);

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
     *  template = "BackendBundle:Usuario:edit.html.twig",
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
    public function editUsuarioAction($id)
    {
        $usuario = $this->getOr404($id);
        return $this->createForm(new UsuarioType(),$usuario);

    }

    /**
     * Update existing usuario from the submitted data or create a new usuario at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\BackendBundle\Form\UsuarioType",
     *   statusCodes = {
     *     201 = "Returned when the Usuario is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "BackendBundle:Usuario:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the usuario id
     *
     * @PreAuthorize("hasRole('ROLE_API')")
     * 
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when usuario not exist
     */
    public function putUsuarioAction(Request $request, $id)
    {
        try {
            
            $parameters = $request->request->all();
            if(isset($parameters['file']))unset($parameters['file']);
            if(isset($parameters['stringGrupo']))unset($parameters['stringGrupo']);            
            if(isset($parameters['created_at']))unset($parameters['created_at']);
            if(isset($parameters['updated_at']))unset($parameters['updated_at']);
            if(isset($parameters['webPath']))unset($parameters['webPath']);
            if(isset($parameters['imagenOld'])){
                $imagenOld = $parameters['imagenOld'];
                unset($parameters['imagenOld']);
            }
            
            
            if (!($usuario = $this->container->get('richpolis_backend.usuario.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $usuario = $this->container->get('richpolis_backend.usuario.handler')->post(
                    $parameters
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $usuario = $this->container->get('richpolis_backend.usuario.handler')->put(
                    $usuario,
                    $parameters
                );
            }
            
            if(isset($imagenOld)){
                $file = $usuario->getUploadRootDir() . "/" . $imagenOld;
                if(file_exists($file)){
                    unlink($file);
                }
            }

            $routeOptions = array(
                'id' => $usuario->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('backend_usuarios', $routeOptions, $statusCode);
            }else{
                return $this->handleView($this->view(null,$statusCode));
            }
            

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing usuario from the submitted data or create a new usuario at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Richpolis\BackendBundle\Form\UsuarioType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "BackendBundle:Usuario:edit.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the usuario id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when usuario not exist
     */
    public function patchUsuarioAction(Request $request, $id)
    {
        try {
            $usuario = $this->container->get('richpolis_backend.usuario.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $usuario->getId(),
                '_format' => $request->getRequestFormat()
            );
            
            if($routeOptions['_format']=="html"){
                return $this->routeRedirectView('get_usuario', $routeOptions, Codes::HTTP_NO_CONTENT);
            }else{
                return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
            }
            

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Removes a usuario.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the usuario is not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the usuario id
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @return RouteRedirectView
     *
     * @throws NotFoundHttpException when usuario not exist
     */
    public function deleteUsuarioAction(Request $request, $id)
    {
        $usuario = $this->getOr404($id);
        $em = $this->getDoctrine()->getManager();

        foreach($usuario->getPublicaciones() as $publicacion){
            foreach($publicacion->getGalerias() as $galeria){
                $em->remove($galeria);
            }
            $em->remove($publicacion);
        }


        $em->remove($usuario);
        $em->flush();
        $routeOptions = array(
            '_format' => $request->getRequestFormat()
        );
        if($routeOptions['_format']=="html"){
            return $this->routeRedirectView('get_usuarios', $routeOptions, Codes::HTTP_NO_CONTENT);
        }else{
            return $this->handleView($this->view(null,Codes::HTTP_NO_CONTENT));
        }
    }

    /**
     * Fetch a Usuario or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return UsuarioInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($usuario = $this->container->get('richpolis_backend.usuario.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $usuario;
    }
    
    /**
     * Create a Imagen from the submitted data for one user.
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
     *  template = "BackendBundle:Usuarios:show.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "usuario"
     * )
     * 
     * @PreAuthorize("hasRole('ROLE_API')")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postUsuarioUploadAction(Request $request){
        
       // list of valid extensions, ex. array("jpeg", "xml", "bmp")
       $allowedExtensions = array("jpeg","png","gif","jpg","flv","mp4");
       // max file size in bytes
       $sizeLimit = 6 * 1024 * 1024;
       $uploader = new qqFileUploader($allowedExtensions, $sizeLimit,$request->server);
       $uploads= $this->container->getParameter('richpolis.uploads');
       $result = $uploader->handleUpload($uploads."/usuarios/");
       
       // to pass data through iframe you will need to encode all html tags
       /*****************************************************************/
       //$file = $request->getParameter("qqfile");
       if(!isset($result["success"])){
           //$result
        }
        
        $request->setFormat('json','application/json');
        $request->setRequestFormat('json');
        
        $routeOptions = array(
            'id' => 0,
            '_format' => $request->getRequestFormat(),
        );

        if ($routeOptions['_format'] == "html") {
            return $this->routeRedirectView('api_1_get_publicacion', $routeOptions, Codes::HTTP_CREATED);
        } else {
            //return $this->handleView($this->view(new ResultGaleria($result), Codes::HTTP_CREATED));
            return $this->handleView($this->view($result,Codes::HTTP_CREATED));
        }
       
    }
}
