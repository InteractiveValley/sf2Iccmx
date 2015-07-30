<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Richpolis\FrontendBundle\Entity\Contacto;
use Richpolis\FrontendBundle\Form\ContactoType;
use Richpolis\FrontendBundle\Form\SolicitarPedidoType;
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;
use Richpolis\BackendBundle\Entity\Newsletter;
use Richpolis\BackendBundle\Form\NewsletterType;

class DefaultController extends Controller {
    
    protected function getValoresSession($key,$value = array()) {
        return $this->get('session')->get($key, $value);
    }

    protected function setVAloresSession($key,$value) {
        return $this->get('session')->set($key, $value);
    }
    
    protected function getPublicidadEnSession(&$em) {
        /*$publicidad = $this->getValoresSession('publicidad');
        if (isset($publicidad[Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO])) {
            return $publicidad;
        } else {*/
            $publicidadArray = array();
            
            $publicidads = $em->getRepository('PublicidadBundle:Publicidad')
                          ->getPublicidadActual();
            
            $publicidadArray[Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_IZQUIERDO]=null;
            $publicidadArray[Publicidad::TIPO_PUBLICIDAD_ENCABEZADO_DERECHO]=null;
            $publicidadArray[Publicidad::TIPO_PUBLICIDAD_ASIDE_ARRIBA]=null;
            $publicidadArray[Publicidad::TIPO_PUBLICIDAD_ASIDE_ABAJO]=null;

            foreach($publicidads as $publicidad){
                $publicidadArray[$publicidad->getTipoPublicidad()][]=$publicidad;
            }
            $this->setVAloresSession('publicidad', $publicidadArray);
            return $publicidadArray;
       /* }*/
    }
    
    protected function getLosmasVistosEnSession(&$em) {
        /*$losmasvistos = $this->getValoresSession('losmasvistos');
        if (count($losmasvistos)) {
            return $losmasvistos;
        } else {*/
            $losmasvistos = $em->getRepository('PublicacionesBundle:Publicacion')
                          ->findLosMasVistos(0,  CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION);
            $this->setVAloresSession('losmasvistos', $losmasvistos);
            return $losmasvistos;
        //}
    }
    
    protected function getLosmasComentadosEnSession(&$em) {
        /*$losmascomentados = $this->getValoresSession('losmascomentados');
        if (count($losmascomentados)) {
            return $losmascomentados;
        } else {*/
            $losmascomentados = $em->getRepository('PublicacionesBundle:Publicacion')
                          ->findLosMasComentados(0,  CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION);
            $this->setVAloresSession('losmascomentados', $losmascomentados);
            return $losmascomentados;
        //}
    }
    
    protected function getCategoriasEnSession(&$em) {
        $categorias = $this->getValoresSession('categorias');
        if (count($categorias)) {
            return $categorias;
        } else {
            $categorias = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findBy(array('tipoCategoria' => CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION));
            $this->setVAloresSession('categorias', $categorias);
            return $categorias;
        }
    }

    protected function getUltimasPublicaciones(&$em) {
        $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                ->getUltimasPublicaciones(10);
        return $publicaciones;
    }

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $eventos = $em->getRepository('PublicacionesBundle:Publicacion')
                ->getEventosEnInicio(100);

        $noticias = $em->getRepository('PublicacionesBundle:Publicacion')
                ->getUltimasNoticias(5);
        
        $aside = $em->getRepository('PublicacionesBundle:Aside')
                    ->findOneBy(array('clave'=>'inicio'));
        
        $pagina = $em->getRepository('PaginasBundle:Pagina')
                     ->findOneBy(array('pagina'=>'inicio'));        

