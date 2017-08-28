<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CorsListener
{
    const X_APP_ACCESS_TOKEN = 'X-App-Access-Token';
    const X_TIMESTAMP = 'X-Timestamp';
    const X_NONCE = 'X-Nonce';
    const X_USER_ACCESS_TOKEN = 'X-User-Access-Token';

    public function onKernelResponse(FilterResponseEvent $event) 
    {

        $allowHeaders[] = self::X_APP_ACCESS_TOKEN;
        $allowHeaders[] = self::X_TIMESTAMP;
        $allowHeaders[] = self::X_NONCE;
        $allowHeaders[] = self::X_USER_ACCESS_TOKEN;

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', implode(',', $allowHeaders));
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE');

        $event->setResponse($response);
    }
}