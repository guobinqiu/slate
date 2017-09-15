<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\SopRespondent;
use Predis\Client;
use Wenwen\FrontendBundle\Entity\SurveySop;
use Wenwen\FrontendBundle\Entity\SurveySopParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\OwnerType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class SurveySopService
{
    private $logger;
    private $fakeAnswerLogger;
    private $em;
    private $prizeTicketService;
    private $pointService;
    private $redis;
    private $userService;
    private $parameterService;

    public function __construct(LoggerInterface $logger,
                                LoggerInterface $fakeAnswerLogger,
                                EntityManager $em,
                                PrizeTicketService $prizeTicketService,
                                PointService $pointService,
                                Client $redis,
                                UserService $userService,
                                ParameterService $parameterService)
    {
        $this->logger = $logger;
        $this->fakeAnswerLogger = $fakeAnswerLogger;
        $this->em = $em;
        $this->prizeTicketService = $prizeTicketService;
        $this->pointService = $pointService;
        $this->redis = $redis;
        $this->userService = $userService;
        $this->parameterService = $parameterService;
    }

    public function addSurveyUrlToken($survey, $userId)
    {
        $token = $this->createSurveyToken($survey['survey_id'], $userId);
        $survey['url'] = $survey['url'] . '&sop_custom_token=' . $token;
        return $survey;
    }

    public function createSurveyToken($surveyId, $userId)
    {
        $key = CacheKeys::getSopTokenKey($surveyId, $userId);
        $token = md5(uniqid(rand(), true));
        $this->redis->set($key, $token);
        $this->redis->expire($key, CacheKeys::SURVEY_TOKEN_TTL);
        return $token;
    }

    public function getSurveyToken($surveyId, $userId)
    {
        $key = CacheKeys::getSopTokenKey($surveyId, $userId);
        return $this->redis->get($key);
    }

    public function deleteSurveyToken($surveyId, $userId)
    {
        $key = CacheKeys::getSopTokenKey($surveyId, $userId);
        $this->redis->del($key);
    }

    public function processSurveyEndlink($surveyId, $tid, $appMid, $answerStatus, $clientIp)
    {
        $points = 0;
        $answerStatus = strtolower($answerStatus);
        if (!SurveyStatus::isValid($answerStatus)) {
            throw new \InvalidArgumentException("sop invalid answer status: {$answerStatus}");
        }
        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        $user = $this->userService->getUserBySopRespondentAppMid($appMid);
        $token = $this->getSurveyToken($surveyId, $user->getId());
        if ($token != null && $tid == $token) {
            $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $surveyId));
            if (null === $survey) {
                throw new \Exception('SurveySop entity was not found. surveyId=' . $surveyId);
            }
            try {
                $answerStatus = $this->createParticipationHistory($survey, $user, $answerStatus, $clientIp);
                $user->updateCSQ($answerStatus);// 记录csq
                $points = $survey->getPoints($answerStatus);
                $this->pointService->addPoints(
                    $user,
                    $points,
                    CategoryType::SOP_COST,
                    TaskType::SURVEY,
                    $this->getTaskName($survey, $answerStatus),
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
                $this->prizeTicketService->createPrizeTicket(
                    $user,
                    $answerStatus == SurveyStatus::STATUS_COMPLETE ? PrizeItem::TYPE_BIG : PrizeItem::TYPE_SMALL,
                    'sop商业问卷',
                    $surveyId,
                    $answerStatus
                );
                $this->deleteSurveyToken($surveyId, $user->getId());
                $conn->commit();
            } catch (\Exception $e) {
                $conn->rollBack();
                $this->logger->error(__METHOD__ . ' ' . $e->getMessage());
                throw $e;
            }
        }
        return $points;
    }

    public function addProfilingUrlToken($profiling, $userId)
    {
        $token = md5(uniqid(rand(), true));
        $key = 'sop_profiling_' . $userId;
        $this->redis->set($key, $token);
        $this->redis->expire($key, CacheKeys::SURVEY_TOKEN_TTL);
        $profiling['url'] = $profiling['url'] . '&sop_custom_token=' . $token;
        return $profiling;
    }

    public function processProfilingEndlink($appMid, $tid)
    {
        $user = $this->userService->getUserBySopRespondentAppMid($appMid);
        $key = 'sop_profiling_' . $user->getId();
        $token = $this->redis->get($key);
        if ($token != null && $tid == $token) {
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_SMALL, 'sop属性问卷', null, SurveyStatus::STATUS_COMPLETE);
            $this->redis->del($key);
        }
    }

    public function createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp = null, $loi = null)
    {
        if (!SurveyStatus::isValid($answerStatus)) {
            throw new \InvalidArgumentException("sop invalid answer status: {$answerStatus}");
        }
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveySopParticipationHistory')->findOneBy(array(
            'surveyId' => $surveyId,
            'status' => $answerStatus,
            'userId' => $userId,
        ));
        if ($participation == null) {
            $participation = new SurveySopParticipationHistory();
            $participation->setSurveyId($surveyId);
            $participation->setStatus($answerStatus);
            $participation->setClientIp($clientIp);
            $participation->setLoi($loi);
            $participation->setUserId($userId);
            $this->em->persist($participation);
            $this->em->flush();
        }
        return $participation;
    }

    public function createParticipationByAppMid($appMid, $surveyId, $answerStatus, $clientIp = null, $loi = null)
    {
        $user = $this->userService->getUserBySopRespondentAppMid($appMid);
        return $this->createParticipationByUserId($user->getId(), $surveyId, $answerStatus, $clientIp, $loi);
    }

    public function createSurvey(array $surveyData)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $surveyData['survey_id']));
        if ($survey == null) {
            $survey = new SurveySop();
            $this->copyProperties($survey, $surveyData);
            $this->em->persist($survey);
            $this->em->flush();
        }
        return $survey;
    }

    public function createOrUpdateSurvey(array $surveyData)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $surveyData['survey_id']));
        if ($survey == null) {
            $survey = new SurveySop();
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

    private function copyProperties(SurveySop $survey, array $surveyData)
    {
        $survey->setSurveyId($surveyData['survey_id']);
        $survey->setQuotaId($surveyData['quota_id']);
        $survey->setLoi($surveyData['loi']);
        $survey->setIr($surveyData['ir']);
        $survey->setCpi($surveyData['cpi']);
        $survey->setTitle($surveyData['title']);
        if (!empty($surveyData['extra_info']['point']['complete'])) {
            $survey->setCompletePoint($surveyData['extra_info']['point']['complete']);
        }
        if (!empty($surveyData['extra_info']['point']['screenout'])) {
            $survey->setScreenoutPoint($surveyData['extra_info']['point']['screenout']);
        }
        if (!empty($surveyData['extra_info']['point']['quotafull'])) {
            $survey->setQuotafullPoint($surveyData['extra_info']['point']['quotafull']);
        }
        if (!empty($surveyData['extra_info']['date']['start_at'])) {
            $survey->setStartDate(new \DateTime($surveyData['extra_info']['date']['start_at']));
        }
        if (!empty($surveyData['extra_info']['date']['end_at'])) {
            $survey->setEndDate(new \DateTime($surveyData['extra_info']['date']['end_at']));
        }
        if (!empty($surveyData['extra_info']['content'])) {
            $survey->setComment($surveyData['extra_info']['content']);
        }
        if (isset($surveyData['blocked_devices']['PC'])) {
            $survey->setPcBlocked($surveyData['blocked_devices']['PC']);
        }
        if (isset($surveyData['blocked_devices']['MOBILE'])) {
            $survey->setMobileBlocked($surveyData['blocked_devices']['MOBILE']);
        }
        if (isset($surveyData['blocked_devices']['TABLET'])) {
            $survey->setTabletBlocked($surveyData['blocked_devices']['TABLET']);
        }
        if (isset($surveyData['is_closed'])) {
            if ($survey->getIsClosed() == 0 && $surveyData['is_closed'] == 1) {
                $survey->setClosedAt(new \DateTime());
            } else if ($survey->getIsClosed() == 1 && $surveyData['is_closed'] == 0) {
                $this->logger->warning('sop survey_id: ' . $survey->getSurveyId() . '从关闭又被打开');
                $survey->setClosedAt(null);
            }
            $survey->setIsClosed($surveyData['is_closed']);
        }
        if (isset($surveyData['is_fixed_loi'])) {
            $survey->setIsFixedLoi($surveyData['is_fixed_loi']);
        }
        if (isset($surveyData['is_notifiable'])) {
            $survey->setIsNotifiable($surveyData['is_notifiable']);
        }
    }

    public function isNotifiableSurvey($surveyId)
    {
        $researchSurvey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $surveyId));
        if ($researchSurvey != null) {
            return $researchSurvey->getIsNotifiable() == 1;
        }
        return false;
    }

    /**
     * Verify whether the it is a valid request by all params
     * https://console.partners.surveyon.com/docs/v1_1/authentication#signing-a-query-string
     *
     * @param array $params   all params of request. app_id and sig must exist.
     * @return boolean
     */
    public function isValidQueryString($params) {
        if (!is_array($params)) {
            $this->logger->warning(__METHOD__ . ' invalid params');
            return false;
        }

        if (!isset($params['app_id'])) {
            $this->logger->warning(__METHOD__ . ' app_id not set in params');
            return false;
        }

        if (!isset($params['sig'])) {
            $this->logger->warning(__METHOD__ . ' sig not set in params');
            return false;
        }

        try {
            // Get secret_key by param app_id
            $appId = $params['app_id'];
            $appSecret = $this->getAppSecretByAppId($appId);

            // Verify param sig is valid or not
            $sig = $params['sig'];
            $auth = new \SOPx\Auth\V1_1\Client($appId, $appSecret);
            unset($params['sig']);
            $result = $auth->verifySignature($sig, $params);

            if (!$result['status']) {
                $this->logger->warn(__METHOD__ . ' errMsg=' . $result['msg'] . ' params=' . json_encode($params));
                return false;
            } else {
                $this->logger->debug(__METHOD__ . ' success');
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' errMsg=' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify whether the it is a valid request by all params
     * https://console.partners.surveyon.com/docs/v1_1/authentication#signing-a-json-string
     *
     * @param JSON String $requestBody   all params of request. app_id and sig must exist.
     * @return boolean
     */
    public function isValidJSONString($jsonData, $xSopSig) {
        if (null === $jsonData) {
            $this->logger->warning(__METHOD__ . ' invalid jsonData');
            return false;
        }

        if (null === $xSopSig) {
            $this->logger->warning(__METHOD__ . ' invalid xSopSig');
            return false;
        }

        $params = $jsonData ? json_decode($jsonData, true) : array ();

        if (!isset($params['app_id'])) {
            $this->logger->warning(__METHOD__ . ' app_id not set in params');
            return false;
        }
        try {
            // Get secret_key by param app_id
            $appId = $params['app_id'];
            $appSecret = $this->getAppSecretByAppId($appId);

            // Verify xSopSig is valid or not
            $auth = new \SOPx\Auth\V1_1\Client($appId, $appSecret);
            $result = $auth->verifySignature($xSopSig, $jsonData);

            if (!$result['status']) {
                $this->logger->warn(__METHOD__ . ' errMsg=' . $result['msg'] . ' jsonData=' . $jsonData);
                return false;
            } else {
                $this->logger->debug(__METHOD__ . ' success');
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' errMsg=' . $e->getMessage());
            return false;
        }
    }


    /**
     * Verify whether the it is a valid request by all params
     * !!! This is used by ProjectSurveyCintController->agreementCompleteAction only. !!!
     * !!! Because the call back from SOP does not include app_id as a param in request body !!!
     * !!! Do not use it at other place !!!
     *
     * @param array $params   all params of request. app_id and sig must exist.
     * @return boolean
     */
    public function isValidQueryStringByAppMid($params) {
        if (!is_array($params)) {
            $this->logger->warning(__METHOD__ . ' invalid params');
            return false;
        }

        if (!isset($params['app_mid'])) {
            $this->logger->warning(__METHOD__ . ' app_mid not set in params');
            return false;
        }

        if (!isset($params['sig'])) {
            $this->logger->warning(__METHOD__ . ' sig not set in params');
            return false;
        }

        try {
            // Get app_id and secret_key by param app_mid
            $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid($params['app_mid']);
            if (!$sopRespondent) {
                $this->logger->warning(__METHOD__ . ' sopRespondent not exist app_mid=' . $params['app_mid']);
                return false;
            }
            $appId = $sopRespondent->getAppId();
            $appSecret = $this->getAppSecretByAppId($appId);

            $sig = $params['sig'];
            // remember to remove sig before verify
            unset($params['sig']);
            $auth = new \SOPx\Auth\V1_1\Client($appId, $appSecret);
            $result = $auth->verifySignature($sig, $params);

            if (!$result['status']) {
                $this->logger->warn(__METHOD__ . ' errMsg=' . $result['msg'] . ' params=' . json_encode($params));
                return false;
            } else {
                $this->logger->debug(__METHOD__ . ' success');
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' errMsg=' . $e->getMessage());
            return false;
        }
    }

    public function getSopCredentialsByOwnerType($ownerType)
    {
        if (!OwnerType::isValid($ownerType)) {
            throw new \InvalidArgumentException('Unsupported owner_type: ' . $ownerType);
        }
        $sopApps = $this->parameterService->getParameter('sop_apps');
        if (is_null($sopApps)) {
            throw new \InvalidArgumentException("Missing option 'sop_apps'");
        }
        foreach($sopApps as $sopApp) {
            if (!isset($sopApp['owner_type'])) {
                throw new \InvalidArgumentException("Missing option 'owner_type'");
            }
            if ($sopApp['owner_type'] == $ownerType) {
                if (!isset($sopApp['app_id'])) {
                    throw new \InvalidArgumentException("Missing option 'app_id'");
                }
                if (!isset($sopApp['app_secret'])) {
                    throw new \InvalidArgumentException("Missing option 'app_secret'");
                }
                return $sopApp;
            }
        }
        throw new \RuntimeException('SopCredentials was not found. owner_type=' . $ownerType);
    }

    public function getSopCredentialsByAppId($appId)
    {
        $sopApps = $this->parameterService->getParameter('sop_apps');
        if (is_null($sopApps)) {
            throw new \InvalidArgumentException("Missing option 'sop_apps'");
        }
        foreach($sopApps as $sopApp) {
            if (!isset($sopApp['app_id'])) {
                throw new \InvalidArgumentException("Missing option 'app_id'");
            }
            if ($sopApp['app_id'] == $appId) {
                if (!isset($sopApp['app_secret'])) {
                    throw new \InvalidArgumentException("Missing option 'app_secret'");
                }
                return $sopApp;
            }
        }
        throw new \RuntimeException('SopCredentials was not found. appId=' . $appId);
    }

    public function getAllSopCredentials()
    {
        $sopApps = $this->parameterService->getParameter('sop_apps');
        if (is_null($sopApps)) {
            throw new \InvalidArgumentException("Missing option 'sop_apps'");
        }
        return $sopApps;
    }

    public function getAppIdByOwnerType($ownerType)
    {
        $sopCredentials = $this->getSopCredentialsByOwnerType($ownerType);
        return $sopCredentials['app_id'];
    }

    public function getAppSecretByOwnerType($ownerType)
    {
        $sopCredentials = $this->getSopCredentialsByOwnerType($ownerType);
        return $sopCredentials['app_secret'];
    }

    public function getAppSecretByAppId($appId)
    {
        $sopCredentials = $this->getSopCredentialsByAppId($appId);
        return $sopCredentials['app_secret'];
    }

    public function getSopRespondentByUserId($userId) {
        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(['userId' => $userId]);
        if (null === $sopRespondent) {
            throw new \Exception('SopRespondent was not found. userId=' . $userId);
        }
        return $sopRespondent;
    }

    public function createSopRespondent($userId, $ownerType) {
        $appId = $this->getAppIdByOwnerType($ownerType);
        $sopRespondent = new SopRespondent();
        $i = 0;
        while ($this->isAppMidDuplicated($sopRespondent->getAppMid())) {
            $sopRespondent->setAppMid(SopRespondent::generateAppMid());
            $i++;
            if ($i > 1000) {
                break;
            }
        }
        $sopRespondent->setUserId($userId);
        $sopRespondent->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $sopRespondent->setAppId($appId);
        $this->em->persist($sopRespondent);
        $this->em->flush();
        return $sopRespondent;
    }

    private function createParticipationHistory($survey, $user, $answerStatus, $clientIp)
    {
        $actualLoiSeconds = null;
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveySopParticipationHistory')->findOneBy(array(
            'surveyId' => $survey->getSurveyId(),
            'status' => SurveyStatus::STATUS_FORWARD,
            'userId' => $user->getId(),
        ));
        if ($participation != null) {
            $forwardAt = $participation->getUpdatedAt()->getTimestamp();
            $actualLoiSeconds = time() - $forwardAt;
            if ($survey->getLoi() > 0) {
                $loiSeconds = $survey->getLoi() * 60;
                if ($actualLoiSeconds < $loiSeconds / 4) {
                    $this->fakeAnswerLogger->info('sop: userId=' . $user->getId() . ',surveyId=' . $survey->getId());
                    $answerStatus = SurveyStatus::STATUS_SCREENOUT;
                }
            }
        }
        $this->createParticipationByUserId($user->getId(), $survey->getSurveyId(), $answerStatus, $clientIp, $actualLoiSeconds);
        return $answerStatus;
    }

    private function getTaskName(SurveySop $survey, $answerStatus)
    {
        $statusText = '被甄别';
        if ($answerStatus == SurveyStatus::STATUS_COMPLETE) {
            $statusText = '完成';
        }
        return "r{$survey->getSurveyId()} {$survey->getTitle()}（状态：{$statusText}）";
    }

    private function isAppMidDuplicated($key)
    {
        return count($this->em->getRepository('JiliApiBundle:SopRespondent')->findByAppMid($key)) > 0;
    }
}