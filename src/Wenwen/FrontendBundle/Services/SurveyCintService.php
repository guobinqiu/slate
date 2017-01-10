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

    public function processSurveyEndlink($surveyId, $tid, $appMid, $answerStatus, $clientIp)
    {
        $points = 0;
        $userId = $this->userService->toUserId($appMid);
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($userId);
        $token = $this->getSurveyToken($surveyId, $user->getId());
        if ($token != null && $tid == $token) {
//            $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => $surveyId));
//            if ($survey != null) {
//                $conn = $this->em->getConnection();
//                $conn->beginTransaction();
//                try {
//                    $this->createParticipationHistory($survey, $user, $answerStatus, $clientIp);
//                    $points = $survey->getPoints($answerStatus);
//                    $this->pointService->addPoints(
//                        $user,
//                        $points,
//                        CategoryType::CINT_COST,
//                        TaskType::SURVEY,
//                        "c{$survey->getSurveyId()} {$survey->getTitle()}",
//                        $survey
//                    );
//                    $this->pointService->addPointsForInviter(
//                        $user,
//                        $points * 0.1,
//                        CategoryType::EVENT_INVITE_SURVEY,
//                        TaskType::RENTENTION,
//                        '您的好友' . $user->getNick() . '回答了一份商业问卷',
//                        $survey
//                    );
//                    $conn->commit();
//                } catch (\Exception $e) {
//                    $conn->rollBack();
//                    throw $e;
//                }
//            }
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'cint商业问卷', $surveyId, $answerStatus);
            $this->deleteSurveyToken($surveyId, $user->getId());
        }
        return $points;
    }

    public function createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp = null, $loi = null)
    {
        if (!SurveyStatus::isValid($answerStatus)) {
            throw new \InvalidArgumentException("cint invalid answer status: {$answerStatus}");
        }
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
            $participation->setLoi($loi);
            $participation->setUserId($userId);
            $this->em->persist($participation);
            $this->em->flush();
        }
        return $participation;
    }

    public function createParticipationByAppMid($appMid, $surveyId, $answerStatus, $clientIp = null, $loi = null)
    {
        $userId = $this->userService->toUserId($appMid);
        return $this->createParticipationByUserId($userId, $surveyId, $answerStatus, $clientIp, $loi);
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
            if ($survey->getIsClosed() == 0 && $surveyData['is_closed'] == 1) {
                $survey->setClosedAt(new \DateTime());
            } else if ($survey->getIsClosed() == 1 && $surveyData['is_closed'] == 0) {
                $this->logger->warning('cint survey_id: ' . $survey->getSurveyId() . '从关闭又被打开');
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

    private function createParticipationHistory($survey, $user, $answerStatus, $clientIp)
    {
        $actualLoiSeconds = null;
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
//            'appMid' => $appMid,
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
                    $this->fakeAnswerLogger->info('cint: userId=' . $user->getId() . ',surveyId=' . $survey->getId());
                    $answerStatus = SurveyStatus::STATUS_SCREENOUT;
                }
            }
        }
        $this->createParticipationByUserId($user->getId(), $survey->getSurveyId(), $answerStatus, $clientIp, $actualLoiSeconds);
    }
}
