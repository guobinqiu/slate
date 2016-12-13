<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Wenwen\FrontendBundle\Entity\SurveySop;
use Wenwen\FrontendBundle\Entity\SurveySopParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\User;
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

    public function __construct(LoggerInterface $logger,
                                LoggerInterface $fakeAnswerLogger,
                                EntityManager $em,
                                PrizeTicketService $prizeTicketService,
                                PointService $pointService,
                                Client $redis,
                                UserService $userService)
    {
        $this->logger = $logger;
        $this->fakeAnswerLogger = $fakeAnswerLogger;
        $this->em = $em;
        $this->prizeTicketService = $prizeTicketService;
        $this->pointService = $pointService;
        $this->redis = $redis;
        $this->userService = $userService;
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

    public function processSurveyEndlink($surveyId, $tid, User $user, $answerStatus, $clientIp)
    {
        $token = $this->getSurveyToken($surveyId, $user->getId());
        if ($token != null && $tid == $token) {
            if ($answerStatus == SurveyStatus::STATUS_COMPLETE) {
                $answerStatus = $this->changeAnswerStatus($user->getId(), $surveyId, $answerStatus);
            }
            $this->createParticipationByUserId($user->getId(), $surveyId, $answerStatus, $clientIp);
            $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $surveyId));
            if ($survey != null) {
                $conn = $this->em->getConnection();
                $conn->beginTransaction();
                try {
                    $points = $survey->getPoints($answerStatus);
                    $this->pointService->addPoints(
                        $user,
                        $points,
                        CategoryType::SOP_COST,
                        TaskType::SURVEY,
                        "r{$surveyId} {$survey->getTitle()}",
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
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'sop商业问卷', $surveyId, $answerStatus);
            $this->deleteSurveyToken($surveyId, $user->getId());
        }
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

    public function processProfilingEndlink(User $user, $tid)
    {
        $key = 'sop_profiling_' . $user->getId();
        $token = $this->redis->get($key);
        if ($token != null && $tid == $token) {
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'sop属性问卷', null, SurveyStatus::STATUS_COMPLETE);
            $this->redis->del($key);
        }
    }

    public function createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp = null)
    {
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveySopParticipationHistory')->findOneBy(array(
//            'appMid' => $appMid,
            'surveyId' => $surveyId,
            'status' => $answerStatus,
            'userId' => $userId,
        ));
        if ($participation == null) {
            $participation = new SurveySopParticipationHistory();
//            $participation->setAppMid($appMid);
            $participation->setSurveyId($surveyId);
            $participation->setStatus(strtolower($answerStatus));
            $participation->setClientIp($clientIp);
            $participation->setUserId($userId);
            $this->em->persist($participation);
            $this->em->flush();
        }
        return $participation;
    }

    public function createParticipationByAppMid($appMid, $surveyId, $answerStatus, $clientIp = null)
    {
        $userId = $this->userService->toUserId($appMid);
        return $this->createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp);
    }

    public function getSurveyPoint($userId, $surveyId)
    {
        $taskHistory = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($userId % 10))->getTaskHistoryBySurveySop($userId, $surveyId);
        if ($taskHistory != null) {
            return $taskHistory->getPoint();
        }
        return 0;
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

    public function changeAnswerStatus($userId, $surveyId, $answerStatus)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $surveyId));
        if ($survey != null && $survey->getLoi() > 0) {
            $participation = $this->em->getRepository('WenwenFrontendBundle:SurveySopParticipationHistory')->findOneBy(array(
//              'appMid' => $appMid,
                'surveyId' => $surveyId,
                'status' => SurveyStatus::STATUS_FORWARD,
                'userId' => $userId,
            ));
            if ($participation != null) {
                $forwardAt = $participation->getCreatedAt()->getTimestamp();
                $actualLoiSeconds = time() - $forwardAt;
                $loiSeconds = $survey->getLoi() * 60;
                if ($actualLoiSeconds < $loiSeconds / 4) {
                    $this->fakeAnswerLogger->info('sop: userId=' . $userId . ',surveyId=' . $surveyId);
                    return SurveyStatus::STATUS_SCREENOUT;
                }
            }
        }
        return $answerStatus;
    }
}