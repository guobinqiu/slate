<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\AuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;
use Wenwen\FrontendBundle\Annotation\API\NeedLoginToken;

class SurveyController extends AuthenticatedFOSRestController
{
    /**
     * @Rest\Get("/surveys")
     *
     * @NeedLoginToken
     */
    public function indexAction(Request $request) {
        $surveys[] = 'survey1';
        $surveys[] = 'survey2';
        $surveys[] = 'survey3';
        $surveys[] = 'survey4';
        $surveys[] = 'survey5';

        return $this->view(ApiUtils::formatSuccess($surveys), Status::HTTP_OK);
    }
}