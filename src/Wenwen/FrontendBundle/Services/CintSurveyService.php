<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Wenwen\AppBundle\Entity\CintResearchSurveyParticipationHistory;
use Wenwen\FrontendBundle\Entity\CintResearchSurvey;
use Wenwen\FrontendBundle\Entity\CintResearchSurveyStatusHistory;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class CintSurveyService
{
    private $logger;
    private $em;
    private $prizeTicketService;
    private $pointService;
    private $redis;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                PrizeTicketService $prizeTicketService,
                                PointService $pointService,
                                Client $redis)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->prizeTicketService = $prizeTicketService;
        $this->pointService = $pointService;
        $this->redis = $redis;
    }

    public function addSurveyUrlToken($survey, $userId)
    {
        $token = $this->createToken($survey['survey_id'], $userId);
        $survey['url'] = $survey['url'] . '&sop_custom_token=' . $token;
        return $survey;
    }

    public function createToken($surveyId, $userId)
    {
        $key = CacheKeys::getCintTokenKey($surveyId, $userId);
        $token = md5(uniqid(rand(), true));
        $this->redis->set($key, $token);
        $this->redis->expire($key, CacheKeys::TOKEN_TTL);
        return $token;
    }

    public function getToken($surveyId, $userId)
    {
        $key = CacheKeys::getCintTokenKey($surveyId, $userId);
        return $this->redis->get($key);
    }

    public function deleteToken($surveyId, $userId)
    {
        $key = CacheKeys::getCintTokenKey($surveyId, $userId);
        $this->redis->del($key);
    }

    public function processSurveyEndlink($surveyId, $tid, User $user, $answerStatus, $appMid)
    {
        $token = $this->getToken($surveyId, $user->getId());
        if ($token != null && $tid == $token) {
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'cint商业问卷', $surveyId, $answerStatus);
            $this->createStatusHistory($appMid, $surveyId, $answerStatus, SurveyStatus::ANSWERED);
            $survey = $this->em->getRepository('WenwenFrontendBundle:CintResearchSurvey')->findOneBy(array('surveyId' => $surveyId));
            if ($survey != null) {
                $conn = $this->em->getConnection();
                $conn->beginTransaction();
                try {
                    $points = $survey->getPoints($answerStatus);
                    $this->createParticipationHistory($appMid, $surveyId, $survey->getQuotaId(), $points);
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
            $this->deleteToken($surveyId, $user->getId());
        }
    }

    public function createStatusHistory($appMid, $surveyId, $answerStatus, $isAnswered = SurveyStatus::UNANSWERED)
    {
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:CintResearchSurveyStatusHistory')->findOneBy(array(
            'appMid' => $appMid,
            'surveyId' => $surveyId,
            'status' => $answerStatus,
        ));
        if ($statusHistory == null) {
            $statusHistory = new CintResearchSurveyStatusHistory();
            $statusHistory->setAppMid($appMid);
            $statusHistory->setSurveyId($surveyId);
            $statusHistory->setStatus($answerStatus);
            $statusHistory->setIsAnswered($isAnswered);
            $this->em->persist($statusHistory);
            $this->em->flush();
        }
        return $statusHistory;
    }

    public function createParticipationHistory($appMid, $surveyId, $quotaId, $points, $type = null)
    {
        $participationHistory = $this->em->getRepository('WenwenAppBundle:CintResearchSurveyParticipationHistory')->findOneBy(array(
            'cintProjectId' => $surveyId,
            'appMemberId' => $appMid
        ));
        if ($participationHistory == null) {
            $participationHistory = new CintResearchSurveyParticipationHistory();
            $participationHistory->setCintProjectId($surveyId);
            $participationHistory->setCintProjectQuotaId($quotaId);
            $participationHistory->setAppMemberID($appMid);
            $participationHistory->setPoint($points);
            $participationHistory->setType($type);
            $this->em->persist($participationHistory);
            $this->em->flush();
        }
        return $participationHistory;
    }

    public function getResearchSurveyPoint($appMid, $surveyId)
    {
        $participationHistory = $this->em->getRepository('WenwenAppBundle:CintResearchSurveyParticipationHistory')->findOneBy(array(
            'cintProjectId' => $surveyId,
            'appMemberId' => $appMid
        ));
        if ($participationHistory != null) {
            return $participationHistory->getPoint();
        }
        return 0;
    }

    public function createResearchSurvey($survey)
    {
        $researchSurvey = $this->em->getRepository('WenwenFrontendBundle:CintResearchSurvey')->findOneBy(array('surveyId' => $survey['survey_id']));
        if ($researchSurvey == null) {
            $researchSurvey = new CintResearchSurvey();
            $this->copyProperties($researchSurvey, $survey);
            $this->em->persist($researchSurvey);
            $this->em->flush();
        }
        return $researchSurvey;
    }

    public function createOrUpdateResearchSurvey($survey)
    {
        $researchSurvey = $this->em->getRepository('WenwenFrontendBundle:CintResearchSurvey')->findOneBy(array('surveyId' => $survey['survey_id']));
        if ($researchSurvey == null) {
            $researchSurvey = new CintResearchSurvey();
            $this->copyProperties($researchSurvey, $survey);
            $this->em->persist($researchSurvey);
            $this->em->flush($researchSurvey);
        } else {
            $snapshot = clone $researchSurvey;
            $this->copyProperties($researchSurvey, $survey);
            if ($researchSurvey != $snapshot) {
                $this->em->flush($researchSurvey);
            }
        }
        return $researchSurvey;
    }

    private function copyProperties(CintResearchSurvey $researchSurvey, $survey)
    {
        $researchSurvey->setSurveyId($survey['survey_id']);
        $researchSurvey->setQuotaId($survey['quota_id']);
        $researchSurvey->setLoi($survey['loi']);
        $researchSurvey->setIr($survey['ir']);
        $researchSurvey->setCpi($survey['cpi']);
        $researchSurvey->setTitle($survey['title']);
        if (!empty($survey['extra_info']['point']['complete'])) {
            $researchSurvey->setCompletePoint($survey['extra_info']['point']['complete']);
        }
        if (!empty($survey['extra_info']['point']['screenout'])) {
            $researchSurvey->setScreenoutPoint($survey['extra_info']['point']['screenout']);
        }
        if (!empty($survey['extra_info']['point']['quotafull'])) {
            $researchSurvey->setQuotafullPoint($survey['extra_info']['point']['quotafull']);
        }
        if (!empty($survey['extra_info']['date']['start_at'])) {
            $researchSurvey->setStartDate(new \DateTime($survey['extra_info']['date']['start_at']));
        }
        if (!empty($survey['extra_info']['date']['end_at'])) {
            $researchSurvey->setEndDate(new \DateTime($survey['extra_info']['date']['end_at']));
        }
        if (!empty($survey['extra_info']['content'])) {
            $researchSurvey->setComment($survey['extra_info']['content']);
        }
        if (isset($survey['blocked_devices']['PC'])) {
            $researchSurvey->setPcBlocked($survey['blocked_devices']['PC']);
        }
        if (isset($survey['blocked_devices']['MOBILE'])) {
            $researchSurvey->setMobileBlocked($survey['blocked_devices']['MOBILE']);
        }
        if (isset($survey['blocked_devices']['TABLET'])) {
            $researchSurvey->setTabletBlocked($survey['blocked_devices']['TABLET']);
        }
        if (isset($survey['is_closed'])) {
            $researchSurvey->setIsClosed($survey['is_closed']);
        }
        if (isset($survey['is_fixed_loi'])) {
            $researchSurvey->setIsFixedLoi($survey['is_fixed_loi']);
        }
        if (isset($survey['is_notifiable'])) {
            $researchSurvey->setIsNotifiable($survey['is_notifiable']);
        }
    }
}
