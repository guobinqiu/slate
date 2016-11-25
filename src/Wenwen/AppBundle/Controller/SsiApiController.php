<?php

namespace Wenwen\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VendorIntegration\SSI\PC1\Request as SsiRequest;
use VendorIntegration\SSI\PC1\RequestValidator as SsiRequestValidator;
use VendorIntegration\SSI\PC1\RequestHandler as SsiRequestHandler;
use Wenwen\AppBundle\Entity\SsiRespondent;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use JMS\JobQueueBundle\Entity\Job;


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
        $logger = $this->get('monolog.logger.ssi_notification');
        
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

/*
2016-11-24 不给ssi 发邮件
        # send mail job
        if (sizeof($handler->getSucceededRespondentIds())) {

            // 输出respondentId 到临时文件里
            $ssiParams = $this->container->getParameter('ssi_project_survey');
            $waitDir = $ssiParams['notification']['wait_dir'];
            $fileName = 'ssi_' . $ssiRequest->getProjectId() . '_' . uniqid();
            $filePath = $waitDir . $fileName;

            $fs = new Filesystem();

            try {
                if(!$fs->exists($waitDir)){
                    $fs->mkdir($waitDir, 0775);
                }
                if($fs->exists($filePath)){
                    $filePath = $filePath . '_A';
                }
            } catch (IOExceptionInterface $e) {
                $logger->error(__METHOD__ . ' errMsg=' . $e->getMessage());
            }

            $fh = fopen($filePath, 'x+');
            foreach ($handler->getSucceededRespondentIds() as $respondentId) {
                $ssiRespondentId = SsiRespondent::parseRespondentId($respondentId);
                $logger->info('ssiRespondentId=' . json_encode($respondentId));
                fwrite($fh, $ssiRespondentId . PHP_EOL);
            }
            fclose($fh);

            $logger->info('Start add job');

            $job = new Job('mail:ssi_delivery_notification_batch', array(
                    '--respondents_id_file='.$fileName), true, '91wenwen');
            $em->persist($job);
            $em->flush();
            $em->clear();

            $logger->info('End add job');

        }
 */

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
