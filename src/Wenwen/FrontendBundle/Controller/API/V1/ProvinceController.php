<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\RestAuthenticatedController;

class ProvinceController extends RestAuthenticatedController
{
    /**
     * @Rest\Get("/provinces")
     */
    public function indexAction() {
        $provinces = $this->get('app.user_service')->getProvinceList();

        return $this->view([
            'status' => 'success',
            'data' => $provinces,
        ], 200);
    }
}