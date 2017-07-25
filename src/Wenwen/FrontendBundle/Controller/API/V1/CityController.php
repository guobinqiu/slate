<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\MyFOSRestController;

class CityController extends MyFOSRestController
{
    /**
     * @Rest\Get("/provinces/{province_id}/cities")
     */
    public function indexAction($province_id) {
        $cities = $this->getDoctrine()->getRepository('WenwenFrontendBundle:CityList')->getCitiesByProvinceId($province_id);

        $data = [];
        if (!empty($cities)) {
            $data['status'] = 'success';
            $data['data'] = $cities;
        } else {
            $data['status'] = 'error';
            $data['message'] = 'no data';
        }

        return $this->view($data, 200);
    }
}