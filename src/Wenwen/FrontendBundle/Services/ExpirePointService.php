<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Templating\EngineInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

/**
 * 积分清零以及邮件通知用户的功能
 * 默认不会实际清零以及发邮件
 * 调用sendEmail()和doExpiringPoint()后才会实际积分清零以及发邮件
 */
class ExpirePointService
{
    private $logger;

    private $em;

    private $parameterService;

    private $pointService;

    private $templating;

    private $mailerForUser;

    private $mailerForSys;

    private $skipEmailFlag = true;    // 模拟测试用开关 true: do not send email, false: send email

    private $skipExpiringFlag = true; // 模拟测试用开关 true: skip point expiring, false: do point expiring

    const TASK_NAME = "积分过期清零";

    const TASK_STATUS = 1;

    const TITLE_FORMAT_EXPIRING_30D = "%s, 您的%d积分再过30天就要消失啦";
    const TEMPLATE_EXPIRING_30D     = 'WenwenFrontendBundle:EmailTemplate:point_failure_for_month.html.twig';

    const TITLE_FORMAT_EXPIRING_07D = "%s, 您的%d积分再过7天就要消失啦";
    const TEMPLATE_EXPIRING_07D     = 'WenwenFrontendBundle:EmailTemplate:point_failure_for_week.html.twig';

    const TITLE_FORMAT_EXPIRED      = "%s, 您的%d积分消失了";
    const TEMPLATE_EXPIRED          = 'WenwenFrontendBundle:EmailTemplate:point_failure.html.twig';

