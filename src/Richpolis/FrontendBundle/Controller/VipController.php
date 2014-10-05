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

class VipController extends Controller {
   
    /**
     * @Route("/vip/pauta", name="frontend_pauta")
     * @Template()
     * @Method({"GET"})
     */
    public function pautaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        return array();
        
    }
    
    
}

