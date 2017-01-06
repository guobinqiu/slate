<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Entity\SurveyGmo;
use Wenwen\FrontendBundle\Entity\SurveyGmoParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\ServiceDependency\HttpClient;

class SurveyGmoService
{
    private $logger;
    private $fakeAnswerLogger;
    private $em;
    private $prizeTicketService;
    private $pointService;
    private $parameterService;
    private $httpClient;

    public function __construct(LoggerInterface $logger,
                                LoggerInterface $fakeAnswerLogger,
                                EntityManager $em,
                                PrizeTicketService $prizeTicketService,
                                PointService $pointService,
                                ParameterService $parameterService,
                                HttpClient $httpClient
    ) {
        $this->logger = $logger;
        $this->fakeAnswerLogger = $fakeAnswerLogger;
        $this->em = $em;
        $this->prizeTicketService = $prizeTicketService;
        $this->pointService = $pointService;
        $this->parameterService = $parameterService;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $userId
     * @return array
     */
    public function getSurveyList($userId = '2067715')
    {
        $surveylistUrl = $this->parameterService->getParameter('gmo_surveylistUrl');
        $panelistId = $userId;
        $panelCode = $this->parameterService->getParameter('gmo_panelCode');
        $randomString = strtotime('now');
        $encryptedID = $panelistId . ':' . $panelCode . ':' . $randomString;
        $encryptKey = $this->parameterService->getParameter('gmo_encryptKey');
        $crypt = $this->encrypt_blowfish($encryptedID, $encryptKey);
        $data = array('panelType' => $panelCode, 'crypt' => $crypt);
        $surveylistUrl .= '?' . http_build_query($data);
        $request = $this->httpClient->get($surveylistUrl, null, array('timeout' => 3, 'connect_timeout' => 3));
        $response = $request->send();
        $json = $response->getBody();
        $researches = json_decode($json, true);
        foreach ($researches as &$research) {
            if ('02' == $research['ans_stat_cd']) {
                $research['is_answered'] = 1;
            } else {
                $research['is_answered'] = 0;
            }
            if ('05' == $research['status'] && '05' == $research['enqPerPanelStatus']) {
                $research['is_closed'] = 0;
            } else {
                $research['is_closed'] = 1;
            }
            $research['title'] = 'g' . $research['research_id'] . ' ' . $research['title'];
            $research['url'] = $research['redirectSt'] . $research['id'] . '=' . $research['encryptId'];
        }
        return $researches;
    }

    public function processSurveyEndlink($surveyId, User $user, $answerStatus, $clientIp)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findOneBy(array('researchId' => $surveyId));
        if ($survey != null) {
            $conn = $this->em->getConnection();
            $conn->beginTransaction();
            try {
                $this->createParticipationHistory($survey, $user, $answerStatus, $clientIp);
                $points = $survey->getPoints($answerStatus);
                $this->pointService->addPoints(
                    $user,
                    $points,
                    CategoryType::SOP_COST,
                    TaskType::SURVEY,
                    "g{$survey->getId()} {$survey->getTitle()}",
                    $survey
                );
                $this->pointService->addPointsForInviter(
                    $user,
                    $points * 0.1,
                    CategoryType::EVENT_INVITE_SURVEY,
                    TaskType::RENTENTION,
                    '您的好友' . $user->getNick() . '回答了一份商业问卷',
                    $survey
                );
                $conn->commit();
            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }
        }
        $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'gmo商业问卷', $surveyId, $answerStatus);
    }

    public function createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp = null, $loi = null)
    {
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyGmoParticipationHistory')->findOneBy(array(
//            'appMid' => $appMid,
            'surveyId' => $surveyId,
            'status' => $answerStatus,
            'userId' => $userId,
        ));
        if ($participation == null) {
            $participation = new SurveyGmoParticipationHistory();
//            $participation->setAppMid($appMid);
            $participation->setSurveyId($surveyId);
            $participation->setStatus($answerStatus);
            $participation->setClientIp($clientIp);
            $participation->setLoi($loi);
            $participation->setUserId($userId);
            $this->em->persist($participation);
            $this->em->flush();
        } else {
            $participation->setUpdatedAt(new \DateTime());
            $this->em->flush();
        }
        return $participation;
    }

    public function createOrUpdateSurvey(array $surveyData)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findOneBy(array('researchId' => $surveyData['research_id']));
        if ($survey == null) {
            $survey = new SurveyGmo();
            $this->copyProperties($survey, $surveyData);
            $this->em->persist($survey);
            $this->em->flush($survey);
        } else {
            $snapshot = clone $survey;
            $this->copyProperties($survey, $surveyData);
            if ($survey != $snapshot) {
                $this->em->flush($survey);
            }
        }
        return $survey;
    }

    private function copyProperties(SurveyGmo $survey, array $surveyData)
    {
        $survey->setArrivalDay($surveyData['arrivalDay']);
        $survey->setResearchId($surveyData['research_id']);
        $survey->setResearchType($surveyData['research_type']);
        $survey->setTitle($surveyData['title']);
        $survey->setStatus($surveyData['status']);
        $survey->setEnqPerPanelStatus($surveyData['enqPerPanelStatus']);
        $survey->setPoint($surveyData['point']);
        $survey->setPointMin($surveyData['point_min']);
        $survey->setPointType($surveyData['point_type']);
        $survey->setStartDt($surveyData['start_dt']);
        if (isset($surveyData['is_closed'])) {
            if (!$survey->isClosed() && $surveyData['is_closed']) {
                $survey->setClosedAt(new \DateTime());
            } else if ($survey->isClosed() && !$surveyData['is_closed']) {
                $this->logger->warning('gmo survey_id: ' . $survey->getSurveyId() . '从关闭又被打开');
                $survey->setClosedAt(null);
            }
        }
    }

    private function encrypt_blowfish($data, $key) {
        $blockSize = mcrypt_get_block_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $padding = $blockSize - (strlen($data) % $blockSize);
        $data .= str_repeat(chr($padding), $padding);
        $cipherText = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_ECB);
        return bin2hex($cipherText);
    }

    private function decrypt_blowfish($data, $key) {
        $data = pack("H*", $data);
        $res = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data , MCRYPT_MODE_ECB);
        return $res;
    }

    private function createParticipationHistory($survey, $user, $answerStatus, $clientIp)
    {
        $actualLoiSeconds = null;
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyGmoParticipationHistory')->findOneBy(array(
//            'appMid' => $appMid,
            'surveyId' => $survey->getId(),
            'status' => SurveyStatus::STATUS_FORWARD,
            'userId' => $user->getId(),
        ));
        if ($participation != null) {
            $forwardAt = $participation->getUpdatedAt()->getTimestamp();
            $actualLoiSeconds = time() - $forwardAt;
            if ($survey->getLoi() > 0) {
                $loiSeconds = $survey->getLoi() * 60;
                if ($actualLoiSeconds < $loiSeconds / 4) {
                    $this->fakeAnswerLogger->info('gmo: userId=' . $user->getId() . ',surveyId=' . $survey->getId());
                    $answerStatus = SurveyStatus::STATUS_SCREENOUT;
                }
            }
        }
        $this->createParticipationByUserId($user->getId(), $survey->getSurveyId(), $answerStatus, $clientIp, $actualLoiSeconds);
    }
}