<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;

class VipController extends Controller {
   
    /**
     * @Route("/vip/login", name="vip_login")
     * @Template()
     * @Method({"GET"})
     */
    public function loginAction()
    {
        $em = $this->getDoctrine()->getManager();
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        
 
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        
        $pagina = $em->getRepository('PaginasBundle:Pagina')
                     ->findOneBy(array('pagina'=>'login-vip'));
        
        return $this->render('FrontendBundle:Vip:login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
            'pagina'=>$pagina,
        ));
    }
    
    /**
     * @Route("/vip/login_check", name="vip_login_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/vip/logout", name="vip_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    
    /**
     * @Route("/vip/pauta", name="vip_pauta")
     * @Template()
     * @Method({"GET"})
     */
    public function pautaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
       $libreria = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                         ->findOneBy(array('slug'=>'pauta'));
        
        $pagina = $em->getRepository('PaginasBundle:Pagina')
                     ->findOneBy(array('pagina'=>'pauta'));
        return array(
            'libreria'=>$libreria,
            'pagina'=>$pagina,
        );
        
    }
    
    
}