        return array(
            'eventos' => $eventos,
            'noticias'=> $noticias,
            'aside'=>$aside,
            'pagina'=>$pagina,
        );
    }
    
    /**
     * @Route("/sesiones/eventos/comisiones/trabajo", name="sesiones_eventos_comisiones_trabajo")
     */
    public function sesionesEventosComisionesTrabajoAction(){
        $em = $this->getDoctrine()->getManager();
        $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                            ->getEventosEnComisionesTrabajo();
        return $this->render('FrontendBundle:Default:lista_eventos.html.twig', compact('publicaciones'));
    }
    
    /**
     * @Route("/articulos/comisiones/trabajo", name="articulos_comisiones_trabajo")
     */
    public function articulosComisionesTrabajoAction(){
        $em = $this->getDoctrine()->getManager();
        $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                            ->getNoticiasEnComisionesTrabajo();
        return $this->render('FrontendBundle:Default:lista_noticias.html.twig', compact('publicaciones'));
    }
    
    /**
     * @Route("/aside/{clave}", name="frontend_aside")
     * @Template()
     */
    public function asideAction(Request $request,$clave) {
        $em = $this->getDoctrine()->getManager();
        
        $aside = $em->getRepository('PublicacionesBundle:Aside')
                    ->findOneBy(array('clave'=>$clave));
        
        return compact('aside');
    }
    
    /**
     * @Route("/pie/pagina", name="frontend_pie_pagina")
     * @Template()
     */
    public function piePaginaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                        ->findOneBy(array('slug' => 'pie-pagina'));
        return array(
            'configuracion' => $configuracion,
        );
    }
    
    /**
     * @Route("/menu/principal", name="frontend_menu_principal")
     * @Template()
     */
    public function menuPrincipalAction() 
    {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                          ->findActivos();
        
        return array('categorias'=>$categorias);
    }
    
    /**
     * @Route("/quienes-somos", name="frontend_quienes_somos")
     * @Route("/quienes-somos/{categoriaSlug}", name="frontend_quienes_somos_categoria")
     * @Route("/quienes-somos/{categoriaSlug}/{publicacionSlug}", name="frontend_quienes_somos_publicacion")
     * @Method({"GET"})
     */
    public function quienesSomosAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_quienes_somos';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }else{
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => 'mensaje-de-nuestro-presidente'));
            $categoria = $publicacion->getCategoria();
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }
        
    }
    
    /**
     * @Route("/influencia-global", name="frontend_influencia_global")
     * @Route("/influencia-global/{categoriaSlug}", name="frontend_influencia_global_categoria")
     * @Route("/influencia-global/{categoriaSlug}/{publicacionSlug}", name="frontend_influencia_global_publicacion")
     * @Method({"GET"})
     */
    public function influenciaGlobalAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_influencia_global';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }else{
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' =>'agenda-global-de-comercio'));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }
        
    }
    
    /**
     * @Route("/productos-y-servicios", name="frontend_productos_servicios")
     * @Route("/productos-y-servicios/{categoriaSlug}", name="frontend_productos_servicios_categoria")
     * @Route("/productos-y-servicios/{categoriaSlug}/{publicacionSlug}", name="frontend_productos_servicios_publicacion")
     * @Method({"GET"})
     */
    public function productosServiciosAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_productos_servicios';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }else{
            /*$categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'productos-y-servicios'));
            $publicaciones = $categoria->getPublicaciones();
            $portada = $publicaciones[0];
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));*/
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' =>'herramientas-para-el-comercio'));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }
        
    }
    
    /**
     * @Route("/comisiones-de-trabajo", name="frontend_comisiones_trabajo")
     * @Route("/comisiones-de-trabajo/{categoriaSlug}", name="frontend_comisiones_trabajo_categoria")
     * @Route("/comisiones-de-trabajo/{categoriaSlug}/{publicacionSlug}", name="frontend_comisiones_trabajo_publicacion")
     * @Method({"GET"})
     */
    public function comisionesTrabajoAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_comisiones_trabajo';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            $adicionales = $em->getRepository('PublicacionesBundle:Adicional')
                              ->findBy(array('publicacion'=>$publicacion));
            
            return $this->render('FrontendBundle:Default:comision.html.twig', compact('categoria','publicacion','rutaBase','adicionales'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            $adicionales = $em->getRepository('PublicacionesBundle:Adicional')
                              ->findBy(array('publicacion'=>$publicacion));
            return $this->render('FrontendBundle:Default:comision.html.twig', compact('categoria','publicacion','rutaBase','adicionales'));
        }else{
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'comisiones-de-trabajo'));
            $publicaciones = $categoria->getPublicaciones();
            $portada = $publicaciones[0];
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));
            
        }
        
    }
    
    /**
     * @Route("/grupos-especializados", name="frontend_grupos_especializados")
     * @Route("/grupos-especializados/{categoriaSlug}", name="frontend_grupos_especializados_categoria")
     * @Route("/grupos-especializados/{categoriaSlug}/{publicacionSlug}", name="frontend_grupos_especializados_publicacion")
     * @Method({"GET"})
     */
    public function gruposEspecializadosAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_grupos_especializados';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }else{
            /*$categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'grupos-especializados'));
            $publicaciones = $categoria->getPublicaciones();
            $portada = $publicaciones[0];
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));*/
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => 'sobre-el-grupo-asesor-g20'));
            $categoria = $publicacion->getCategoria();
            $rutaBase = 'frontend_influencia_global';
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }
        
    }
    
    /**
     * @Route("/posturas", name="frontend_posturas")
     * @Route("/posturas/{categoriaSlug}", name="frontend_posturas_categoria")
     * @Route("/posturas/{categoriaSlug}/{publicacionSlug}", name="frontend_posturas_publicacion")
     * @Method({"GET"})
     */
    public function posturasAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_posturas';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }else{
            /*$categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'posturas'));
            $publicaciones = $categoria->getPublicaciones();
            $portada = $publicaciones[0];
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));*/
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' =>'posicionamientos'));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }
        
    }
    
    /**
     * @Route("/noticias", name="frontend_noticias")
     * @Route("/noticias/{categoriaSlug}", name="frontend_noticias_categoria")
     * @Route("/noticias/{categoriaSlug}/{publicacionSlug}", name="frontend_noticias_publicacion")
     * @Method({"GET"})
     */
    public function noticiasAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_noticias';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:noticia.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            if(count($publicaciones)>0){
                $publicacion = $publicaciones[0];
            }
            $publicacion = $publicaciones[0];
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:noticias.html.twig', compact('categoria','publicaciones','publicacion','rutaBase'));
        }else{
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'noticias'));
            $publicaciones = $categoria->getPublicaciones();
            $portada = $publicaciones[0];
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));
        }
    }
    
    /**
     * @Route("/eventos", name="frontend_eventos")
     * @Route("/eventos/{categoriaSlug}", name="frontend_eventos_categoria")
     * @Route("/eventos/{categoriaSlug}/{publicacionSlug}", name="frontend_eventos_publicacion")
     * @Method({"GET"})
     */
    public function eventosAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_eventos';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:evento.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif($categoriaSlug=='calendario-icc-mexico' || $categoriaSlug == 'calendario-icc-mundial'){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoriaSlug);
            $fechaActual = new \DateTime();
            $year = $request->query->get('year', $fechaActual->format('Y'));
            $month = $request->query->get('month', $fechaActual->format('m'));
            $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                                ->getEventosForFecha($year,$month,$categoria->getId());
            $nombreMes = $this->getNombreMes($month);
            return $this->render('FrontendBundle:Default:calendario.html.twig', compact('categoria','publicaciones','rutaBase','year','month','nombreMes'));
        }elseif($categoriaSlug=='patrocinios'){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoriaSlug);
            
            $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                                ->getEventosEnPatrocinio();
            return $this->render('FrontendBundle:Default:patrocinios.html.twig', compact('categoria','publicaciones','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            if(count($publicaciones)>0){
                $publicacion = $publicaciones[0];
            }else{
                $publicacion = null;
            }
            
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                            ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:eventos.html.twig', compact('categoria','publicaciones','rutaBase'));
        }else{
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'eventos'));
            $publicaciones = $categoria->getPublicaciones();
            if(count($publicaciones)){
                $portada = $publicaciones[0];
            }else{
                $portada = null;
            }
            
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));
        }
    }
    
    /**
     * @Route("/afiliacion", name="frontend_afiliacion")
     * @Route("/afiliacion/{categoriaSlug}", name="frontend_afiliacion_categoria")
     * @Route("/afiliacion/{categoriaSlug}/{publicacionSlug}", name="frontend_afiliacion_publicacion")
     * @Method({"GET"})
     */
    public function afiliacionAction(Request $request, $categoriaSlug = '', $publicacionSlug = '') {
        $em = $this->getDoctrine()->getManager();
        $rutaBase = 'frontend_afiliacion';
        if(strlen($categoriaSlug)>0 && strlen($publicacionSlug)>0){
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                              ->findOneBy(array('slug' => $publicacionSlug));
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }elseif(strlen($categoriaSlug)>0){
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $categoriaSlug));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }else{
            /*$categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => 'afiliacion'));
            $publicaciones = $categoria->getPublicaciones();
            $portada = $publicaciones[0];
            return $this->render('FrontendBundle:Default:portada.html.twig',  compact('portada','rutaBase'));*/
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' =>'afiliacin-2'));
            $publicaciones = $categoria->getPublicaciones();
            $publicacion = $publicaciones[0];
            $categoria = $publicacion->getCategoria();
            while($categoria->getNivel()>1){
                $categoria = $categoria->getParent();
            }
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findCategoriaForSlug($categoria->getSlug());
            return $this->render('FrontendBundle:Default:publicacion.html.twig', compact('categoria','publicacion','rutaBase'));
        }
    }
    
    
    
    /**
     * @Route("/registro/newsletter", name="frontend_newsletter")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function newsletterAction(Request $request) {
        $newsletter = new Newsletter();
        $form = $this->createForm(new NewsletterType(), $newsletter);
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $registro = $em->getRepository('BackendBundle:Newsletter')
                           ->findOneBy(array('email'=>$newsletter->getEmail()));
                if($registro==null){
                    $em->persist($newsletter);
                    $em->flush();
                }else{
                    $newsletter=null;
                    $em->flush();
                }
                
                $ok = true;
                $error = false;
                $mensaje = "Se ha registrado el email";
                
                $newsletter = new Newsletter();
                $form = $this->createForm(new NewsletterType(), $newsletter);
            } else {
                $ok = false;
                $error = true;
                $mensaje = "No se pudo generar el registro";
            }
        } else {
            $ok = false;
            $error = false;
            $mensaje = "";
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('FrontendBundle:Default:formNewsletter.html.twig', array(
                        'form' => $form->createView(),
                        'ok' => $ok,
                        'error' => $error,
                        'mensaje' => $mensaje,
            ));
        }

        $pagina = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina' => 'newsletter'));

        return $this->render('FrontendBundle:Default:newsletter.html.twig', array(
                    'form' => $form->createView(),
                    'ok' => $ok,
                    'error' => $error,
                    'mensaje' => $mensaje,
                    'pagina' => $pagina,
        ));
    }
    
    /**
     * @Route("/libreria", name="frontend_libreria")
     * @Template()
     * @Method({"GET"})
     */
    public function libreriaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $libreria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                         ->findOneBy(array('slug'=>'libreria-1'));
        
        $pagina = $em->getRepository('PaginasBundle:Pagina')
                     ->findOneBy(array('pagina'=>'libreria'));
        return array(
            'libreria'=>$libreria,
            'pagina'=>$pagina,
        );
    }
    
    /**
     * @Route("/patrocinio", name="frontend_patrocinio")
     * @Template()
     * @Method({"GET"})
     */
    public function patrocinioAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        return array();
    }
    
    /**
     * @Route("/contacto", name="frontend_contacto")
     * @Method({"GET", "POST"})
     */
    public function contactoAction(Request $request) {
        $contacto = new Contacto();
        $form = $this->createForm(new ContactoType(), $contacto);
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $datos = $form->getData();
                $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                        ->findOneBy(array('slug' => 'emailcontacto'));
                $message = \Swift_Message::newInstance()
                        ->setSubject('Contacto desde pagina')
                        ->setFrom($datos->getEmail())
                        ->setTo($configuracion->getTexto())
                        ->setBody($this->renderView('FrontendBundle:Default:contactoEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);
                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok = true;
                $error = false;
                $mensaje = "Se ha enviado el mensaje";
                $contacto = new Contacto();
                $form = $this->createForm(new ContactoType(), $contacto);
            } else {
                $ok = false;
                $error = true;
                $mensaje = "El mensaje no se ha podido enviar";
            }
        } else {
            $ok = false;
            $error = false;
            $mensaje = "";
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('FrontendBundle:Default:formContacto.html.twig', array(
                        'form' => $form->createView(),
                        'ok' => $ok,
                        'error' => $error,
                        'mensaje' => $mensaje,
            ));
        }

        $pagina = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina' => 'contacto'));

        return $this->render('FrontendBundle:Default:contacto.html.twig', array(
                    'form' => $form->createView(),
                    'ok' => $ok,
                    'error' => $error,
                    'mensaje' => $mensaje,
                    'pagina' => $pagina,
        ));
    }

    
    /**
     * @Route("/alertas", name="frontend_alertas")
     * @Template()
     * @Method({"GET"})
     */
    public function alertasAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        return array();
    }
    
    
    /**
     * @Route("/buscador", name="frontend_buscador")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function buscadorAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $buscar = $request->get("q","");
	$noticias = $em->getRepository('PublicacionesBundle:Publicacion')
                       ->buscarPublicacionPorTipoCategoria($buscar,CategoriaPublicacion::TIPO_CATEGORIA_NOTICIAS);
        
        $eventos = $em->getRepository('PublicacionesBundle:Publicacion')
                       ->buscarPublicacionPorTipoCategoria($buscar,CategoriaPublicacion::TIPO_CATEGORIA_EVENTOS);
	
        $pagina = $em->getRepository('PaginasBundle:Pagina')
                     ->findOneBy(array('pagina'=>'buscador'));	
        
        return array(
            'noticias'  => $noticias,
            'eventos'   => $eventos,
            'pagina'    => $pagina,
        );
        
    }
    
    public function getNombreMes($month){
        switch($month){
            case 1: return "Enero";
            case 2: return "Febrero";
            case 3: return "Marzo";
            case 4: return "Abril";
            case 5: return "Mayo";
            case 6: return "Junio";
            case 7: return "Julio";
            case 8: return "Agosto";
            case 9: return "Septiembre";
            case 10: return "Octubre";
            case 11: return "Noviembre";
            case 12: return "Diciembre";
        }
    }
    
    /**
     * @Route("/solicitar/pedido/{id}",name="solicitar_pedido")
     * @Method({"GET","POST"})
     */
    public function getSolicitarPedido(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);
        
        $contacto = new Contacto();
        $contacto->setBody('Solicitud de pedido de libreria');
        $form = $this->createForm(new SolicitarPedidoType(), $contacto);
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $datos = $form->getData();
                $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                        ->findOneBy(array('slug' => 'emailpedidolibreria'));
                $message = \Swift_Message::newInstance()
                        ->setSubject('Pedido de libreria')
                        ->setFrom($datos->getEmail())
                        ->setTo($configuracion->getTexto())
                        ->setBody($this->renderView('FrontendBundle:Default:pedidoEmail.html.twig', array('datos' => $datos,'producto'=>$producto)), 'text/html');
                $this->get('mailer')->send($message);
                
                $contacto = new Contacto();
                $contacto->setBody('Solicitud de pedido de libreria');
                $form = $this->createForm(new SolicitarPedidoType(), $contacto);    
                // Redirige - Esto es importante para prevenir que el usuario
                $response = new JsonResponse(json_encode(array(
                    'form' => $this->renderView('FrontendBundle:Default:formPedido.html.twig', array(
                        'rutaAction' => $this->generateUrl('solicitar_pedido',array('id'=>$producto->getId())),
                        'form'=>$form->createView(),
                        'producto'=>$producto,
                     )),
                    'respuesta' => 'creado',
                )));
                return $response;
            }
        }
        
        $response = new JsonResponse(json_encode(array(
            'form' => $this->renderView('FrontendBundle:Default:formPedido.html.twig', array(
                'rutaAction' => $this->generateUrl('solicitar_pedido',array('id'=>$producto->getId())),
                'form'=>$form->createView(),
                'producto'=>$producto,
             )),
            'respuesta' => 'nuevo',
        )));
        return $response;
    }
}

