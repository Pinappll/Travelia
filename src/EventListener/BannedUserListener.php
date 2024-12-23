<?php
// src/EventListener/BannedUserListener.php
namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class BannedUserListener
{
    private $security;
    private $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        // Vérifie si l'utilisateur est authentifié et possède le rôle ROLE_BANNED
        if ($this->security->isGranted('ROLE_BANNED')) {
            // Si l'utilisateur tente d'accéder à une autre page que /banned
            if ($currentRoute !== 'app_banned') {
                $response = new RedirectResponse($this->router->generate('app_banned'));
                $event->setResponse($response);
            }
        }
    }
}
