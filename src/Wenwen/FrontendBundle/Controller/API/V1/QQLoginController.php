<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class QQLoginController extends FOSRestController
{
    /**
     * @Rest\Get("/qq/callback")
     */
    public function callbackAction() 
    {
        return $this->view(ApiUtil::formatSuccess('success'), HttpStatus::HTTP_OK);
    }
}