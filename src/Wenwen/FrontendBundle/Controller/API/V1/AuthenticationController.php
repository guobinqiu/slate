<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Form\API\V1\UserDeviceLoginType;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class AuthenticationController extends TokenAuthenticatedFOSRestController
{
    /**
     * @Rest\Post("/authentications/device")
     */
    public function userDeviceLoginAction(Request $request)
    {
        $form = $this->createForm(new UserDeviceLoginType());
        $form->bind($request);
        if ($form->isValid()) {
            $authenticationService = $this->get('api.authentication_service');
            $data = $authenticationService->userDeviceLogin($form->getData());
            return $this->view(ApiUtil::formatSuccess($data), HttpStatus::HTTP_CREATED);
        }
        return $this->view(ApiUtil::formatError($this->getErrorMessages($form)), HttpStatus::HTTP_BAD_REQUEST);
    }
}