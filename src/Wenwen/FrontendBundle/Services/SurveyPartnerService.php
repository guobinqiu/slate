<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Base64Url\Base64Url;

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

    private $testUserEmails = array(
        'rpa-sys-china@d8aspring.com', //id =2718082
        'ds-Product-china@d8aspring.com' //id =2717895
        );

    const VALID_REFERER_DOMAIN = 'r.researchpanelasia.com';

    const SECRET_KEY = "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3";  // 编码时的混杂HEX key 不要随便改哦

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
            
            if(in_array($user->getEmail(), $this->testUserEmails)){
                // 如果是测试用户，则显示所有处于init状态的项目
                $surveyPartners = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findBy(
                    array(
                        'status' => SurveyPartner::STATUS_INIT,
                        ));
                $this->logger->info(__METHOD__ . ' This is test a user. email=' . $user->getEmail());
                return $surveyPartners;
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
                    $this->logger->warn(__METHOD__ . ' survey is not valid for user_id='. $user->getId().' reason:' . $validResult['result']);
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
            if(in_array($user->getEmail(), $this->testUserEmails)){
                // 如果是测试用用户，检查这个项目是否存在并且处于init状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartnerId,
                        'status' => SurveyPartner::STATUS_INIT
                        ));
                $this->logger->info(__METHOD__ . ' This is test user. email=' . $user->getEmail());
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
                $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_INIT);
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
        if($surveyPartner->getNewUserOnly()){
            // 这个项目只允许新用户参加
            $now = new \DateTime();
            // 注册三天以上的用户不显示这个类型的问卷
            if($user->getRegisterCompleteDate() <= $now->sub(new \DateInterval('P03D'))){
                $rtn['result'] = 'OnlyForNewUser';
                return $rtn;
            }
        }

        if(in_array($user->getEmail(), $this->testUserEmails)){
            // 如果是测试用户的话，不做细节检查
            $rtn['result'] = 'success';
            return $rtn;
        }

        // 1 性别要求检查
        $sex = $user->getUserProfile()->getSex();
        if($sex){
            if(1 == $sex){
                // 1 是男的
                $gender = SurveyPartner::GENDER_MALE;
            } else {
                $gender = SurveyPartner::GENDER_FEMALE;
            }
            if(SurveyPartner::GENDER_BOTH == $surveyPartner->getGender()){

            } else {
                if($gender != $surveyPartner->getGender()){
                    // 项目有男女限制，且该用户的性别不符合要求
                    $rtn['result'] = 'genderCheckFailed';
                    return $rtn;
                }
            }
        }
        
        // 2 年龄检查
        $birthday = $user->getUserProfile()->getBirthday();
        if($birthday){
            $age = \DateTime::createFromFormat('Y-m-d', $birthday)->diff(new \DateTime())->y;
            if($age < $surveyPartner->getMinAge()){
                $rtn['result'] = 'ageCheckFailed';
                return $rtn;
            }
            if($age > $surveyPartner->getMaxAge()){
                $rtn['result'] = 'ageCheckFailed';
                return $rtn;
            }
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

        if(strpos($surveyPartner->getProvince(), str_replace('省', '', $locationInfo['province'])) !== false){
            // 省份匹配上了
            $rtn['result'] = 'success';
            return $rtn;
        }

        if(strpos($surveyPartner->getCity(), str_replace('市', '', $locationInfo['city'])) !== false){
            // 市匹配上了
            $rtn['result'] = 'success';
            return $rtn;
        }
        // 都没匹配上
        $rtn['result'] = 'locationCheckFailed . city=' . $locationInfo['city'] . ' province=' . $locationInfo['province'];
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
            if(in_array($user->getEmail(), $this->testUserEmails)){
                // 测试用户的话，检查这个项目是否存在并且处于init状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartnerId,
                        'status' => SurveyPartner::STATUS_INIT
                        ));
                $this->logger->info(__METHOD__ . ' This is test user. email=' . $user->getEmail());
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
                $this->logger->warn(__METHOD__ . ' errMsg: ' . $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }

            $token = '';
            if(SurveyPartner::PARTNER_FORSURVEY == $surveyPartner->getPartnerName()){
                // forSurvey的时候，用 userId, surveyPartnerId, secretKey编码而成token去替换url中的__UID__
                $token = $this->encodeToken($user->getId(), $surveyPartnerId);
                $rtn['surveyUrl'] = $this->generateSurveyUrlForUser($surveyPartner->getUrl(), $token);
            } else {
                // 替换标准问卷url中的__UID__部分为userId
                $rtn['surveyUrl'] = $this->generateSurveyUrlForUser($surveyPartner->getUrl(), $user->getId());
            }

            // 检查这个用户是否符合这个项目的要求
            if(!in_array($user->getEmail(), $this->testUserEmails)){
                $validResult = $this->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
                if($validResult['result'] != 'success'){
                    // 用户不符合这个项目的参与要求，结束处理，返回状态为不可参与
                    $this->logger->warn(__METHOD__ . ' user_id='. $user->getId().' is not allowed for this survey. reason: ' . $validResult['result']);
                    $rtn['status'] = 'notallowed';
                    $rtn['errMsg'] = $validResult['result'];
                    return $rtn;
                }
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
                $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_INIT);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());
                $this->em->persist($surveyPartnerParticipationHistory);

                $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
                $surveyPartnerParticipationHistory->setUser($user);
                $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
                $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_FORWARD);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setUKey($token);
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
                $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_FORWARD);
                $surveyPartnerParticipationHistory->setClientIp($locationInfo['clientIp']);
                $surveyPartnerParticipationHistory->setUKey($token);
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
    public function isValidEndlink($answerStatus, $partnerName, $referer, $uid, $key){
        $rtn = array();
        $rtn['status'] = false;
        $rtn['key'] = 'SYSERROR';
        $rtn['errMsg'] = '';

        // 检查answerStatus是否合法
        if(SurveyStatus::STATUS_COMPLETE == $answerStatus){

        } else if(SurveyStatus::STATUS_SCREENOUT == $answerStatus){

        } else if(SurveyStatus::STATUS_QUOTAFULL == $answerStatus){

        } else if(SurveyStatus::STATUS_ERROR == $answerStatus){

        } else {
            $rtn['status'] = false;
            $rtn['key'] = 'INVALIDACCESS';
            $rtn['errMsg'] = 'Not a valid answerStatus. answerStatus=' . $answerStatus;
            $this->logger->warn(__METHOD__ . ' ' . $rtn['errMsg']);
            return $rtn;
        }

        // 检查partnerName以及其他参数是否合法是否合法
        if(SurveyPartner::PARTNER_FORSURVEY == $partnerName){
            // forSurvey 先不检查referer了
            $rtn['status'] = true;
            $rtn['key'] = $uid;
            return $rtn;
        } else if(SurveyPartner::PARTNER_TRIPLES == $partnerName){
            // TripleS 要检查referer
            $rtn['key'] = $key;
            if(empty($referer)){
                // no referer found, allow because some antivirus softs will clear referer
                $rtn['status'] = true;
                return $rtn;
            } else {
                // has referer
                // only allow for triples at this moment
                if(! strpos($referer, self::VALID_REFERER_DOMAIN)){
                    // 如果不含有 VALID_REFERER_DOMAIN 视为非法
                    $rtn['status'] = false;
                    $rtn['key'] = 'INVALIDACCESS';
                    $rtn['errMsg'] = 'Referer not matched. referer=' . $referer;
                    $this->logger->warn(__METHOD__ . ' ' . $rtn['errMsg']);
                    return $rtn;
                }

            }
            $rtn['status'] = true;
            return $rtn;
        } else {
            $rtn['status'] = false;
            $rtn['key'] = 'INVALIDACCESS';
            $rtn['errMsg'] = 'Not a valid partnerName. partnerName=' . $partnerName;
            $this->logger->warn(__METHOD__ . ' ' . $rtn['errMsg']);
            return $rtn;
        }
        return $rtn;
    }

    public function processEndlink($uid, $answerStatus, $surveyId, $partnerName, $key, $clientIp){
        if(SurveyPartner::PARTNER_FORSURVEY == $partnerName){
            return $this->processForSurveyEndlink($uid, $answerStatus, $clientIp);
        } else {
            // uid 就是 userId
            return $this->processTriplesEndlink($uid, $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        }
    }

    public function processForSurveyEndlink($uid, $answerStatus, $clientIp){
        $this->logger->debug(__METHOD__ . ' START uid=' . $uid . ' answerStatus=' . $answerStatus);
        $rtn = array();
        $rtn['status'] = 'failure';
        $rtn['answerStatus'] = $answerStatus;
        $rtn['key'] = $uid;
        $rtn['errMsg'] = '';

        // 先解码uid，获得userId, surveyPartnerId
        $params = $this->decodeToken($uid);

        // 检查uid是否合法
        if(!$this->isValidParams($params)){
            $errMsg = 'Not a valid uid. uid=' . $uid;
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        $userId = $params[0];
        $surveyPartnerId = $params[1];

        $this->logger->debug(__METHOD__ . ' userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId);

        // 检查该项目是否存在
        $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneById($surveyPartnerId);
        if(empty($surveyPartner)){
            $errMsg = 'Not exist surveyPartnerId. surveyPartnerId=' . $surveyPartnerId;
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        // 检查用户是否存在
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
        if(empty($user)){
            $errMsg = 'Not exist userId. userId=' . $userId;
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        if(in_array($user->getEmail(), $this->testUserEmails)){
            // 如果是测试用用户的话，检查项目是否处于init状态
            if(SurveyPartner::STATUS_INIT != $surveyPartner->getStatus()){
                $errMsg = 'Test user, surveyPartner not in init status. surveyPartnerId=' . $surveyPartnerId;
                $this->logger->warn(__METHOD__ . ' ' . $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }

        } else {
            // 如果是普通用用户的话，检查项目是否处于open状态
            if(SurveyPartner::STATUS_OPEN != $surveyPartner->getStatus()){
                $errMsg = 'Normal user, surveyPartner not in open status. surveyPartnerId=' . $surveyPartnerId;
                $this->logger->warn(__METHOD__ . ' ' . $errMsg);
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = $errMsg;
                return $rtn;
            }

        }

        // 检查有没有对应未处理的forward记录
        // 1. 检索出这个user 在这个surveyPartner里的所有参与记录
        $surveyPartnerParticipationHistorys = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    ));

        if(2 != count($surveyPartnerParticipationHistorys)){
            // 只有两条参与记录的时候认为是正常的
            $errMsg = 'Participation history is not correct. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId;
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        // 2. 检索看看有没有forward状态的参与记录
        $forwardParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    'status' => SurveyStatus::STATUS_FORWARD,
                    ));
        if(empty($forwardParticipationHistory)){
            $errMsg = 'Participation history in forward is not exist. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId;
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        // 3. forward记录存在的情况比对 ukey的值和uid是否一致
        if($uid != $forwardParticipationHistory->getUKey()){
            $errMsg = 'Participation history UKey not match uid. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId;
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        // 4. forward记录存在的情况比对 forward的时候的clienIp和clientIp是否一致
        if($forwardParticipationHistory->getClientIp() != $clientIp){
            // 如果参与时的clientIp 不等于endlink的clientIp，也不做处理
            $errMsg = 'Participation clientIp does not match. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId;
            $this->logger->warn(__METHOD__ . ' '. $errMsg);
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = $errMsg;
            return $rtn;
        }

        // 如果返回状态是complete的话，检查参与的开始时间(forward状态的记录时间)到现在所经过的时间是否小于预估LOI的1/4，如果低于这个时间，视为非法的结果，处理为screenout
        if(SurveyStatus::STATUS_COMPLETE == $rtn['answerStatus']){
            $now = new \DateTime();

            $diffSeconds = strtotime($now->format('Y-m-d H:i:s')) - strtotime($forwardParticipationHistory->getCreatedAt()->format('Y-m-d H:i:s'));

            if($diffSeconds <= ($surveyPartner->getLoi() * 60 / 4)){
                $errMsg = 'This is a too fast complete. userId = ' . $userId . ' surveyPartnerId=' . $surveyPartnerId;
                $this->logger->warn(__METHOD__ . ' '. $errMsg);
                // 完成回答过快，状态改为screenout
                $rtn['answerStatus'] = SurveyStatus::STATUS_SCREENOUT;
                $rtn['errMsg'] = $errMsg;
            }
        }


        // 检查都通过了，开始正常处理

        // 增加一条结束状态的历史记录
        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setStatus($rtn['answerStatus']);
        $surveyPartnerParticipationHistory->setUKey($uid);
        $surveyPartnerParticipationHistory->setClientIp($clientIp);
        $surveyPartnerParticipationHistory->setComment($rtn['errMsg']);
        $surveyPartnerParticipationHistory->setCreatedAt(new \DateTime());

        $this->em->persist($surveyPartnerParticipationHistory);

        // 发积分
        $result = $this->reward($surveyPartner, $user, $rtn['answerStatus'], $uid);
        $this->em->flush();

        $rtn['title'] = $this->generateSurveyTitleWithSurveyId($surveyPartner);
        $rtn['rewardedPoint'] = $result['rewardedPoint'];
        $rtn['ticketCreated'] = $result['ticketCreated'];
        $rtn['status'] = 'success';
        $this->logger->debug(__METHOD__ . ' endlink process success.');
        return $rtn;
    }

    /**
     * 处理来自TripleS对endlink的request
     * @return array(
     *              status: success 正常处理结束
     *              status: failure 非正常处理结束
     *              )
     */
    public function processTriplesEndlink($userId, $answerStatus, $surveyId, $partnerName, $key, $clientIp){
        $this->logger->debug(__METHOD__ . ' START userId=' . $userId . ' surveyId=' . $surveyId . 'partnerName=' . $partnerName . ' answerStatus=' . $answerStatus . ' $key=' . $key);

        $rtn = array();
        $rtn['status'] = 'failure';
        $rtn['answerStatus'] = $answerStatus;
        $rtn['surveyId'] = $surveyId;
        $rtn['partnerName'] = $partnerName;
        $rtn['key'] = $key;
        $rtn['errMsg'] = '';

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
            if(in_array($user->getEmail(), $this->testUserEmails)){
                // 测试用户的话，检查这个项目是否存在并且处于init状态
                $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('surveyId' => $surveyId,
                        'partnerName' => $partnerName,
                        'status' => SurveyPartner::STATUS_INIT
                        ));
            } else {
                // Todo 要分布检查，
                // 首先，项目是否存在
                // 然后存在的项目是否已经关闭
                // 如果项目已经关闭，需要给用户提示信息

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
                    'status' => SurveyStatus::STATUS_FORWARD,
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

                // 如果返回状态是complete的话，检查参与的开始时间(forward状态的记录时间)到现在所经过的时间是否小于预估LOI的1/4，如果低于这个时间，视为非法的结果，不处理
                if(SurveyStatus::STATUS_COMPLETE == $rtn['answerStatus']){
                    $now = new \DateTime();

                    $diffSeconds = strtotime($now->format('Y-m-d H:i:s')) - strtotime($forwardParticipationHistory->getCreatedAt()->format('Y-m-d H:i:s'));

                    $this->logger->debug(__METHOD__ . ' diffSeconds='. $diffSeconds);

                    if($diffSeconds <= ($surveyPartner->getLoi() * 60 / 4)){
                        $errMsg = 'This is a too fast complete. userId = ' . $userId . ' surveyId=' . $surveyId . ' partnerName=' . $partnerName;
                        $this->logger->warn(__METHOD__ . ' '. $errMsg);
                        // 完成回答过快，状态改为screenout
                        $rtn['answerStatus'] = SurveyStatus::STATUS_SCREENOUT;
                        $rtn['errMsg'] = $errMsg;
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
                $surveyPartnerParticipationHistory->setComment($rtn['errMsg']);
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

    /**
     * 实际上是private 函数，为了测试方便，改为public
     */
    public function reward($surveyPartner, $user, $answerStatus, $key){
        $result = array();
        $result['rewardedPoint'] = 0;
        $result['ticketCreated'] = false;

        if(SurveyPartner::TYPE_EXPENSE == $surveyPartner->getType()){
            // expense类型的问卷
            // 给用户加积分
            if($answerStatus == SurveyStatus::STATUS_COMPLETE){
                $this->pointService->addPoints(
                    $user,
                    $surveyPartner->getCompletePoint(),
                    CategoryType::SURVEY_PARTNER_EXPENSE,
                    TaskType::RENTENTION,
                    $this->generateSurveyTitleWithSurveyId($surveyPartner)
                    );
                $result['rewardedPoint'] = $surveyPartner->getCompletePoint();
            } elseif($answerStatus == SurveyStatus::STATUS_SCREENOUT){
                // 给用户加积分
                $this->pointService->addPoints(
                    $user,
                    $surveyPartner->getScreenoutPoint(),
                    CategoryType::SURVEY_PARTNER_EXPENSE,
                    TaskType::RENTENTION,
                    $this->generateSurveyTitleWithSurveyId($surveyPartner)
                    );
                $result['rewardedPoint'] = $surveyPartner->getScreenoutPoint();
            } elseif($answerStatus == SurveyStatus::STATUS_QUOTAFULL){
                $this->pointService->addPoints(
                    $user,
                    $surveyPartner->getQuotafullPoint(),
                    CategoryType::SURVEY_PARTNER_EXPENSE,
                    TaskType::RENTENTION,
                    $this->generateSurveyTitleWithSurveyId($surveyPartner)
                    );
                $result['rewardedPoint'] = $surveyPartner->getQuotafullPoint();
            } else {
                // 未知状态不加积分
            }
        } else {
            // cost 类型的问卷
            if($answerStatus == SurveyStatus::STATUS_COMPLETE){

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
            } elseif($answerStatus == SurveyStatus::STATUS_SCREENOUT){
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
            } elseif($answerStatus == SurveyStatus::STATUS_QUOTAFULL){
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
        }
        return $result;
    }

    public function generateSurveyTitleWithSurveyId($surveyPartner){
        return $surveyPartner->getId() . ' ' . $surveyPartner->getTitle();
    }

    public function encodeToken($userId, $surveyPartnerId, $secretkey = self::SECRET_KEY){
        // 简单一点先，以后有需要了再加强吧
        $key = pack('H*', $secretkey);
        $plaintext = $userId . ',' . $surveyPartnerId;

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
                                 $plaintext, MCRYPT_MODE_CBC, $iv);
        $ciphertext_base64url = Base64Url::encode($iv . $encrypted);
        return $ciphertext_base64url;
    }

    public function decodeToken($ciphertext_base64url, $secretkey = self::SECRET_KEY){
        try{
            $key = pack('H*', $secretkey);
            // $decrypted = mcrypt_decrypt(MCRYPT_DES, $secretKey, Base64Url::decode($ciphertext_base64), MCRYPT_MODE_ECB, '');
            $ciphertext_dec = Base64Url::decode($ciphertext_base64url);
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv_dec = substr($ciphertext_dec, 0, $iv_size);
            $ciphertext_dec = substr($ciphertext_dec, $iv_size);
            $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
                                    $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
        } catch (\Exception $e){
            //throw $e;
            return array();
        }
        return explode(',', rtrim($plaintext_dec, "\0")); // 去掉最后一个\0 不然乱码
    }

    public function isValidParams($params){
        // 解码出来的params不等于3个的情况视为非法的token
        if(count($params) != 2){
            $errMsg = 'Not a valid token. Params counts incorrect.';
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            return false;
        }


        if(empty($params[0]) || empty($params[1])){
            $errMsg = 'Not a valid token. userId or surveyPartnerId not exist.';
            $this->logger->warn(__METHOD__ . ' ' . $errMsg);
            return false;
        }
        return true;
    }

}