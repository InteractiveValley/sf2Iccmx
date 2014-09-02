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
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;
use Richpolis\ComentariosBundle\Entity\Comentario;
use Richpolis\ComentariosBundle\Form\ComentarioConImagenType;
use Richpolis\ComentariosBundle\Form\ComentarioType;
use Richpolis\PublicidadBundle\Entity\Publicidad;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Richpolis\BackendBundle\Utils\Youtube;

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
     * @Route("/", name="portada")
     * @Template()
     */
    public function portadaAction() {

        $em = $this->getDoctrine()->getManager();
        $portadas = $em->getRepository('PublicacionesBundle:Publicacion')
                ->findPortada();
        $llamados = $em->getRepository('PublicacionesBundle:Publicacion')
                ->getPublicacionesPorTipoCategoria(
                Publicacion::STATUS_PUBLICADO, CategoriaPublicacion::TIPO_CATEGORIA_LLAMADOS
        );

        return array(
            'publicaciones' => $portadas,
            'llamados' => $llamados
        );
    }

    /**
     * @Route("/inicio", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $carrusel = $em->getRepository('PublicacionesBundle:Publicacion')
                ->findCarrusel();

        $categorias = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->getCategoriasConPublicaciones(6);

        $publicaciones = $em->getRepository('PublicacionesBundle:Publicacion')
                ->getUltimasPublicaciones(4);

        return array(
            'carrusel' => $carrusel,
            'categorias' => $categorias,
            'ultimasPublicaciones' => $publicaciones
        );
    }

    /**
     * @Route("/categoria/{slug}", name="frontend_categoria")
     * @Template()
     */
    public function categoriaAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->findOneBy(array('slug' => $slug));
        $query = $em->getRepository('PublicacionesBundle:Publicacion')
                ->queryPorCategoria($slug);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, 
            $this->get('request')->query->get('page', 1),
            10,
            array()
        );
        return array(
            'categoria' => $categoria,
            'pagination' => $pagination,
        );
        
    }

    /**
     * @Route("/preview/publicacion/{slug}", name="frontend_preview_publicaciones")
     * @Method({"GET"})
     */
    public function previewPublicacionAction(Request $request, $slug) {
        $em = $this->getDoctrine()->getManager();
        
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                ->findOneBy(array('slug' => $slug));
		
        $contar = $request->query->get('contar', false);
        
		if($publicacion->getCategoria()->getTipoCategoria()==CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION){
        	return $this->redirect($this->generateUrl('frontend_publicaciones', array('slug' => $slug, 'contar' => $contar)));
		} elseif ($publicacion->getCategoria()->getTipoCategoria() == CategoriaPublicacion::TIPO_CATEGORIA_HERALDO_TV) {
            return $this->redirect($this->generateUrl('frontend_heraldo_tv', array('slug' => $slug, 'contar' => $contar)));
        } elseif ($publicacion->getCategoria()->getTipoCategoria() == CategoriaPublicacion::TIPO_CATEGORIA_TU_ESPACIO) {
            return $this->redirect($this->generateUrl('frontend_tu_espacio', array('slug' => $slug, 'contar' => $contar)));
        }
		
		return $this->redirect($this->generateUrl('frontend_publicaciones', array('slug' => $slug, 'contar' => $contar)));
    }
	
	/**
     * @Route("/publicacion/{slug}", name="frontend_publicaciones")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function publicacionAction(Request $request, $slug) {
        $em = $this->getDoctrine()->getManager();
        
        $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                ->findOneBy(array('slug' => $slug));
		
        $contar = $request->query->get('contar', true);
        
        if ($publicacion->getCategoria()->getTipoCategoria() == CategoriaPublicacion::TIPO_CATEGORIA_HERALDO_TV) {
            return $this->redirect($this->generateUrl('frontend_heraldo_tv', array('slug' => $slug, 'contar' => $contar)));
        } elseif ($publicacion->getCategoria()->getTipoCategoria() == CategoriaPublicacion::TIPO_CATEGORIA_TU_ESPACIO) {
            return $this->redirect($this->generateUrl('frontend_tu_espacio', array('slug' => $slug, 'contar' => $contar)));
        }

        //$parent = $request->query->get('parent', 0);
        $comentario = new Comentario();
        $comentario->setPublicacion($publicacion);

        /*if ($parent > 0) {
            $comentarioParent = $em->getRepository('ComentariosBundle:Comentario')->find($parent);
            $comentario->setParent($comentarioParent);
        }*/

        $form = $this->createForm(new ComentarioType(), $comentario, array('em' => $em));

        if ($request->isMethod('GET')) {
            if ($contar) {
                $publicacionesSession = $this->getValoresSession('publicaciones');
                if (!isset($publicacionesSession[$publicacion->getSlug()])) {
                    $publicacion->setContVisitas($publicacion->getContVisitas() + 1);
                    $em->flush();
                    $publicacionesSession[$publicacion->getSlug()] = true;
                    $this->setVAloresSession('publicaciones',$publicacionesSession);
                }
            }
        } elseif ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comentario);
                $publicacion->setContComentarios($publicacion->getContComentarios() + 1);
                $em->flush();
                $comentario = new Comentario();
                $comentario->setPublicacion($publicacion);
                $form = $this->createForm(new ComentarioType(), $comentario, array('em' => $em));
            }
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('FrontendBundle:Default:form.html.twig', array('form' => $form->createView()));
        }
        $comentarios = $em->getRepository('ComentariosBundle:Comentario')
                ->findBy(array('publicacion' => $publicacion), array('createdAt' => 'ASC'));
		
		$categoria = $publicacion->getCategoria();

		$relacionados = $em->getRepository('PublicacionesBundle:Publicacion')
							   ->getPublicacionesRelacionadas($categoria);
		
        return array(
            'categoria' => $categoria,
	    'relacionados' => $relacionados,
            'publicacion' => $publicacion,
            'comentarios' => $comentarios,
            'form' => $form->createView(),
            'contar'=>$contar,
        );
    }

    /**
     * @Route("/heraldo/tv/{slug}", name="frontend_heraldo_tv")
     * @Template("FrontendBundle:Default:heraldoTv.html.twig")
     */
    public function heraldoTvAction(Request $request, $slug) {
        $em = $this->getDoctrine()->getManager();
        if ($slug != "ultimo") {
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                    ->findOneBy(array('slug' => $slug));
        } else {
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                    ->findOneBy(array('tipoCategoria' => CategoriaPublicacion::TIPO_CATEGORIA_HERALDO_TV));
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                    ->findOneBy(
                    array('categoria' => $categoria, 'status' => Publicacion::STATUS_PUBLICADO), array('createdAt' => 'DESC')
            );
        }
		
        $contar = $request->query->get('contar', true);

        if ($contar) {
            $publicacionesSession = $this->getValoresSession('publicaciones');
            if (!isset($publicacionesSession[$publicacion->getSlug()])) {
                $publicacion->setContVisitas($publicacion->getContVisitas() + 1);
                $em->flush();
                $publicacionesSession[$publicacion->getSlug()] = true;
                $this->setVAloresSession('publicaciones', $publicacionesSession);
            }
        }

        return array(
            'categoria' => $publicacion->getCategoria(),
            'publicacion' => $publicacion
        );
    }

    /**
     * @Route("/tu/espacio/{slug}", name="frontend_tu_espacio")
     * @Template("FrontendBundle:Default:tuEspacio.html.twig")
     * @Method({"GET","POST"})
     */
    public function tuEspacioAction(Request $request, $slug) {
        $em = $this->getDoctrine()->getManager();

        if ($slug != "ultimo") {
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                    ->findOneBy(array('slug' => $slug));
        } else {
            $categoria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                    ->findOneBy(array('tipoCategoria' => CategoriaPublicacion::TIPO_CATEGORIA_TU_ESPACIO));
            $publicacion = $em->getRepository('PublicacionesBundle:Publicacion')
                    ->findOneBy(
                    array('categoria' => $categoria, 'status' => Publicacion::STATUS_PUBLICADO), array('createdAt' => 'DESC')
            );
        }

        $contar = $request->query->get('contar', true);
		
        $comentario = new Comentario();
        $comentario->setPublicacion($publicacion);
        $form = $this->createForm(new ComentarioConImagenType(), $comentario, array('em' => $em));
        if ($request->isMethod('GET')) {
            if ($contar) {
                $publicacionesSession = $this->getValoresSession('publicaciones');
                if (!isset($publicacionesSession[$publicacion->getSlug()])) {
                    $publicacion->setContVisitas($publicacion->getContVisitas() + 1);
                    $em->flush();
                    $publicacionesSession[$publicacion->getSlug()] = true;
                    $this->setVAloresSession('publicaciones', $publicacionesSession);
                }
            }
        } elseif ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comentario);
                $publicacion->setContComentarios($publicacion->getContComentarios() + 1);
                $em->flush();
                $comentario = new Comentario();
                $comentario->setPublicacion($publicacion);
                $form = $this->createForm(new ComentarioConImagenType(), $comentario, array('em' => $em));
            }
        }

        $comentarios = $em->getRepository('ComentariosBundle:Comentario')
                ->findBy(array('publicacion' => $publicacion), array('createdAt' => 'DESC'));

        return array(
            'categoria' => $publicacion->getCategoria(),
            'publicacion' => $publicacion,
            'comentarios' => $comentarios,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/nosotros", name="frontend_nosotros")
     * @Template("FrontendBundle:Default:estatica.html.twig")
     */
    public function nosotrosAction() {
        $em = $this->getDoctrine()->getManager();
        $nosotros = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina' => 'nosotros'));
        return array(
            'pagina' => $nosotros
        );
    }

    /**
     * @Route("/historia", name="frontend_historia")
     * @Template("FrontendBundle:Default:estatica.html.twig")
     */
    public function historiaAction() {
        $em = $this->getDoctrine()->getManager();
        $historia = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina' => 'historia'));
        return array(
            'pagina' => $historia
        );
    }

    /**
     * @Route("/edicion/pdf", name="frontend_edicion_pdf")
     * @Template("FrontendBundle:Default:estatica.html.twig")
     */
    public function edicionPdfAction() {
        $em = $this->getDoctrine()->getManager();
        $edicionPdf = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina' => 'edicion-pdf'));
        return array(
            'pagina' => $edicionPdf
        );
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
                        ->findOneBy(array('slug' => 'email-contacto'));
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
     * @Route("/publicidad/head", name="frontend_publicidad_encabezado")
     * @Template("FrontendBundle:Default:publicidadHead.html.twig")
     */
    public function publicidadHeadAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        return array(
            'publicidadArray' => $this->getPublicidadEnSession($em),
        );
    }
    
    /**
     * @Route("/aside", name="frontend_aside")
     * @Template()
     */
    public function asideAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        return array(
            'publicidadArray' => $this->getPublicidadEnSession($em),
            'lomasvistos' => $this->getLosmasVistosEnSession($em),
            'lomascomentados' => $this->getLosmasComentadosEnSession($em),
            'categorias' => $this->getCategoriasEnSession($em),
            'ultimasPublicaciones' => $this->getUltimasPublicaciones($em),
        );
    }
    
    /**
     * @Route("/buscardor", name="frontend_buscador")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function buscadorAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $buscar = $request->get("textoBuscar","");
		$query = $em->getRepository('PublicacionesBundle:Publicacion')
                ->queryBuscarPublicacion($buscar);
        if(strlen($buscar)>0){
          $options = array('filterParam'=>'buscar','filterValue'=>$buscar);
        }else{
          $options = array();
        }
		$paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, 
            $this->get('request')->query->get('page', 1),
            8,
            $options
        );
		
        return array(
            'pagination' => $pagination,
        );
        
    }
	
    /**
     * @Route("/archivos", name="frontend_archivos")
     * @Template()
     */
    public function archivosAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
		return array(
            'categorias' => $this->getCategoriasEnSession($em),
        );
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
     * @Route("/prueba/video", name="frontend_prueba_video")
     * @Template()
     */
    public function pruebaAction() 
    {
        $link=RpsStms::getLinkLargeYoutube('http://youtu.be/eieuKNEw2Mw');
        $videoId = RpsStms::getVideoIdYoutube($link);
        $video = new Youtube($videoId);
        $response = new JsonResponse();
        $response->setData(array('titulo'=>$video->getTitle()));
        return $response;
    }
}
