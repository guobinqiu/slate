<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class ProvinceController extends TokenAuthenticatedFOSRestController
{
    /**
     * @Rest\Get("/provinces")
     */
    public function indexAction() 
    {
        $provinces = $this->get('app.user_service')->getProvinceList();
        return $this->view(ApiUtil::formatSuccess($provinces), HttpStatus::HTTP_OK);
    }
}