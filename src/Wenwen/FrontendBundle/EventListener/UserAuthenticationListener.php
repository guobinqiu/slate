<?php

namespace Wenwen\FrontendBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Controller\UserAuthenticationController;

class UserAuthenticationListener
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof UserAuthController) {
            $request = $event->getRequest();

            if (!$request->getSession()->has('uid')) {
                $redirectUrl = $this->router->generate('_user_login');

                $event->setController(function() use ($redirectUrl) {
                    return new RedirectResponse($redirectUrl);
                });
            }
        }
    }
}