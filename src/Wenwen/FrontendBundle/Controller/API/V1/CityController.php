<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\Status;

class CityController extends TokenAuthenticatedFOSRestController
{
    /**
     * @Rest\Get("/provinces/{province_id}/cities")
     */
    public function indexAction($province_id) {
        $cities = $this->getDoctrine()->getRepository('WenwenFrontendBundle:CityList')->getCitiesByProvinceId($province_id);
        return $this->view(ApiUtil::formatSuccess($cities), Status::HTTP_OK);
    }
}