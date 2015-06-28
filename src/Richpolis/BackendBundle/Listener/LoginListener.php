<?php

namespace Richpolis\BackendBundle\Listener;
 
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
 
class LoginListener
{
    private $contexto, $router, $usuario;
 
    public function __construct(SecurityContext $context, Router $router)
    {
        $this->contexto = $context;
        $this->router   = $router;
    }
 
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $this->usuario = $token->getUser();
    }
 
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if(null != $this->usuario){
            if ($this->contexto->isGranted('ROLE_ADMIN')) {
                $pagina = $this->router->generate('backend_homepage');
            }elseif ($this->contexto->isGranted('ROLE_ICC')) {
                $pagina = $this->router->generate('publicaciones_categoria',array('slug'=>'calendario-icc-mexico'));
            }else{
                $pagina = $this->router->generate('vip_pauta');
            }
            if(strlen($pagina)>0){
                $event->setResponse(new RedirectResponse($pagina));
                $event->stopPropagation();
            }
        }
    }
}

