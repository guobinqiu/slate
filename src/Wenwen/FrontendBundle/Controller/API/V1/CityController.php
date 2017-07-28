<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\RestAuthenticatedController;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;

class CityController extends RestAuthenticatedController
{
    /**
     * @Rest\Get("/provinces/{province_id}/cities")
     */
    public function indexAction($province_id) {
        $cities = $this->getDoctrine()->getRepository('WenwenFrontendBundle:CityList')->getCitiesByProvinceId($province_id);
        return $this->view(ApiUtils::formatSuccess($cities), Status::HTTP_OK);
    }
}