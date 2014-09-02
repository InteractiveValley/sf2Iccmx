<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;

use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Entity\Galeria;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;

class DefaultController extends Controller
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
        if (isset($filters['status'])) {
            $this->tipos = Publicacion::$sStatus;
            foreach ($this->tipos as $key => $value) {
                if ($key == $filters['status']) {
                    $tipo = $key;
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
     * @Route("/", name="backend_homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $statusAprobado = Publicacion::STATUS_APROBADO;
        $statusPublicado = Publicacion::STATUS_PUBLICADO;
        $categoriaPublicacion = CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION;
        $categoriaLlamados = CategoriaPublicacion::TIPO_CATEGORIA_LLAMADOS;
        $categoriaTuEspacio = CategoriaPublicacion::TIPO_CATEGORIA_TU_ESPACIO;
        $categoriaHeraldoTv = CategoriaPublicacion::TIPO_CATEGORIA_HERALDO_TV;

        $publicidad = $em->getRepository('PublicidadBundle:Publicidad')
                            ->findBy(array('isActive'=>true), array('createdAt'=>'DESC'), 20);
        
        $arreglo =compact('statusAprobado','statusPublicado',
            'categoriaPublicacion','categoriaLlamados','categoriaHeraldoTv',
            'categoriaTuEspacio','publicidad');

        return $arreglo;
    }
    
    
    /**
     * @Route("/get/publicaciones", name="backend_get_publicaciones")
     * @Template("BackendBundle:Default:lista.html.twig")
     */
    public function getPublicacionesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $status = $request->get('status', 0);
        $tipoCategoria = $request->get('tipoCategoria', CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION);
        $paginador = true;
        if($tipoCategoria == CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION){
            $isPrincipal = $request->get('isPrincipal', false);
            $inCarrusel = $request->get('inCarrusel', false);
            if($isPrincipal){
                $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                                ->findPortada($status,true);
                $paginador = false;
            }else if($inCarrusel){
                $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                                ->findCarrusel($status,true);
                $paginador = false;
            }else{
                $publicaciones = $this->getPublicaciones($em,$status,$tipoCategoria);
            }
        }else{
            $publicaciones = $this->getPublicaciones($em,$status,$tipoCategoria);
        }
        
        return array(
            'entities'=>$publicaciones,
            'tipoCategoria' => $tipoCategoria,
            'paginador'=>$paginador,
            'stringTipoCategoria' =>  CategoriaPublicacion::$sTipoCategoria[$tipoCategoria],
        );
    }
    
    private function getPublicaciones($em,$status,$tipoCategoria){
        $query = $em->getRepository('PublicacionesBundle:Publicacion')
                  ->queryPublicacionesPorTipoCategoria($status,$tipoCategoria);
        $user = $this->get('security.context')->getToken()->getUser();
        
        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            $query->andWhere('u.id=:usuario')->setParameter('usuario',$user->getId());
        }
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(),
            $this->get('request')->query->get('page', 1)/*page number*/,
            100/*limit per page*/
        );
        return $pagination;

    }
    

    /**
     * @Route("/publicacion/los/mas/vistos", name="backend_publicacion_losmasvisto")
     * @Template("BackendBundle:Default:lista.html.twig")
     */
    public function publicacionLosMasVistosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tipoCategoria = $request->get('tipoCategoria', CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION);
        
        $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                            ->findLosMasVistos(0,$tipoCategoria);
        
        return array(
            'entities'=>$publicaciones,
            'tipoCategoria' => $tipoCategoria,
            'paginador'=>false,
            'stringTipoCategoria' =>  CategoriaPublicacion::$sTipoCategoria[$tipoCategoria],
        );
    }

    /**
     * @Route("/publicacion/los/mas/comentados", name="backend_publicacion_losmascomentados")
     * @Template("BackendBundle:Default:lista.html.twig")
     */
    public function publicacionLosMasComentadosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tipoCategoria = $request->get('tipoCategoria', CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION);
        
        $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                            ->findLosMasComentados(0,$tipoCategoria);
        
        return array(
            'entities'=>$publicaciones,
            'tipoCategoria' => $tipoCategoria,
            'paginador'=>false,
            'stringTipoCategoria' =>  CategoriaPublicacion::$sTipoCategoria[$tipoCategoria],
        );
    }
 
    /**
     * @Route("/login", name="backend_login")
     * @Template()
     */
    
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // obtiene el error de inicio de sesión si lo hay
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'BackendBundle:Default:login.html.twig',
            array(
                // último nombre de usuario ingresado
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
    
    /**
     * @Route("/login_check", name="backend_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="backend_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    
    /**
     * Activa o inactiva si una publicacion si es principal o no.
     *
     * @Route("/publicacion/{id}/is/principal", name="backend_publicacion_is_principal", requirements={"id" = "\d+"})
     * @Method("PATCH")
     */
    public function publicacionIsPrincipalAction($id) {
        $em = $this->getDoctrine()->getManager();
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$publicacion) {
            throw $this->createNotFoundException('Unable to find publicacion entity.');
        }
        $valor = !$publicacion->getIsPrincipal();
        $publicacion->setIsPrincipal($valor);
        $publicacion->setIsCarrusel($valor);
        
        $em->flush();

        return $this->render("BackendBundle:Default:item.html.twig", array(
                   'entity' => $publicacion
        ));
    }

    /**
     * Activa o inactiva si una publicacion si esta en carrusel o no.
     *
     * @Route("/publicacion/{id}/in/carrusel", name="backend_publicacion_in_carrusel", requirements={"id" = "\d+"})
     * @Method("PATCH")
     */
    public function publicacionInCarruselAction($id) {
        $em = $this->getDoctrine()->getManager();
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$publicacion) {
            throw $this->createNotFoundException('Unable to find publicacion entity.');
        }
        $publicacion->setIsCarrusel(!$publicacion->getIsCarrusel());
        $em->flush();

        return $this->render("BackendBundle:Default:item.html.twig", array(
                    'entity' => $publicacion
        ));
    }
    
    /**
     * Publicar una publicacion para que aparezca en la pagina.
     *
     * @Route("/publicacion/{id}/publicar", name="backend_publicacion_publicar", requirements={"id" = "\d+"})
     * @Method("PATCH")
     */
    public function publicacionPublicarAction($id) {
        $em = $this->getDoctrine()->getManager();
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$publicacion) {
            throw $this->createNotFoundException('Unable to find publicacion entity.');
        }
        $publicacion->setStatus(Publicacion::STATUS_PUBLICADO);
        $em->flush();

        return $this->render("BackendBundle:Default:item.html.twig", array(
                    'entity' => $publicacion
        ));
    }
    
    /**
     * Revisar una publicacion para que se haga la correccion y se vuelva activar para editar.
     *
     * @Route("/publicacion/{id}/revisar", name="backend_publicacion_revisar", requirements={"id" = "\d+"})
     * @Method("PATCH")
     */
    public function publicacionRevisarAction($id) {
        $em = $this->getDoctrine()->getManager();
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$publicacion) {
            throw $this->createNotFoundException('Unable to find publicacion entity.');
        }
        $publicacion->setStatus(Publicacion::STATUS_REVISAR);
        $em->flush();

        return $this->render("BackendBundle:Default:item.html.twig", array(
                    'entity' => $publicacion
        ));
    }
    
    /**
     * Caducar una publicacion publicada para que deje de aparecer en la pagina.
     *
     * @Route("/publicacion/{id}/caducar", name="backend_publicacion_caducar", requirements={"id" = "\d+"})
     * @Method("PATCH")
     */
    public function publicacionCaducarAction($id) {
        $em = $this->getDoctrine()->getManager();
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$publicacion) {
            throw $this->createNotFoundException('Unable to find publicacion entity.');
        }
		
        $publicacion->setStatus(Publicacion::STATUS_CADUCADO);
        $em->flush();

        return $this->render("BackendBundle:Default:item.html.twig", array(
                    'entity' => $publicacion
        ));
    }
}
