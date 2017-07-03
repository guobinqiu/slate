<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Entity\SurveyGmo;
use Wenwen\FrontendBundle\Entity\SurveyGmoParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
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

    public function getSurveyListJson($userId) {
        $panelistId = $userId;
        $panelCode = $this->parameterService->getParameter('gmo_panelCode');
        $randomString = strtotime('now');
        $encryptId = $panelistId . ':' . $panelCode . ':' . $randomString;
        $encryptKey = $this->parameterService->getParameter('gmo_encryptKey');
        $crypt = $this->encrypt_blowfish($encryptId, $encryptKey);
        $data = array('panelType' => $panelCode, 'crypt' => $crypt);
        $url = $this->parameterService->getParameter('gmo_surveylistUrl') . '?' . http_build_query($data);
        $request = $this->httpClient->get($url, null, array('timeout' => 2, 'connect_timeout' => 2));
        $response = $request->send();
        return $response->getBody();

//        return '
//        [
//          {
//            "ans_mode": "01",
//            "ans_stat_cd": "01",
//            "arrivalDay": "2015/12/02",
//            "custom_nm": null,
//            "encryptId": "5cd31dba666568c3c7dee40bb4b2b4039a8e7067fec89fc6",
//            "enqPerPanelStatus": "05",
//            "enq_id": 629277,
//            "enq_id_truenavi": null,
//            "external_enq_id": null,
//            "id": "dmid",
//            "lg_img": "mtop_i_cate01.gif",
//            "lg_nm": "通常調査",
//            "logo_type": "1",
//            "loi": 2,
//            "main_enq_id": 629278,
//            "matter_type": 9,
//            "optimize_device": "3",
//            "own_flag": "0",
//            "page_comment": "（表紙挿入文例）事後付与",
//            "point": 5,
//            "point_min": 2,
//            "point_sign": "p",
//            "point_string": "最大5p",
//            "point_type": 0,
//            "promotion_type": "0",
//            "que_num": 5,
//            "redirectSt": "https://st.infopanel.jp/lpark/enqRedirect.do?",
//            "research_id": 110202,
//            "research_type": "2",
//            "si_img": "mtop_i_stus01.gif",
//            "situation": "未回答",
//            "start_dt": 1448982000000,
//            "status": "05",
//            "title": "test survey 2"
//          },
//          {
//            "ans_mode": "01",
//            "ans_stat_cd": "01",
//            "arrivalDay": "2015/12/02",
//            "custom_nm": null,
//            "encryptId": "fa47bc2ad1944b7b9d7748b67260736b30c173cd99a068e3",
//            "enqPerPanelStatus": "05",
//            "enq_id": 629275,
//            "enq_id_truenavi": null,
//            "external_enq_id": null,
//            "id": "dmid",
//            "lg_img": "mtop_i_cate01.gif",
//            "lg_nm": "通常調査",
//            "logo_type": "1",
//            "loi": 4,
//            "main_enq_id": 629276,
//            "matter_type": 9,
//            "optimize_device": "3",
//            "own_flag": "0",
//            "page_comment": "",
//            "point": 10,
//            "point_min": 2,
//            "point_sign": "p",
//            "point_string": "最大10p",
//            "point_type": 0,
//            "promotion_type": "0",
//            "que_num": 10,
//            "redirectSt": "https://st.infopanel.jp/lpark/enqRedirect.do?",
//            "research_id": 110200,
//            "research_type": "2",
//            "si_img": "mtop_i_stus01.gif",
//            "situation": "未回答",
//            "start_dt": 1448982000000,
//            "status": "05",
//            "title": "test survey 1"
//          }
//        ]
//        ';
    }

    public function getSurveyList($userId)
    {
        $researches = json_decode($this->getSurveyListJson($userId), true);
        foreach ($researches as &$research) {
            $research = $this->addOrUpdateAttributes($research);
        }
        return $researches;
    }

    public function processSurveyEndlink($surveyId, $userId, $answerStatus, $points, $clientIp)
    {
        if (!SurveyStatus::isValid($answerStatus)) {
            throw new \InvalidArgumentException("gmo invalid answer status: {$answerStatus}");
        }
        $answerStatus = strtolower($answerStatus);
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($userId);
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findOneBy(array('researchId' => $surveyId));
        if ($survey != null) {
            $conn = $this->em->getConnection();
            $conn->beginTransaction();
            try {
                $this->createParticipationHistory($survey, $user, $answerStatus, $clientIp);
                // 记录csq
                    $user->updateCSQ($answerStatus);

                $this->pointService->addPoints(
                    $user,
                    $points,
                    CategoryType::GMO_COST,
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
                $conn->commit();
            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }
        }
        $this->prizeTicketService->createPrizeTicket(
            $user,
            $answerStatus == SurveyStatus::STATUS_COMPLETE ? PrizeItem::TYPE_BIG : PrizeItem::TYPE_SMALL,
            'gmo商业问卷',
            $surveyId,
            $answerStatus
        );
    }

    public function createParticipationByUserId($userId, $surveyGmoId, $answerStatus, $clientIp = null, $loi = null)
    {
        if (!SurveyStatus::isValid($answerStatus)) {
            throw new \InvalidArgumentException("gmo invalid answer status: {$answerStatus}");
        }
        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyGmoParticipationHistory')->findOneBy(array(
//            'appMid' => $appMid,
            'surveyGmoId' => $surveyGmoId,
            'status' => $answerStatus,
            'userId' => $userId,
        ));
        if ($participation == null) {
            $participation = new SurveyGmoParticipationHistory();
//            $participation->setAppMid($appMid);
            $participation->setSurveyGmoId($surveyGmoId);
            $participation->setStatus($answerStatus);
            $participation->setClientIp($clientIp);
            $participation->setLoi($loi);
            $participation->setUserId($userId);
            $this->em->persist($participation);
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
        $surveyData = $this->addOrUpdateAttributes($surveyData);
        if ($survey->isClosed() == 0 && $surveyData['is_closed'] == 1) {
            $survey->setClosedAt(new \DateTime());
        } else if ($survey->isClosed() == 1 && $surveyData['is_closed'] == 0) {
            $this->logger->warning('gmo survey_id: ' . $survey->getId() . '从关闭又被打开');
            $survey->setClosedAt(null);
        }
        $survey->setArrivalDay($surveyData['arrivalDay']);
        $survey->setResearchId($surveyData['research_id']);
        $survey->setTitle($surveyData['title']);
        $survey->setStatus($surveyData['status']);
        $survey->setEnqPerPanelStatus($surveyData['enqPerPanelStatus']);
        $survey->setPoint($surveyData['point']);
        $survey->setPointMin($surveyData['point_min']);
        $survey->setLoi($surveyData['loi']);
        $survey->setLgNm($surveyData['lg_nm']);
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
            'surveyGmoId' => $survey->getId(),
            'status' => SurveyStatus::STATUS_FORWARD,
            'userId' => $user->getId(),
        ));
        if ($participation != null) {
            $forwardAt = $participation->getUpdatedAt()->getTimestamp();
            $actualLoiSeconds = time() - $forwardAt;
            if ($survey->getLoi() > 0) {
                $loiSeconds = $survey->getLoi() * 60;
                if ($actualLoiSeconds < $loiSeconds / 4) {
                    $this->fakeAnswerLogger->info('gmo: userId=' . $user->getId() . ', surveyGmoId=' . $survey->getId());
                }
            }
        }
        $this->createParticipationByUserId($user->getId(), $survey->getId(), $answerStatus, $clientIp, $actualLoiSeconds);
    }

    private function addOrUpdateAttributes(array $surveyData) {
        if ('02' == $surveyData['ans_stat_cd']) {
            $surveyData['is_answered'] = 1;
        } else {
            $surveyData['is_answered'] = 0;
        }

        if ('05' == $surveyData['status'] && '05' == $surveyData['enqPerPanelStatus']) {
            $surveyData['is_closed'] = 0;
        } else {
            $surveyData['is_closed'] = 1;
        }

        $surveyData['url'] = $surveyData['redirectSt'] . $surveyData['id'] . '=' . $surveyData['encryptId'];

        $surveyGmoNonBusiness = $this->em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->findOneBy(array('researchId' => $surveyData['research_id']));
        if ($surveyGmoNonBusiness != null) {
            $surveyData['point'] = $surveyGmoNonBusiness->getCompletePoint();
            $surveyData['point_min'] = min($surveyGmoNonBusiness->getScreenoutPoint(), $surveyGmoNonBusiness->getQuotafullPoint());
        }

        return $surveyData;
    }

    private function getTaskName(SurveyGmo $survey, $answerStatus)
    {
        $statusText = '被甄别';
        if ($answerStatus == SurveyStatus::STATUS_COMPLETE) {
            $statusText = '完成';
        }
        return "g{$survey->getResearchId()} {$survey->getTitle()}（状态：{$statusText}）";
    }
}