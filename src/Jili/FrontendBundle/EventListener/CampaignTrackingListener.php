<?php

namespace Jili\FrontendBundle\EventListener;

use Jili\FrontendBundle\Controller\CampaignTrackingController;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class CampaignTrackingListener
{

    private $session;

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }
        if ($controller[0] instanceof CampaignTrackingController) {
            if( $event->getRequest()->query->has('c') ) {
                $campaign_code= $event->getRequest()->query->get('c');
                $this->session->set('campaign_code' , $campaign_code);

                if( $event->getRequest()->query->has('c')) {
                    $campaign_code_token = $event->getRequest()->query->has('c_token');
                    $this->session->set('campaign_code_token' , $campaign_code_token);
                }

            } 
        }
    }

    public function setSession($session) 
    {
        $this->session = $session;
    }
}
