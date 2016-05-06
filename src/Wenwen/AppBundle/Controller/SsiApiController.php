<?php

namespace Wenwen\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VendorIntegration\SSI\PC1\Request as SsiRequest;
use VendorIntegration\SSI\PC1\RequestValidator as SsiRequestValidator;
use VendorIntegration\SSI\PC1\RequestHandler as SsiRequestHandler;

class SsiApiController extends Controller
{
    const SUCCESS_STATUS_CODE = '201';
    const ERROR_STATUS_CODE = '202';
    const UNSUBSCRIBDED_STATUS_CODE = '203';

    /**
     * @Route("/ssi_pc1_protocol/request_api")
     */
    public function handleRequestAction(Request $request)
    {
        $ssiRequest = new SsiRequest();
        $ssiRequest->loadJson($request->getContent());

        # validation
        $validator = new SsiRequestValidator($ssiRequest);
        $validator->validate();
        if (!$validator->isValid()) {
            $response = new JsonResponse();
            $response->setData(['generalResponseCode' => self::ERROR_STATUS_CODE]);

            return $response;
        }
        $em = $this->getDoctrine()->getManager();

        # Save DB
        $handler = new SsiRequestHandler($em->getConnection());
        $handler->setUpProject($ssiRequest);
        $handler->setUpProjectRespondents($ssiRequest);

        # send mail
        if (sizeof($handler->getSucceededRespondentIds())) {
            $notification = new \Wenwen\AppBundle\Services\Notification\SurveyDelivery\SsiProject(
                $handler->getSucceededRespondentIds(),
                $em,
                $this->container
            );
            $recipients = $notification->retrieveRecipientsToMail();
            $notification->sendMailing(100, $recipients);
        }

        return $this->createResponse(
            $handler->getSucceededRespondentIds(),
            $handler->getFailedRespondentIds(),
            $handler->getUnsubscribedRespondentIds()
        );
    }

    private function createResponse($succeededRespondentIds, $failedRespondentIds, $unsubscribedRespondentIds)
    {
        $response = new JsonResponse();

        if (sizeof($succeededRespondentIds)) {
            $res = ['generalResponseCode' => self::SUCCESS_STATUS_CODE];
            if (sizeof($failedRespondentIds)) {
                $res['additionalResponseCodes'][self::ERROR_STATUS_CODE] = $failedRespondentIds;
            }
            if (sizeof($unsubscribedRespondentIds)) {
                $res['additionalResponseCodes'][self::UNSUBSCRIBDED_STATUS_CODE] = $unsubscribedRespondentIds;
            }
            $response->setData($res);

            return $response;
        }

        if (sizeof($failedRespondentIds) >= sizeof($unsubscribedRespondentIds)) {
            $res = ['generalResponseCode' => self::ERROR_STATUS_CODE];
            if (sizeof($unsubscribedRespondentIds)) {
                $res['additionalResponseCodes'][self::UNSUBSCRIBDED_STATUS_CODE] = $unsubscribedRespondentIds;
            }
            $response->setData($res);

            return $response;
        }

        $res = ['generalResponseCode' => self::UNSUBSCRIBDED_STATUS_CODE];
        if (sizeof($failedRespondentIds)) {
            $res['additionalResponseCodes'][self::ERROR_STATUS_CODE] = $failedRespondentIds;
        }
        $response->setData($res);

        return $response;
    }
}
