<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Entity\PrizeItem;

/**
 * 第三方非API对接方式的问卷项目信息管理 
 * 目前暂时只用于同TripleS的对接
 * 主要功能：新建问卷项目/更新问卷项目的内容/开放问卷项目/关闭问卷项目
 */
class SurveyPartnerService
{
    private $logger;

    private $em;

    private $parameterService;

    private $pointService;

    private $prizeTicketService;

    const TEST_USER_EMAIL = 'rpa-sys-china@d8aspring.com';

    const VALID_REFERER_DOMAIN = 'r.researchpanelasia.com';

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                PointService $pointService,
                                PrizeTicketService $prizeTicketService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->pointService = $pointService;
        $this->prizeTicketService = $prizeTicketService;
    }

    /**
     * 获取指定用户所有可以参与的问卷
     * @param string @userId
     * @return array $surveyPartners
     */
    public function getSurveyPartnerListForUser($user, $locationInfo) {
        $this->logger->info(__METHOD__ . ' START userId=' . $user->getId());
        $availableSurveyPartners = array();

        try{
            
            if(self::TEST_USER_EMAIL == $user->getEmail()){
                // 如果是测试用户，则显示所有处于init状态的项目
                $surveyPartners = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findBy(
                    array(
                        'status' => SurveyPartner::STATUS_INIT,
                        ));
                $this->logger->info(__METHOD__ . ' This is test user email(' . self::TEST_USER_EMAIL . ') ');
                return $surveyPartners;
            }

            $now = new \DateTime();
            // 注册三天以上的用户不显示这个类型的问卷
            if($user->getRegisterCompleteDate() <= $now->sub(new \DateInterval('P03D'))){
                $this->logger->debug(__METHOD__ . ' register over 3 days');
                return array();
            }

            // 找到所有open的问卷项目
            $surveyPartners = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findBy(
                array(
                    'status' => SurveyPartner::STATUS_OPEN,
                    ));
            // 循环检查每个项目，不符合参与条件或者已经参与过的话就继续处理下一个项目
            $this->logger->info(__METHOD__ . ' START of processing surveyPartners userId=' . $user->getId());
            foreach($surveyPartners as $surveyPartner){
                $this->logger->debug(__METHOD__ . ' check started. id=' . $surveyPartner->getId());
                // 核对用户信息是否满足该项目的参与条件
                $validResult = $this->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
                
                if($validResult['result'] == 'success'){
                    // 满足参与条件
                    $this->logger->debug(__METHOD__ . ' survey valid for this user.');
                } else {
                    // 不满足参与条件，直接处理下一个问卷项目
                    $this->logger->debug(__METHOD__ . ' survey not valid for this user.');
                    continue;
                }

                // 检查这个用户的参与记录 from surveyPartnerParticipationHistory
                $count = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->countByUserAndSurveyPartner($user, $surveyPartner);
                if($count <= 1){
                    // 没有记录或者只有init，可以参与
                    $this->logger->debug(__METHOD__ . ' only init history or no history');
                } elseif($count == 2){
                    // 有forward状态，需要判断项目是否允许中途退出
                    if(true == $surveyPartner->getReentry()){
                        // 如果该项目允许中途退出的话，就可以继续参加
                        $this->logger->debug(__METHOD__ . ' survey allow reentry');
                    } else {
                        // 该项目不允许继续参加，不可参与
                        $this->logger->debug(__METHOD__ . ' survey do not allow reentry');
                        continue;
                    }
                } else {
                    // 有3个历史记录，已经参与过了，不允许再次参与
                    $this->logger->debug(__METHOD__ . ' this user already participated.');
                    continue;
                }
                // 检查通过，添加至availableSurveyPartners
                $this->logger->debug(__METHOD__ . ' check passed. id=' . $surveyPartner->getId());
                array_push($availableSurveyPartners, $surveyPartner);
            }
            $this->logger->info(__METHOD__ . ' END of processing surveyPartners userId=' . $user->getId() . ' availableSurveyPartners count: ' . count($availableSurveyPartners));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' ErrorMsg:   ' . $e->getMessage());
            $this->logger->error(__METHOD__ . ' ErrorStack:   ' . $e->getTraceAsString());
        }

        $this->logger->info(__METHOD__ . ' END userId=' . $user->getId());
        return $availableSurveyPartners;
    }

    /**
     * 获取inforamtion页面所需的信息
     * 记录点击状态
     * @param User $user
     * @param string $surveyPartnerId
     * @param array $locationInfo
     * @return array {
     *               status participable/unparticipable
     *               title  问卷编号 + 问卷标题
     *               content 问卷的说明内容， 可以为null
     *               loi 问卷所需的估计时间
     *               url 重定向至问卷的入口url
     *               difficulty 问卷难易度
     *               completePoint 完成时可获得的积分数
     *               errMsg 系统出错时的错误信息
     *         }
     */
    public function processInformation(User $user, $surveyPartnerId, $locationInfo){
        $this->logger->debug(__METHOD__ . ' START userId=' . $user->getId() . ' surveyPartnerId=' . $surveyPartnerId);
        $rtn = array();
        $rtn['status'] = 'failure';
        try{
            $surveyPartner = null;
            if(self::TEST_USER_EMAIL == $user->getEmail()){
                // 如果是测试用用户，检查这个项目是否存在并且处于init状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartnerId,
                        'status' => SurveyPartner::STATUS_INIT
                        ));
                $this->logger->info(__METHOD__ . ' This is test user email(' . self::TEST_USER_EMAIL . ') ');
            } else {
                // 检查这个项目是否存在并且处于open状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartnerId,
                        'status' => SurveyPartner::STATUS_OPEN
                        ));
            }
            
            if(is_null($surveyPartner)){
                // open状态的项目不存在，更改返回状态为不可参与
                $errMsg = 'Survey is not exist(open). surveyPartnerId=' . $surveyPartnerId;
                $this->logger->debug(__METHOD__ . ' ' . $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }

            $rtn['title'] = $this->generateSurveyTitleWithSurveyId($surveyPartner);
            $rtn['content'] = $surveyPartner->getContent();
            $rtn['loi'] = $surveyPartner->getLoi();
            $rtn['url'] = $surveyPartner->getUrl();
            // Todo 根据loi来定难度
            $rtn['difficulty'] = '简单';
            $rtn['completePoint'] = $surveyPartner->getCompletePoint();

            // 检查这个用户的参与记录 from surveyPartnerParticipationHistory
            $surveyPartnerParticipationHistorys = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    ));

            if(0 == count($surveyPartnerParticipationHistorys)){
                // 正常的情况，没有参与记录时，增加一条init状态的参与记录
                $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
                $surveyPartnerParticipationHistory->setUser($user);
                $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
                $surveyPartnerParticipationHistory->setStatus(SurveyPartnerParticipationHistory::STATUS_INIT);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());
                $this->em->persist($surveyPartnerParticipationHistory);

                $this->em->flush();
                $this->logger->debug(__METHOD__ . ' normal no participation history.');
                $rtn['status'] = 'success';
                return $rtn;
            } elseif(1 == count($surveyPartnerParticipationHistorys)){
                // 正常的情况，已经点击过information page，有了一条init状态的记录，允许参加，不做任何状态变更
                $this->logger->debug(__METHOD__ . ' already has a init participation history.');
                $rtn['status'] = 'success';
                return $rtn;
            } elseif(2 == count($surveyPartnerParticipationHistorys)){
                // 已经有了forward状态，需要判断该项目是否允许中途退出
                if(true == $surveyPartner->getReentry()){
                    // 该项目允许中途退出，不更新状态
                    $rtn['status'] = 'success';
                    $this->logger->debug(__METHOD__ . ' normal situation with a reentry forward.');
                    return $rtn;
                } else {
                    // 该项目不允许中途退出
                    $this->logger->debug(__METHOD__ . ' not a reentry forward.');
                }
            } else {
                // 已经有了c/s/q/e 不允许继续参与
                $this->logger->debug(__METHOD__ . ' already has a c/s/q/e participation history.');
            }
            $errMsg = 'Already participated. userId=' . $user->getId() . ' surveyPartnerId=' . $surveyPartnerId;
            $rtn['status'] = 'participated';
            $rtn['errMsg'] = $errMsg;
        } catch (\Exception $e){
            // 任何系统级别的错误都直接返回error状态
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $e->getMessage();
        }
        $this->logger->debug(__METHOD__ . ' END ' . json_encode($rtn));
        return $rtn;
    }


    /**
     * 判断该用户是否有资格回答这个问卷
     * 
     * @param SurveyPartner $surveyPartner 问卷信息
     * @param User $user 用户信息
     * @param array $locationInfo 地区信息
     * @return array (
     *               'result' => 'success' or errmsg
     *               )
     */
    public function isValidSurveyPartnerForUser(SurveyPartner $surveyPartner, User $user, array $locationInfo){
        $this->logger->debug(__METHOD__ . ' START userId=' . $user->getId() . ' surveyId=' . $surveyPartner->getSurveyId() );

        $rtn = array();
        if(self::TEST_USER_EMAIL == $user->getEmail()){
            // 如果是测试用户的话，不做细节检查
            $rtn['result'] = 'success';
            return $rtn;
        }

        $birthday = $user->getUserProfile()->getBirthday();
        $age = \DateTime::createFromFormat('Y-m-d', $birthday)->diff(new \DateTime())->y;
        $sex = $user->getUserProfile()->getSex();
        if(1 == $sex){
            // 1 是男的
            $gender = SurveyPartner::GENDER_MALE;
        } else {
            $gender = SurveyPartner::GENDER_FEMALE;
        }

        // 1 性别要求检查
        if(SurveyPartner::GENDER_BOTH == $surveyPartner->getGender()){

        } else {
            if($gender != $surveyPartner->getGender()){
                // 项目有男女限制，且该用户的性别不符合要求
                $rtn['result'] = 'genderCheckFailed';
                return $rtn;
            }
        }

        // 2 年龄检查
        if($age < $surveyPartner->getMinAge()){
            $rtn['result'] = 'ageCheckFailed';
            return $rtn;
        }
        if($age > $surveyPartner->getMaxAge()){
            $rtn['result'] = 'ageCheckFailed';
            return $rtn;
        }

        // 3 地区检查 最后做,任意匹配就结束
        if(false == $locationInfo['status']){
            // 没有找到location信息
            $rtn['result'] = 'success';
            return $rtn;
        }

        if(is_null($surveyPartner->getProvince()) && is_null($surveyPartner->getCity())){
            // 没有设置地域限制
            $rtn['result'] = 'success';
            return $rtn;
        }

        if(strpos($surveyPartner->getProvince(), str_replace('省', '', $locationInfo['province']))){
            // 省份匹配上了
            $rtn['result'] = 'success';
            return $rtn;
        }

        if(strpos($surveyPartner->getCity(), str_replace('市', '', $locationInfo['city']))){
            // 市匹配上了
            $rtn['result'] = 'success';
            return $rtn;
        }
        // 都没匹配上
        $rtn['result'] = 'locationCheckFailed';
        $this->logger->debug(__METHOD__ . ' END');
        return $rtn;
    }

    /**
     * 获取重定向至问卷的入口URL
     * 1. 检查该项目是否存在并且处于open状态
     *    不存在或者非open状态的话，直接返回错误
     * 2. 检查这个用户是否可以回答这个问卷
     * 3. 根据参与记录的状态判断可否回答这个问卷
     *    
     * @param User $user
     * @param string $surveyPartnerId
     * @param array $locationInfo
     * @return array (
     *               status
     *               surveyUrl
     *               reentry
     *               errMsg
     *         )
     */
    public function redirectToSurvey(User $user, $surveyPartnerId, $locationInfo){
        $this->logger->debug(__METHOD__ . ' START userId=' . $user->getId() . ' surveyPartnerId=' . $surveyPartnerId);

        $rtn = array();
        $rtn['status'] = 'failure';

        try{
            $surveyPartner = null;
            if(self::TEST_USER_EMAIL == $user->getEmail()){
                // 测试用户的话，检查这个项目是否存在并且处于init状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartnerId,
                        'status' => SurveyPartner::STATUS_INIT
                        ));
                $this->logger->info(__METHOD__ . ' This is test user email(' . self::TEST_USER_EMAIL . ') ');
            } else {
                // 检查这个项目是否存在并且处于open状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartnerId,
                        'status' => SurveyPartner::STATUS_OPEN
                        ));
            }

            if(is_null($surveyPartner)){
                // open状态的项目不存在，结束处理，返回状态为不可参与
                $errMsg = 'Survey is not exist(open). surveyPartnerId=' . $surveyPartnerId;
                $this->logger->debug(__METHOD__ . ' errMsg: ' . $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }
            // 替换标准问卷url中的__UID__部分为userId
            $rtn['surveyUrl'] = $this->generateSurveyUrlForUser($surveyPartner->getUrl(), $user->getId());

            // 检查这个用户是否符合这个项目的要求
            $validResult = $this->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
            if($validResult['result'] != 'success'){
                // 用户不符合这个项目的参与要求，结束处理，返回状态为不可参与
                $this->logger->debug(__METHOD__ . ' this user is not allowed for this survey: ' . $validResult['result']);
                $rtn['status'] = 'notallowed';
                $rtn['errMsg'] = $validResult['result'];
                return $rtn;
            }

            // 检查这个用户的参与记录 from surveyPartnerParticipationHistory
            $surveyPartnerParticipationHistorys = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    ));
            if(0 == count($surveyPartnerParticipationHistorys)){
                // 没有参与记录时，直接新建记录为forward状态 （一般来说这个是不太可能的，保险起见做上处理）
                $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
                $surveyPartnerParticipationHistory->setUser($user);
                $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
                $surveyPartnerParticipationHistory->setStatus(SurveyPartnerParticipationHistory::STATUS_INIT);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());
                $this->em->persist($surveyPartnerParticipationHistory);

                $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
                $surveyPartnerParticipationHistory->setUser($user);
                $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
                $surveyPartnerParticipationHistory->setStatus(SurveyPartnerParticipationHistory::STATUS_FORWARD);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());
                $this->em->persist($surveyPartnerParticipationHistory);

                $this->em->flush();
                $rtn['status'] = 'success';
                $this->logger->debug(__METHOD__ . ' not reasonable situation, but...');
                return $rtn;
            } elseif(1 == count($surveyPartnerParticipationHistorys)){
                // 正常的情况，已经点击过information page，有了一条init状态的记录，增加一条forward状态的记录
                $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
                $surveyPartnerParticipationHistory->setUser($user);
                $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
                $surveyPartnerParticipationHistory->setStatus(SurveyPartnerParticipationHistory::STATUS_FORWARD);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());
                $this->em->persist($surveyPartnerParticipationHistory);

                $this->em->flush();
                $rtn['status'] = 'success';
                $this->logger->debug(__METHOD__ . ' normal situation add a forward participation history.');
                return $rtn;
            } elseif(2 == count($surveyPartnerParticipationHistorys)){
                // 已经有了forward状态，需要判断该项目是否允许中途退出
                if(true == $surveyPartner->getReentry()){
                    // 该项目允许中途退出，不更新状态
                    $rtn['status'] = 'success';
                    $this->logger->debug(__METHOD__ . ' normal situation with a reentry forward.');
                    return $rtn;
                } else {
                    // 该项目不允许中途退出
                    $this->logger->debug(__METHOD__ . ' not a reentry forward.');
                }
            } else {
                // 已经有了c/s/q/e 不允许继续参与
                $this->logger->debug(__METHOD__ . ' already has a c/s/q/e participation history.');
            }
            $errMsg = 'Already participated. userId=' . $user->getId() . ' surveyPartnerId=' . $surveyPartnerId;
            $rtn['status'] = 'participated';
            $rtn['errMsg'] = $errMsg;
        } catch (\Exception $e){
            // 任何系统级别的错误都直接返回error状态
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $e->getMessage();
        }
        $this->logger->debug(__METHOD__ . ' END ' . json_encode($rtn));
        return $rtn;
    }


    /**
     * tripleS的referer例子：
     * http:\/\/r.researchpanelasia.com\/redirect\/reverse\/9ed68ef0e7615306a793792905330e85\/error?uid=099104111d001exljg
     */
    public function isValidEndlinkReferer($referer, $key){
        if(empty($referer)){
            // no referer found, allow because some antivirus softs will clear referer
            return true;
        } else {
            // has referer
            // only allow for triples at this moment
            if(! strpos($referer, self::VALID_REFERER_DOMAIN)){
                // 如果不含有 VALID_REFERER_DOMAIN 视为非法
                return false;
            }

            if(! strpos($referer, $key)){
                // 如果不含有 $key 视为非法
                return false;
            }

        }
        return true;
    }

    /**
     * 处理来自TripleS对endlink的request
     * @return array(
     *              status: success 正常处理结束
     *              status: failure 非正常处理结束
     *              )
     */
    public function processEndlink($userId, $answerStatus, $surveyId, $partnerName, $key, $clientIp){
        $this->logger->debug(__METHOD__ . ' START userId=' . $userId . ' surveyId=' . $surveyId . 'partnerName=' . $partnerName . ' answerStatus=' . $answerStatus . ' $key=' . $key);

        $rtn = array();
        $rtn['status'] = 'failure';
        $rtn['answerStatus'] = $this->convertAnswerStatusToHistoryStatus($answerStatus);
        $rtn['surveyId'] = $surveyId;
        $rtn['partnerName'] = $partnerName;
        $rtn['key'] = $key;

        try{
            // 先检查这个用户是否存在
            $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
            if(is_null($user)){
                // 用户不存在 不做继续处理
                $errMsg = 'User not exist. userId = ' . $userId;
                $this->logger->warn(__METHOD__ . ' ' . $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }

            // 用户存在的情况
            $surveyPartner = null;
            if(self::TEST_USER_EMAIL == $user->getEmail()){
                // 测试用户的话，检查这个项目是否存在并且处于init状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('surveyId' => $surveyId,
                        'partnerName' => $partnerName,
                        'status' => SurveyPartner::STATUS_INIT
                        ));
            } else {
                // 检查这个项目是否存在并且处于open状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('surveyId' => $surveyId,
                        'partnerName' => $partnerName,
                        'status' => SurveyPartner::STATUS_OPEN
                        ));
            }

            if(is_null($surveyPartner)){
                // 该项目不处于open状态的话，不做积分处理
                $errMsg = 'Opening survey is not exist. surveyId=' . $surveyId . ' partnerName=' . $partnerName;
                $this->logger->warn(__METHOD__ . ' '. $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }
            // 该项目处于open状态
            $rtn['title'] = $this->generateSurveyTitleWithSurveyId($surveyPartner);

            
            // 检查这个用户的参与记录 from surveyPartnerParticipationHistory
            $surveyPartnerParticipationHistorys = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    ));
            if(2 == count($surveyPartnerParticipationHistorys)){
                // 有两条参与记录
                // 查找forward状态的历史记录
                $forwardParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    'status' => SurveyPartnerParticipationHistory::STATUS_FORWARD,
                    ));

                if(empty($forwardParticipationHistory)){
                    // 如果没有forward状态的历史记录，直接结束
                    $errMsg = 'Participation status is not correct. userId = ' . $userId . ' surveyId=' . $surveyId . ' partnerName=' . $partnerName;
                    $this->logger->warn(__METHOD__ . ' '. $errMsg);
                    $rtn['status'] = 'failure';
                    $rtn['errMsg'] = $errMsg;
                    return $rtn;
                }

                if($forwardParticipationHistory->getClientIp() != $clientIp){
                    // 如果参与时的clientIp 不等于endlink的clientIp，也不做处理
                    $errMsg = 'Participation clientIp does not match. userId = ' . $userId . ' surveyId=' . $surveyId . ' partnerName=' . $partnerName;
                    $this->logger->warn(__METHOD__ . ' '. $errMsg);
                    $rtn['status'] = 'failure';
                    $rtn['errMsg'] = $errMsg;
                    return $rtn;
                }

                // 如果返回状态是complete的话，检查参与的开始时间(forward状态的记录时间)到现在所经过的时间是否小于预估LOI的1/3，如果低于这个时间，视为非法的结果，不处理
                if(SurveyPartnerParticipationHistory::STATUS_COMPLETE == $rtn['answerStatus']){
                    $now = new \DateTime();

                    $diff = $now->diff($forwardParticipationHistory->getCreatedAt());
                    $minutes = $diff->days * 24 * 60;
                    $minutes += $diff->h * 60;
                    $minutes += $diff->i;

                    if($minutes <= $surveyPartner->getLoi()/3){
                        $errMsg = 'This is a too fast complete. userId = ' . $userId . ' surveyId=' . $surveyId . ' partnerName=' . $partnerName;
                        $this->logger->warn(__METHOD__ . ' '. $errMsg);
                        $rtn['status'] = 'failure';
                        $rtn['errMsg'] = $errMsg;
                        return $rtn;
                    }
                }
                
                // 检查都通过了，开始正常处理

                // 先增加一条结束状态的历史记录
                $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
                $surveyPartnerParticipationHistory->setUser($user);
                $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
                $surveyPartnerParticipationHistory->setStatus($rtn['answerStatus']);
                $surveyPartnerParticipationHistory->setUKey($key);
                $surveyPartnerParticipationHistory->setClientIp($clientIp);
                $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());
                $this->em->persist($surveyPartnerParticipationHistory);

                
                // 发积分
                $result = $this->reward($surveyPartner, $user, $rtn['answerStatus'], $key);
                $this->em->flush();
                $rtn['rewardedPoint'] = $result['rewardedPoint'];
                $rtn['ticketCreated'] = $result['ticketCreated'];
                $rtn['status'] = 'success';
                $this->logger->debug(__METHOD__ . ' endlink process success.');
                return $rtn;
            } else {
                // 有2条以外的参与记录，视为非正常结果，不做任何处理
                // 非正确的参与状态，不加积分
                $errMsg = 'Participation count is not correct. userId = ' . $userId . ' surveyId=' . $surveyId . ' partnerName=' . $partnerName . ' count of history=' . count($surveyPartnerParticipationHistorys);
                $this->logger->warn(__METHOD__ . ' '. $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
            }
        } catch (\Exception $e){
            // 任何系统级别的错误都直接返回error状态
            $this->logger->error(__METHOD__ . ' ' . $e->getMessage());
            $this->logger->error(__METHOD__ . ' ' . $e->getTraceAsString());
            $rtn['status'] = 'error';
            $rtn['errMsg'] = $e->getMessage();
        }
        $this->logger->debug(__METHOD__ . ' END ' . json_encode($rtn));
        return $rtn;
    }


    private function generateSurveyUrlForUser($url, $userId){
        $surveyUrl = str_replace('__UID__', $userId, $url);
        return $surveyUrl;
    }

    private function convertAnswerStatusToHistoryStatus($answerStatus){
        if($answerStatus == SurveyPartnerParticipationHistory::STATUS_COMPLETE){
            return $answerStatus;
        }
        elseif($answerStatus == SurveyPartnerParticipationHistory::STATUS_SCREENOUT){
            return $answerStatus;
        }
        elseif($answerStatus == SurveyPartnerParticipationHistory::STATUS_QUOTAFULL){
            return $answerStatus;
        }
        else{
            return SurveyPartnerParticipationHistory::STATUS_ERROR;
        }
    }

    /**
     * 实际上是private 函数，为了测试方便，改为public
     */
    public function reward($surveyPartner, $user, $answerStatus, $key){
        $result = array();
        $result['rewardedPoint'] = 0;
        $result['ticketCreated'] = false;
        if($answerStatus == SurveyPartnerParticipationHistory::STATUS_COMPLETE){

            // 给用户加积分
            $this->pointService->addPoints(
                $user,
                $surveyPartner->getCompletePoint(),
                CategoryType::SURVEY_PARTNER_COST,
                TaskType::SURVEY,
                $this->generateSurveyTitleWithSurveyId($surveyPartner)
                );

            // 同时给邀请人加积分(10%)
            $this->pointService->addPointsForInviter(
                $user,
                $surveyPartner->getCompletePoint() * 0.1,
                CategoryType::EVENT_INVITE_SURVEY,
                TaskType::RENTENTION,
                '您的好友' . $user->getNick() . '完成了一份商业问卷'
                );

            // 发奖券
            $prizeTicket = $this->prizeTicketService->createPrizeTicket(
                $user,
                PrizeItem::TYPE_BIG,
                $key,
                $surveyPartner->getSurveyId()
                );

            $result['rewardedPoint'] = $surveyPartner->getCompletePoint();

            if($prizeTicket){
                $result['ticketCreated'] = true;
            }
        } elseif($answerStatus == SurveyPartnerParticipationHistory::STATUS_SCREENOUT){
            // 给用户加积分
            $this->pointService->addPoints(
                    $user,
                    $surveyPartner->getScreenoutPoint(),
                    CategoryType::SURVEY_PARTNER_EXPENSE,
                    TaskType::RENTENTION,
                    $this->generateSurveyTitleWithSurveyId($surveyPartner)
                    );
            // 发奖券
            $prizeTicket = $this->prizeTicketService->createPrizeTicket(
                $user,
                PrizeItem::TYPE_SMALL,
                $key,
                $surveyPartner->getSurveyId()
                );

            $result['rewardedPoint'] = $surveyPartner->getScreenoutPoint();

            if($prizeTicket){
                $result['ticketCreated'] = true;
            }
        } elseif($answerStatus == SurveyPartnerParticipationHistory::STATUS_QUOTAFULL){
            // 给用户加积分
            $this->pointService->addPoints(
                    $user,
                    $surveyPartner->getQuotafullPoint(),
                    CategoryType::SURVEY_PARTNER_EXPENSE,
                    TaskType::RENTENTION,
                    $this->generateSurveyTitleWithSurveyId($surveyPartner)
                    );
            // 发奖券
            $prizeTicket = $this->prizeTicketService->createPrizeTicket(
                $user,
                PrizeItem::TYPE_SMALL,
                $key,
                $surveyPartner->getSurveyId()
                );

            $result['rewardedPoint'] = $surveyPartner->getQuotafullPoint();

            if($prizeTicket){
                $result['ticketCreated'] = true;
            }
        } else{
            
        }
        return $result;
    }

    public function generateSurveyTitleWithSurveyId($surveyPartner){
        return $surveyPartner->getId() . ' ' . $surveyPartner->getTitle();
    }

}