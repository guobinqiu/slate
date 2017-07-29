<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Wenwen\FrontendBundle\Model\API\ApiUtils;

class CorsListener
{
    public function onKernelResponse(FilterResponseEvent $event) {

        $allowHeaders[] = ApiUtils::HTTP_HEADER_AUTHORIZATION;
        $allowHeaders[] = ApiUtils::HTTP_HEADER_TIMESTAMP;
        $allowHeaders[] = ApiUtils::HTTP_HEADER_NONCE;
        $allowHeaders[] = ApiUtils::HTTP_HEADER_LOGIN_TOKEN;

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowHeaders));
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH');

        $event->setResponse($response);
    }
}