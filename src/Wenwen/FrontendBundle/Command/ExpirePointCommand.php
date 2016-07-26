<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;

class ExpirePointCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('point:expire');
        $this->setDescription('Point expiration and notification');
        $this->addOption('baseDate', null, InputOption::VALUE_REQUIRED, "Please set the date that this command suppose to run on.");
        $this->addOption('realMode', null, InputOption::VALUE_NONE, "Will not notify users and expire point without this option on.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $optionBaseDate = $input->getOption('baseDate');
        $realMode = $input->getOption('realMode');

        if (!$this->isValidBaseDate($optionBaseDate)){
            // 参数检查不合格时，显示提示信息
            $output->writeln("Usage: app/console point:expire --baseDate YYYY-MM-DD/now --realMode");
            $output->writeln("Current date time will be used if --baseDate=now is setted.");
            $output->writeln("Will noly notify users with --realMode.");
        } else {
            // 参数检查合格后，开始正式执行

            // 准备baseDate参数
            $baseDate = $this->getBaseDate($optionBaseDate);
            $output->writeln(date_create()->format('Y-m-d H:i:s') . " ExpirePointCommand START baseDate=" . $baseDate->format('Y-m-d'));
            
            // 开始执行积分清零的逻辑
            $expirePointsService = $this->getContainer()->get('app.expire_point_service');
            if($realMode){
                // 指定 --realMode 的时候，才真正发邮件并做积分清零
                $expirePointsService->sendEmail();
                $expirePointsService->doExpiringPoint();
            }
            // 对于已持续150天未获得积分的用户，邮件通知用户
            $result30Days  = $expirePointsService->notifyUserExpiringIn30Days($baseDate);

            // 对于已持续173天未获得积分的用户，邮件通知用户
            $result7Days   = $expirePointsService->notifyUserExpiringIn7Days($baseDate);

            // 对于已持续180天以上未获得积分的用户，邮件通知用户 并且 积分清零
            $resultExpired = $expirePointsService->notifyAndExpireUserExpired($baseDate);

            $output->writeln('');
            $output->writeln('[result30Days]');
            $output->writeln('status:' . $result30Days['status']);
            $output->writeln('errmsg:' . $result30Days['errmsg']);
            $output->writeln('targetUserCount:' . $result30Days['targetUserCount']);
            $output->writeln('notifyFailedUsers:');
            foreach($result30Days['notifyFailedUsers'] as $notifyFailedUser){
                $output->writeln(implode($notifyFailedUser, ","));
            }
            $output->writeln('');

            $output->writeln('[result7Days]');
            $output->writeln('status:' . $result7Days['status']);
            $output->writeln('errmsg:' . $result7Days['errmsg']);
            $output->writeln('targetUserCount:' . $result7Days['targetUserCount']);
            $output->writeln('notifyFailedUsers:');
            foreach($result7Days['notifyFailedUsers'] as $notifyFailedUser){
                $output->writeln(implode($notifyFailedUser, ","));
            }
            $output->writeln('');

            $output->writeln('[resultExpired]');
            $output->writeln('status:' . $resultExpired['status']);
            $output->writeln('errmsg:' . $resultExpired['errmsg']);
            $output->writeln('targetUserCount:' . $resultExpired['targetUserCount']);
            $output->writeln('notifyFailedUsers:');
            foreach($resultExpired['notifyFailedUsers'] as $notifyFailedUser){
                $output->writeln(implode($notifyFailedUser, ","));
            }
            $output->writeln('totalExpiredPoints:' . $resultExpired['totalExpiredPoints']);
            $output->writeln('expireFailedUsers:');
            foreach($resultExpired['expireFailedUsers'] as $expireFailedUsers){
                $output->writeln(implode($expireFailedUsers, ","));
            }
            $output->writeln('');

            // 将执行结果发邮件通知系统部门
            $subject = "[OK] ExpirePointCommand finished";
            if('succeeded' != $result30Days['status'] || 'succeeded' != $result7Days['status'] || 'succeeded' != $resultExpired['status']){
                $subject = "[NG] ExpirePointCommand has something wrong, please check the result!";
            }
            $params = array();
            $params['result30Days'] = $result30Days;
            $params['result7Days'] = $result7Days;
            $params['resultExpired'] = $resultExpired;
            $resultSysNotify = $expirePointsService->systemResultNotify($subject, $params);

            if(true == $resultSysNotify){
                $output->writeln(date_create()->format('Y-m-d H:i:s') . " ExpirePointCommand SUCEEDED baseDate=" . $baseDate->format('Y-m-d'));
            } else {
                $output->writeln(date_create()->format('Y-m-d H:i:s') . " ExpirePointCommand FAILED baseDate=" . $baseDate->format('Y-m-d'));
            }
        }
    }

// 以下都是private function 为了测试方便都定义成了public

    /**
    * 检查参数
    * @param $optionBaseDate
    * @return boolean
    */
    public function isValidBaseDate($optionBaseDate){
        if($optionBaseDate){
            // $baseDate存在
            if('now' == $optionBaseDate){
                // $baseDate = now
                return true;
            } else {
                // $baseDate = YYYY-MM-DD
                return $this->validateDate($optionBaseDate, 'Y-m-d');
            }
        } else {
            return false;
        }
    }

    /**
    * This function is copied from php.net http://php.net/manual/en/function.checkdate.php
    * @param $date
    * @param $format
    * @return boolean
    */
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
    * @param string $optionBaseDate
    * @param $format
    * @return DateTime
    */
    public function getBaseDate($optionBaseDate, $format = 'Y-m-d'){
        if('now' == $optionBaseDate){
            $d = date_create();
            $d->setTime(0,0,0);
            return $d;
        } else {
            $d = \DateTime::createFromFormat($format, $optionBaseDate);
            return $d;
        }
    }
}