    const SENDTO_SYSTEM_NOTIFY      = 'rpa-sys-china@d8aspring.com';
    const TEMPLATE_SYSTEM_EXPIRE_POINT_NOTIFICATION = 'WenwenFrontendBundle:EmailTemplate:System/expire_point_notification.html.twig';

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                PointService $pointService,
                                EngineInterface $templating)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->pointService = $pointService;
        $this->templating = $templating;

        $this->mailerForUser = MailerFactory::createWebpowerMailer($this->parameterService);
        $this->mailerForSys = MailerFactory::createWebpowerSignupMailer($this->parameterService);
        
    }

    /**
    * 不发邮件（默认）
    */
    public function skipEmail(){
        $this->skipEmailFlag = true;
    }

    /**
    * 发邮件
    */
    public function sendEmail(){
        $this->skipEmailFlag = false;
    }

    /**
    * 不做实际的积分清零（默认）
    */
    public function skipExpiringPoint(){
        $this->skipExpiringFlag = true;
    }

    /**
    * 积分清零
    */
    public function doExpiringPoint(){
        $this->skipExpiringFlag = false;
    }

    /**
    * 给连续150天未获得过积分的用户发邮件
    * 这些用户再过30天就要被清零了
    * 注意，邮件是否发送成功不做严格检查
    * @param \DateTime $baseDate
    * @return array(
    *              'status' => succeeded / failed
    *              'errmsg' => 错误信息 status == failed 时候出现
    *              'targetUserCount' => int,      // 总共应该通知的用户数目
    *              'notifyFailedUsers' => array(    // 通知失败的用户信息
    *                                           'id'     => int,
    *                                           'email'  => string,
    *                                           'nick'   => string,
    *                                           'points' => int,
    *                                           'errmsg' => 该用户失败的原因
    *                                          )                
    *              )
    */
    public function notifyUserExpiringIn30Days(\DateTime $baseDate){
        $this->logger->debug(__METHOD__ . " START " . PHP_EOL);
        // Prepare from and to date.
        // from = now - 151 days
        // to = now - 150 days
        $now = $baseDate->setTime(0,0,0);
        $to = (clone $now);
        $to->sub(new \DateInterval('P150D'));
        $from = (clone $now);
        $from->sub(new \DateInterval('P151D'));

        // select expiring users from user table
        $findResult = $this->findExpiringUsers($from, $to);
        if('suceeded' != $findResult['status']){
            $rtn = array(
            'status' => 'failed',
            'errmsg' => $findResult['msg'],
            'targetUserCount' => '',
            'notifyFailedUsers' => array()
            );
            return $rtn;
        }
        // notify users
        $notifyResult = $this->notifyExpiringUsers($findResult['expiringUsers'], self::TITLE_FORMAT_EXPIRING_30D, self::TEMPLATE_EXPIRING_30D);
        $rtn = array(
            'status' => 'succeeded',
            'errmsg' => '',
            'targetUserCount' => sizeof($findResult['expiringUsers']),
            'notifyFailedUsers' => $notifyResult['notifyFailedUsers']
            );
        $this->logger->debug(__METHOD__ . " END   " . PHP_EOL);
        return $rtn;
    }

    /**
    * 给连续173天未获得过积分的用户发邮件
    * 这些用户再过7天就要被清零了
    * @param \DateTime $baseDate
    * @return array(
    *              'status' => succeeded / failed
    *              'errmsg' => 错误信息 status == failed 时候出现
    *              'targetUserCount'   => int,      // 总共应该通知的用户数目
    *              'notifyFailedUsers' => array(    // 通知失败的用户信息
    *                                           'id'     => int,
    *                                           'email'  => string,
    *                                           'nick'   => string,
    *                                           'points' => int,
    *                                           'errmsg' => 该用户失败的原因
    *                                          )                
    *              )
    */
    public function notifyUserExpiringIn7Days(\DateTime $baseDate){
        $this->logger->debug(__METHOD__ . " START " . PHP_EOL);
        // Prepare from and to date.
        // from = now - 174 days
        // to = now - 173 days
        $now = $baseDate->setTime(0,0,0);
        $to = (clone $now);
        $to->sub(new \DateInterval('P173D'));
        $from = (clone $now);
        $from->sub(new \DateInterval('P174D'));
        
        // select expiring users from user table
        $findResult = $this->findExpiringUsers($from, $to);
        if('suceeded' != $findResult['status']){
            $rtn = array(
            'status' => 'failed',
            'errmsg' => $findResult['msg'],
            'targetUserCount' => '',
            'notifyFailedUsers' => array()
            );
            return $rtn;
        }
        // notify users
        $notifyResult = $this->notifyExpiringUsers($findResult['expiringUsers'], self::TITLE_FORMAT_EXPIRING_07D, self::TEMPLATE_EXPIRING_07D);
        $rtn = array(
            'status' => 'succeeded',
            'errmsg' => '',
            'targetUserCount' => sizeof($findResult['expiringUsers']),
            'notifyFailedUsers' => $notifyResult['notifyFailedUsers']
            );
        $this->logger->debug(__METHOD__ . " END   " . PHP_EOL);
        return $rtn;
    }

    /**
    * 给超过180天未获得过积分的用户发邮件
    * 这些用户的积分清零
    * @param \DateTime $baseDate
    * @return array(
    *              'status' => succeeded / failed
    *              'errmsg' => 错误信息 status == failed 时候出现
    *              'targetUserCount'   => int,      // 积分被清零的用户总数
    *              'notifyFailedUsers' => array(    // 通知失败的用户信息
    *                                           'id'     => int,
    *                                           'email'  => string,
    *                                           'nick'   => string,
    *                                           'points' => int,
    *                                           'errmsg' => 该用户失败的原因
    *                                          ),
    *              'totalExpiredPoints' => int,     // 成功清零的积分总数
    *              'expireFailedUsers' => array(    // 清零失败的用户信息
    *                                           'id'     => int,
    *                                           'email'  => string,
    *                                           'nick'   => string,
    *                                           'points' => int,
    *                                           'errmsg' => 该用户失败的原因
    *                                          )
    *              )
    */
    public function notifyAndExpireUserExpired(\DateTime $baseDate){
        $this->logger->debug(__METHOD__ . " START " . PHP_EOL);
        // Prepare from and to date.
        // from = now - 10 years
        // to = now - 180 days
        $now = $baseDate->setTime(0,0,0);
        $to = (clone $now);
        $to->sub(new \DateInterval('P180D'));
        $from = (clone $now);
        $from->sub(new \DateInterval('P10Y'));

        // select expiring users from user table
        $findResult = $this->findExpiringUsers($from, $to);
        if('suceeded' != $findResult['status']){
            $rtn = array(
            'status' => 'failed',
            'errmsg' => $findResult['msg'],
            'targetUserCount' => '',
            'notifyFailedUsers' => array(),
            'totalExpiredPoints' => '',
            'expireFailedUsers' => array()
            );
            return $rtn;
        }

        // notify users
        $notifyResult = $this->notifyExpiringUsers($findResult['expiringUsers'], self::TITLE_FORMAT_EXPIRED, self::TEMPLATE_EXPIRED);

        // expire points of users
        $expireResult = $this->expireExpiringUsers($findResult['expiringUsers']);

        $rtn = array(
            'status' => 'succeeded',
            'errmsg' => '',
            'targetUserCount' => sizeof($findResult['expiringUsers']),
            'notifyFailedUsers' => $notifyResult['notifyFailedUsers'],
            'totalExpiredPoints' => $expireResult['totalExpiredPoints'],
            'expireFailedUsers' => $expireResult['expireFailedUsers'],
            'expireSucceededUsers' => $expireResult['expireSucceededUsers']
            );
        $this->logger->debug(__METHOD__ . " END   " . PHP_EOL);
        return $rtn;
    }

    /**
    * 发结果通知邮件给系统部门
    * 额，这个地方偷点懒，发给开发部门的邮件
    */
    public function systemResultNotify($subject, $params){
        if(false == $this->skipEmailFlag){
            try{
                $html = $this->templating->render(self::TEMPLATE_SYSTEM_EXPIRE_POINT_NOTIFICATION, $params);
                $this->logger->debug(__METHOD__ . $html);
                $result = $this->mailerForSys->send(self::SENDTO_SYSTEM_NOTIFY, $subject, $html);
                if(true != $result['result']){
                    // 注意，这个分支没有测到
                    $errmsg = "Failed to send email.";
                    $this->logger->ERROR(__METHOD__ . " Errmsg: " . $errmsg . PHP_EOL);
                    return false;
                }
            } catch(\Exception $e) {
                $errmsg = "Failed to send email. " . $e->getMessage();
                $this->logger->ERROR(__METHOD__ . " Errmsg: " . $errmsg . PHP_EOL);
                return false;
            }
        }
        return true;
    }


