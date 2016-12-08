<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\SurveyCint;
use Wenwen\FrontendBundle\Entity\SurveyCintParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class SurveyCintService
{
    private $logger;
    private $em;
    private $prizeTicketService;
    private $pointService;
    private $redis;
    private $userService;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                PrizeTicketService $prizeTicketService,
                                PointService $pointService,
                                Client $redis,
                                UserService $userService)
    {
        $this->logger = $logger;
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
        $key = CacheKeys::getCintTokenKey($surveyId, $userId);
        $token = md5(uniqid(rand(), true));
        $this->redis->set($key, $token);
        $this->redis->expire($key, CacheKeys::SURVEY_TOKEN_TTL);
        return $token;
    }

    public function getSurveyToken($surveyId, $userId)
    {
        $key = CacheKeys::getCintTokenKey($surveyId, $userId);
        return $this->redis->get($key);
    }

    public function deleteSurveyToken($surveyId, $userId)
    {
        $key = CacheKeys::getCintTokenKey($surveyId, $userId);
        $this->redis->del($key);
    }

    public function processSurveyEndlink($surveyId, $tid, User $user, $answerStatus, $appMid, $clientIp)
    {
        $token = $this->getSurveyToken($surveyId, $user->getId());
        if ($token != null && $tid == $token) {
            $answerStatus = $this->changeAnswerStatus($surveyId, $user->getId(), $answerStatus);
            $this->createParticipationByAppMid($appMid, $surveyId, $answerStatus, $clientIp);
            //由于日本那边quota_id还没有加，这里给用户加积分的代码先注释掉
            $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => $surveyId));
            if ($survey != null) {
                $conn = $this->em->getConnection();
                $conn->beginTransaction();
                try {
                    $points = $survey->getPoints($answerStatus);
                    $this->pointService->addPoints(
                        $user,
                        $points,
                        CategoryType::CINT_COST,
                        TaskType::SURVEY,
                        "c{$surveyId} {$survey->getTitle()}",
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
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'cint商业问卷', $surveyId, $answerStatus);
            $this->deleteSurveyToken($surveyId, $user->getId());
        }
    }

    public function createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp = null)
    {
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
//            'appMid' => $appMid,
            'surveyId' => $surveyId,
            'status' => $answerStatus,
            'userId' => $userId,
        ));
        if ($participation == null) {
            $participation = new SurveyCintParticipationHistory();
//            $participation->setAppMid($appMid);
            $participation->setSurveyId($surveyId);
            $participation->setStatus($answerStatus);
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
        $taskHistory = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($userId % 10))->getTaskHistoryBySurveyCint($userId, $surveyId);
        if ($taskHistory != null) {
            return $taskHistory->getPoint();
        }
        return 0;
    }

    public function createSurvey(array $surveyData)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => $surveyData['survey_id']));
        if ($survey == null) {
            $survey = new SurveyCint();
            $this->copyProperties($survey, $surveyData);
            $this->em->persist($survey);
            $this->em->flush();
        }
        return $survey;
    }

    public function createOrUpdateSurvey(array $surveyData)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => $surveyData['survey_id']));
        if ($survey == null) {
            $survey = new SurveyCint();
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

    private function copyProperties(SurveyCint $survey, array $surveyData)
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

    public function changeAnswerStatus($surveyId, $userId, $answerStatus)
    {
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => $surveyId));
        if ($survey != null && $survey->getLoi() > 0) {
            $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
//              'appMid' => $appMid,
                'surveyId' => $surveyId,
                'status' => SurveyStatus::STATUS_FORWARD,
                'userId' => $userId
            ));
            if ($participation != null) {
                $forwardAt = $participation->getCreatedAt()->getTimestamp();
                $actualLoiSeconds = time() - $forwardAt;
                $loiSeconds = $survey->getLoi() * 60;
                if ($actualLoiSeconds < $loiSeconds / 4) {
                    return SurveyStatus::STATUS_SCREENOUT;
                }
            }
        }
        return $answerStatus;
    }
}
