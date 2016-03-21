<?php

namespace Jili\BackendBundle\EventListener;

use Jili\BackendBundle\Controller\IpAuthenticatedController;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class IpListener
{
    private $white_ips;
    private $container_;

    public function __construct(array $ips)
    {
        $this->white_ips = $ips;
    }

    public function onKernelController(FilterControllerEvent $event)
    {

        $controller = $event->getController();


        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof IpAuthenticatedController ) {

            $ip = $event->getRequest()->getClientIp() ;

            if (!in_array($ip, $this->white_ips ) &&  ! in_array( $this->container_->get('kernel')->getEnvironment() , array('dev', 'test')) ) {
                throw new AccessDeniedHttpException('Access denined to ' . $ip);
            }
        }
    }

    public function setContainer($c)
    {
        $this->container_ = $c;
    }
    #private function getAdminIp() {
    #    if(
    #        $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_un_ip') ||
    #        $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||
    #        substr( $_SERVER['REMOTE_ADDR'],0,10)  == '192.168.1.' ||
    #        $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_vpn_ip'))
    #        return false;
    #    else
    #        return true;

    #}
}