// 以下是 private function 为了测试方便，定义成public了 -.-


    /**
    * 检索指定时间范围内的即将被积分清零的用户
    * 注意：这其实是一个private function 为了测试方便，定义成public了 -.-
    * @param \DateTime $from
    * @param \DateTime $to
    * @return array $result
    *           (
    *               'status' => suceeded / failed, 
    *               'expiringUsers' => 检索到的user数组 ('id','email', 'nick', 'points')
    *               'msg' => 错误信息
    *           )
    */
    public function findExpiringUsers(\DateTime $from, \DateTime $to){
        $this->logger->info(__METHOD__ . " from->" . $from->format('Y-m-d H:i:s') . " to->" . $to->format('Y-m-d H:i:s') . PHP_EOL);
        $result = array();
        // select expiring users from user table
        try{
            $expiringUsers = $this->em->getRepository('WenwenFrontendBundle:User')->findExpiringUsers($from, $to);
            $result['status'] = 'suceeded';
            $result['expiringUsers'] = $expiringUsers;
            $result['msg'] = sprintf("Find %d expiring users from %s to %s.", 
                                sizeof($expiringUsers),
                                $from->format('Y-m-d H:i:s'), 
                                $to->format('Y-m-d H:i:s')
                                );
            $this->logger->info(__METHOD__ . " " . $result['msg'] . PHP_EOL);
            return $result;
        } catch(\Exception $e){
            $result['status'] = 'failed';
            $result['msg'] = sprintf("Failed to find expiring users from %s to %s. Errmsg: %s", 
                                $from->format('Y-m-d H:i:s'), 
                                $to->format('Y-m-d H:i:s'), 
                                $e->getMessage()
                                );

            $this->logger->ERROR(__METHOD__ . " " . $result['msg'] . PHP_EOL);
            return $result;
        }
    }

    /**
    * 对对象用户进行邮件通知
    * 注意：这其实是一个private function 为了测试方便，定义成public了 -.-
    * @param array $expiringUsers 数组 ('id','email', 'nick', 'points')
    * @param $titleFormat
    * @param $template
    * @return array $result
    *           (
    *               'notifyFailedUsers' => 邮件通知失败的user数组 ('id','email', 'nick', 'points', 'errmsg')
    *           )
    */
    public function notifyExpiringUsers(array $expiringUsers, $titleFormat, $template){
        $this->logger->debug(__METHOD__ . " START Notify target count=" . sizeof($expiringUsers) . PHP_EOL);
        $notifyFailedUsers = array();
        foreach($expiringUsers as $expiringUser){
            $subject = sprintf($titleFormat, $expiringUser['nick'], $expiringUser['points']);
            $html = $this->templating->render($template, $expiringUser);
            if(false == $this->skipEmailFlag){
                try{
                    // 同步发邮件，等待每次发邮件的结果
                    $result = $this->mailerForUser->send($expiringUser['email'], $subject, $html);
                    if(true != $result['result']){
                        // 注意，这个分支没有测到
                        $errmsg = "Failed to send email.";
                        $expiringUser['errmsg'] = $errmsg;
                        $notifyFailedUsers[] = $expiringUser;
                        $this->logger->ERROR(__METHOD__ . " Email=" . $expiringUser['email'] . " Errmsg: " . $errmsg . PHP_EOL);
                    }
                } catch(\Exception $e) {
                    $errmsg = "Failed to send email. " . $e->getMessage();
                    $expiringUser['errmsg'] = $errmsg;
                    $notifyFailedUsers[] = $expiringUser;
                    $this->logger->ERROR(__METHOD__ . " Email=" . $expiringUser['email'] . " Errmsg: " . $errmsg . PHP_EOL);
                }
            }
        }
        $result = array(
            'notifyFailedUsers' => $notifyFailedUsers);
        $this->logger->info(__METHOD__ . " END Failed to notify count=" . sizeof($notifyFailedUsers) . PHP_EOL);
        return $result;
    }

    /**
    * 对对象用户进行积分清零
    * 注意：这其实是一个private function 为了测试方便，定义成public了 -.-
    * @param array $expiringUsers 数组 ('id','email', 'nick', 'points')
    * @return array $result
    *           (
    *               'totalExpiredPoints' => 成功清除的总分
    *               'expireFailedUsers' => 邮件通知失败的user数组 ('id','email', 'nick', 'points', 'errmsg')
    *               'expireSucceededUsers' => 成功清楚积分的user数组 ('id','email', 'nick', 'points')
    *           )
    */
    public function expireExpiringUsers(array $expiringUsers){
        $this->logger->debug(__METHOD__ . " START " . PHP_EOL);
        $totalExpiredPoints = 0;
        $expireFailedUsers = array();
        $expireSucceededUsers = array();
        $expireTime = date_create();
        foreach($expiringUsers as $expiringUser){
            // 积分清零
            $userId = $expiringUser['id'];
            
            try{
                $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
                if($user){
                    try{
                        if(false == $this->skipExpiringFlag){

                            // 用户现有分数的负数
                            $points = -$user->getPoints();

                            $this->pointService->addPoints(
                                $user,
                                $points,
                                CategoryType::EXPIRE,
                                TaskType::RECOVER,
                                self::TASK_NAME);
                        }
                        $totalExpiredPoints += $expiringUser['points'];
                        $expireSucceededUsers[] = $expiringUser;
                    } catch(\Exception $e){
                        $errmsg = "Failed to expire user.points. " . $e->getMessage();
                        $expiringUser['errmsg'] = $errmsg;
                        $expireFailedUsers[] = $expiringUser;
                        $this->logger->ERROR(__METHOD__ . " Email=" . $expiringUser['email'] . " Errmsg: " . $errmsg . PHP_EOL);
                    }

                    $this->logger->debug(__METHOD__ . " Email=" . $expiringUser['email'] . " Point expired succeed. " . PHP_EOL);
                } else {
                    $errmsg = "User not found.";
                    $expiringUser['errmsg'] = $errmsg;
                    $expireFailedUsers[] = $expiringUser;
                    $this->logger->ERROR(__METHOD__ . " Email=" . $expiringUser['email'] . " Errmsg: " . $errmsg . PHP_EOL);
                }
            } catch(\Exception $e){
                $errmsg = "Failed to find user " . $e->getMessage();
                $expiringUser['errmsg'] = $errmsg;
                $expireFailedUsers[] = $expiringUser;
                $this->logger->ERROR(__METHOD__ . " Email=" . $expiringUser['email'] . " Errmsg: " . $errmsg . PHP_EOL);
            }
        }
        $result = array(
            'totalExpiredPoints' => $totalExpiredPoints,
            'expireFailedUsers' => $expireFailedUsers,
            'expireSucceededUsers' => $expireSucceededUsers
            );
        $this->logger->info(__METHOD__ . " END TotalExpiredPoints=" . $totalExpiredPoints . " Failed count=" . sizeof($expireFailedUsers) . PHP_EOL);
        return $result;
    }

}