<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Wenwen\FrontendBundle\Model\API\ApiUtil;

class CorsListener
{
    public function onKernelResponse(FilterResponseEvent $event) {

        $allowHeaders[] = ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN;
        $allowHeaders[] = ApiUtil::HTTP_HEADER_TIMESTAMP;
        $allowHeaders[] = ApiUtil::HTTP_HEADER_NONCE;
        $allowHeaders[] = ApiUtil::HTTP_HEADER_USER_ACCESS_TOKEN;

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowHeaders));
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH');

        $event->setResponse($response);
    }
}