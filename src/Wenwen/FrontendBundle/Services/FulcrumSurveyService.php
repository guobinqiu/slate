<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Wenwen\AppBundle\Entity\FulcrumResearchSurveyParticipationHistory;
use Wenwen\FrontendBundle\Entity\FulcrumResearchSurvey;
use Wenwen\FrontendBundle\Entity\FulcrumResearchSurveyStatusHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Psr\Log\LoggerInterface;

class FulcrumSurveyService
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
        $token = md5(uniqid(rand(), true));
        $key = 'fulcrum_' . $userId . '_' . $survey['survey_id'];
        $this->redis->set($key, $token);
        $this->redis->expire($key, 60 * 60 * 24);
        $survey['url'] = $survey['url'] . '&sop_custom_token=' . $token;
        return $survey;
    }

    public function processSurveyEndlink($surveyId, $tid, User $user, $answerStatus, $appMid)
    {
        $key = 'fulcrum_' . $user->getId() . '_' . $surveyId;
        $token = $this->redis->get($key);
        if ($token != null && $tid == $token) {
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, 'fulcrum商业问卷', $surveyId, $answerStatus);
            $this->createStatusHistory($appMid, $surveyId, $answerStatus);
            $survey = $this->em->getRepository('WenwenFrontendBundle:FulcrumResearchSurvey')->findOneBy(array('surveyId' => $surveyId));
            if ($survey != null) {
                $conn = $this->em->getConnection();
                $conn->beginTransaction();
                try {
                    $points = $survey->getPoints($answerStatus);
                    $this->createParticipationHistory($appMid, $surveyId, $survey->getQuotaId(), $points);
                    $this->pointService->addPoints(
                        $user,
                        $points,
                        CategoryType::FULCRUM_COST,
                        TaskType::SURVEY,
                        "f{$surveyId} {$survey->getTitle()}",
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
            $this->redis->del($key);
        }
    }

    public function createStatusHistory($appMid, $surveyId, $answerStatus)
    {
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:FulcrumResearchSurveyStatusHistory')->findOneBy(array(
            'appMid' => $appMid,
            'surveyId' => $surveyId,
            'status' => $answerStatus,
        ));
        if ($statusHistory == null) {
            $statusHistory = new FulcrumResearchSurveyStatusHistory();
            $statusHistory->setAppMid($appMid);
            $statusHistory->setSurveyId($surveyId);
            $statusHistory->setStatus($answerStatus);
            $this->em->persist($statusHistory);
            $this->em->flush();
        }
        return $statusHistory;
    }

    public function createParticipationHistory($appMid, $surveyId, $quotaId, $points, $type = null)
    {
        $participationHistory = $this->em->getRepository('WenwenAppBundle:FulcrumResearchSurveyParticipationHistory')->findOneBy(array(
            'fulcrumProjectId' => $surveyId,
            'appMemberId' => $appMid
        ));
        if ($participationHistory == null) {
            $participationHistory = new FulcrumResearchSurveyParticipationHistory();
            $participationHistory->setFulcrumProjectId($surveyId);
            $participationHistory->setFulcrumProjectQuotaId($quotaId);
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
        $participationHistory = $this->em->getRepository('WenwenAppBundle:FulcrumResearchSurveyParticipationHistory')->findOneBy(array(
            'fulcrumProjectId' => $surveyId,
            'appMemberId' => $appMid
        ));
        if ($participationHistory != null) {
            return $participationHistory->getPoint();
        }
        return 0;
    }

    public function createResearchSurvey($survey)
    {
        $researchSurvey = $this->em->getRepository('WenwenFrontendBundle:FulcrumResearchSurvey')->findOneBy(array('surveyId' => $survey['survey_id']));
        if ($researchSurvey == null) {
            $researchSurvey = new FulcrumResearchSurvey();
            $researchSurvey->setSurveyId($survey['survey_id']);
            $researchSurvey->setQuotaId($survey['quota_id']);
            $researchSurvey->setLoi($survey['loi']);
            $researchSurvey->setIr($survey['ir']);
            $researchSurvey->setCpi($survey['cpi']);
            $researchSurvey->setTitle($survey['title']);
            $researchSurvey->setCompletePoint($survey['extra_info']['point']['complete']);
            $researchSurvey->setScreenoutPoint($survey['extra_info']['point']['screenout']);
            $researchSurvey->setQuotafullPoint($survey['extra_info']['point']['quotafull']);
            if (!empty($survey['extra_info']['date']['start_at'])) {
                $researchSurvey->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', $survey['extra_info']['date']['start_at']));
            }
            if (!empty($survey['extra_info']['date']['end_at'])) {
                $researchSurvey->setEndDate(\DateTime::createFromFormat('Y-m-d H:i:s', $survey['extra_info']['date']['end_at']));
            }
            $researchSurvey->setComment($survey['extra_info']['content']);
            if (isset($survey['blocked_devices']['PC'])) {
                $researchSurvey->setPcBlocked($survey['blocked_devices']['PC']);
            }
            if (isset($survey['blocked_devices']['MOBILE'])) {
                $researchSurvey->setMobileBlocked($survey['blocked_devices']['MOBILE']);
            }
            if (isset($survey['blocked_devices']['TABLET'])) {
                $researchSurvey->setTabletBlocked($survey['blocked_devices']['TABLET']);
            }
            $this->em->persist($researchSurvey);
            $this->em->flush();
        }
        return $researchSurvey;
    }
}
