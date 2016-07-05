<?php

namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Jili\ApiBundle\Entity\SendPointFail;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\SendCloudMailer;

class DmdeliveryCommand extends ContainerAwareCommand
{
    const alertTo = 'rpa-sys-china@d8aspring.com';
    const alertSubject = '91问问-定时任务提醒邮件';

    protected function configure()
    {
        $this->setName('jili:run_crontab_Dmdelivery')
            ->setDescription('run_crontab_Dmdelivery')
            ->addArgument('batch_name', InputArgument :: REQUIRED, 'batch_name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start at '.date('Y-m-d H:i:s',time()));
        $batch_name = $input->getArgument('batch_name');
        $output->writeln('batch name : ' . $batch_name);
        $em = $this->getContainer()->get('doctrine')->getManager();
        $mem_limit = ini_get("memory_limit");
        ini_set("memory_limit","256M");
        $this->$batch_name($em);
        $output->writeln('finish at '.date('Y-m-d H:i:s',time()));
        ini_set("memory_limit" , $mem_limit);
    }
    
    public function pointFailure($em)
    {
        set_time_limit(0);
        $failTime = 180;
        $templatePath = 'WenwenFrontendBundle:EmailTemplate:point_failure.html.twig';
        $subject = '[系统通知] 注意！您的积分已经失效清零！';
        $this->handleSendPointFail($em, $failTime, $templatePath, $subject, 'pointFailure');
    }

    public function pointFailureForWeek($em)
    {
        set_time_limit(0);
        $failTime = 173;
        $templatePath = 'WenwenFrontendBundle:EmailTemplate:point_failure_for_week.html.twig';
        $subject = '[系统通知] 注意！您的积分即将在一周后失效清零！';
        $this->handleSendPointFail($em, $failTime, $templatePath, $subject, 'pointFailureForWeek');
    }
    
    public function pointFailureForMonth($em)
    {
        set_time_limit(0);
        $failTime = 150;
        $templatePath = 'WenwenFrontendBundle:EmailTemplate:point_failure_for_month.html.twig';
        $subject = '[系统通知] 注意！您的积分即将在一个月后失效清零！';
        $this->handleSendPointFail($em, $failTime, $templatePath, $subject, 'pointFailureForMonth');
    }

    public function handleSendPointFail($em, $failTime, $templatePath, $subject, $pointType)
    {
        $user = $em->getRepository('JiliApiBundle:User')->pointFail($failTime);
        echo "select count : ".count($user)."\n";
        echo 'memory after user:'.memory_get_usage()."\n";
        if(!empty($user)){
            $send_fail_email_count = 0;
            $templating = $this->getContainer()->get('templating');
            $mailer = $this->createMailer();
            foreach ($user as $key => $value) {
                $templateVars = array('nick' => $value['nick']);
                $html = $templating->render($templatePath, $templateVars);
                $result = $mailer->send($value['email'], $subject, $html);
                $sendflag = $result['result'];
                if ($sendflag){
                    try {
                        $em->getConnection()->beginTransaction();
                        $this->insertSendPointFail($em, $value['id'],$failTime);
                        if($failTime == 180){
                            $this->updatePointZero($em, $value['id']);
                        }
                        if($pointType==='pointFailure'){
                            echo 'key :'.$key. ',userid:'.$value['id']."-> update successfully  \n";
                        } else {
                            echo 'key :'.$key. ',userid:'.$value['id']."-> send successfully  \n";
                        }
                        $em->getConnection()->commit();
                    } catch (\Exception $ex) {
                        $em->getConnection()->rollback();
                        $content = $this->setALertEmailBody($pointType,'something error happend when insert or update)');
                        $this->getContainer()->get('send_mail')->sendMails(self::alertSubject, self::alertTo, $content);
                        throw $ex;
                    }
                } else {
                    $send_fail_email_count++;
                    echo 'key :'.$key. ',userid:'.$value['id'].'-> Cannot send email:'.$result['message']." \n";
                }
                echo 'memory after one record:'.memory_get_usage()."\n";
            }
            if ($send_fail_email_count > 0){
                $content = $this->setALertEmailBody($pointType,'Cannot send email，count = '.$send_fail_email_count);
                echo 'Cannot send email，count = '.$send_fail_email_count."\n";
                $this->getContainer()->get('send_mail')->sendMails(self::alertSubject, self::alertTo, $content);
            } else {
                $content = $this->setALertEmailBody($pointType,'Send finish!!!',true);
                echo ' Finished and Successed !!!'."\n";
                $this->getContainer()->get('send_mail')->sendMails(self::alertSubject, self::alertTo,$content);
            }
        }else{
            $content = $this->setALertEmailBody($pointType,'Email list is empty');
            echo 'Email list is empty'."\n";
            $this->getContainer()->get('send_mail')->sendMails(self::alertSubject, self::alertTo,$content);
        }
    }

    private function updatePointZero($em, $userId)
    {
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);
        $oldPoint = $user->getPoints();
        $user->setPoints($this->getContainer()->getParameter('init'));
        // Create new object of point_history0x
        $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ( $userId % 10);
        $pointHistory = new $classPointHistory();
        $pointHistory->setUserId($userId);
        $pointHistory->setPointChangeNum(-$oldPoint);
        $pointHistory->setReason($this->getContainer()->getParameter('init_fifteen'));
        $em->persist($user);
        $em->persist($pointHistory);
        $em->flush();
        $em->clear();
    }

    private function insertSendPointFail($em, $userId,$type)
    {
        $sendPoint = new SendPointFail();
        $sendPoint->setUserId($userId);
        $sendPoint->setSendType($type);
        $em->persist($sendPoint);
        $em->flush();
        $em->clear();
    }
    
    private function setALertEmailBody($batch_name, $message, $successflag = false){
        if($successflag) {
            $msg_str = "执行成功";
        } else {
            $msg_str = "出现错误";
        }
        $content =      '<html>' .
                        ' <head></head>' .
                        ' <body>' .
                        '积粒网管理员：'.'<br/>'.
                        '<br/>'.
                        '  您的定时任务'.$batch_name.$msg_str.',请确认。<br/>'.
                        '  信息为：'.$message. '<br/>' .
                        '  ++++++++++++++++++++++++++++++++++<br/>' .
                        '  积粒网，轻松积米粒，快乐换奖励！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
                        ' </body>' .
                        '</html>';
        return $content;
    }

    private function createMailer()
    {
        // sendcloud
//        $parameterService = $this->getContainer()->get('app.parameter_service');
//        $httpClient = $this->getContainer()->get('app.http_client');
//        return MailerFactory::createSendCloudMailer($parameterService, $httpClient, 'channel1');

        // webpower
        $parameterService = $this->getContainer()->get('app.parameter_service');
        return MailerFactory::createWebpowerMailer($parameterService);
    }
}
