<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProvinceController extends FOSRestController
{
    /**
     * @Rest\Get("/provinces")
     */
    public function indexAction() {
        $provinces = $this->get('app.user_service')->getProvinceList();

        $data = [];
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