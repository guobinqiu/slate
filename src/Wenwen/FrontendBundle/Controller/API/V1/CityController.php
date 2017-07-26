<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\RestAuthenticatedController;

class CityController extends RestAuthenticatedController
{
    /**
     * @Rest\Get("/provinces/{province_id}/cities")
     */
    public function indexAction($province_id) {
        $cities = $this->getDoctrine()->getRepository('WenwenFrontendBundle:CityList')->getCitiesByProvinceId($province_id);

        if (!empty($cities)) {
            $data['status'] = 'success';
            $data['data'] = $cities;
            return $this->view($data, 200);
        } else {
            $data['status'] = 'error';
            $data['message'] = 'no data';
            return $this->view($data, 400);
        }
    }
}