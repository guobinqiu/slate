<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;
use Wenwen\FrontendBundle\Annotation\API\NeedLogin;

class SurveyController extends TokenAuthenticatedFOSRestController
{
    /**
     * @Rest\Get("/surveys")
     * @NeedLogin
     */
    public function indexAction(Request $request) 
    {
        $surveys[] = 'survey1';
        $surveys[] = 'survey2';
        $surveys[] = 'survey3';
        $surveys[] = 'survey4';
        $surveys[] = 'survey5';

        return $this->view(ApiUtil::formatSuccess($surveys), HttpStatus::HTTP_OK);
    }
}