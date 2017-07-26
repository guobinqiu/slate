<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

        if (!empty($provinces)) {
            $data['status'] = 'success';
            $data['data'] = $provinces;
        } else {
            $data['status'] = 'error';
            $data['message'] = 'no data';
        }

        return $this->view($data, 200);
    }
}