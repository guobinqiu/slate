<?php
namespace Jili\ApiBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Jili\ApiBundle\Entity\SendPointFail;
use Jili\ApiBundle\Entity\PointHistory00;
use Jili\ApiBundle\Entity\PointHistory01;
use Jili\ApiBundle\Entity\PointHistory02;
use Jili\ApiBundle\Entity\PointHistory03;
use Jili\ApiBundle\Entity\PointHistory04;
use Jili\ApiBundle\Entity\PointHistory05;
use Jili\ApiBundle\Entity\PointHistory06;
use Jili\ApiBundle\Entity\PointHistory07;
use Jili\ApiBundle\Entity\PointHistory08;
use Jili\ApiBundle\Entity\PointHistory09;

class DmdeliveryCommand extends ContainerAwareCommand
{
    private $soap = 'http://91jili.dmdelivery.com/x/soap-v4/wsdl.php';
    private $username;
    private $password;
    private $alertTo;
    private $alertSubject;
    
    protected function configure()
    {
        $this->setName('jili:run_crontab_Dmdelivery')
            ->setDescription('run_crontab_Dmdelivery')
            ->addArgument('batch_name', InputArgument :: REQUIRED, 'batch_name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->username = $this->getContainer()->getParameter('webpower_email_username');
        $this->password = $this->getContainer()->getParameter('webpower_email_password');
        $this->alertTo = explode(",", $this->getContainer()->getParameter('cron_alertTo_contacts'));
        $this->alertSubject = $this->getContainer()->getParameter('alert_subject');
        $output->writeln('start at '.date('Y-m-d H:i:s',time()));
        $batch_name = $input->getArgument('batch_name');
        $output->writeln('batch name : ' . $batch_name);
        $em = $this->getContainer()->get('doctrine')->getManager();
        ini_set("memory_limit","100M");
        $this->$batch_name($em);
        $output->writeln('finish at '.date('Y-m-d H:i:s',time()));
        $mem_limit = init_get("memory_limit");
        init_set("memory_limit" , $mem_limit);
        $output->writeln("");
        $output->writeln("");
    }
    
    public function pointFailureTemp($em)
    {
        set_time_limit(0);
        $failTime = 180;
        $companyId = 4;
        $mailingId = 28;
        $this->handleSendPointFailTemp($em, $failTime,$companyId,$mailingId);
    }
    
    public function pointFailure($em)
    {
        set_time_limit(0);
        $failTime = 180;
        $companyId = 4;
        $mailingId = 28;
        $this->handleSendPointFail($em, $failTime,$companyId,$mailingId);
    }
    public function pointFailureForWeek($em)
    {
        set_time_limit(0);
        $failTime = 173;
        $companyId = 4;
        $mailingId = 31;
        $this->handleSendPointFail($em,$failTime,$companyId,$mailingId);
    }
    
    public function pointFailureForMonth($em)
    {
        set_time_limit(0);
        $failTime = 150;
        $companyId = 4;
        $mailingId = 30;
        $this->handleSendPointFail($em,$failTime,$companyId,$mailingId);
    }
    
    public function handleSendPointFail($em, $failTime,$companyId,$mailingId)
    {
        $user = $em->getRepository('JiliApiBundle:User')->pointFail($failTime);
        echo "select count : ".count($user)."\n";
        if(!empty($user)){
            $group = $this->addgroup($companyId);
            if($group->status != "ERROR"){
                $send_fail_email_count = 0;
                foreach ($user as $key => $value) {
                    $failId = $this->issetFailRecord($em, $value['id'],$failTime);
                    if(!$failId){
                        $recipient_arr = array(
                                            array(
                                                'fields'=>
                                                    array(
                                                        array('name'=>'email','value'=>$value['email']),
                                                        array('name'=>'nick','value'=>$value['nick'])
                                                         )
                                                 )
                                        );
                        $send = $this->addRecipientsSendMailing($companyId,$mailingId,$group->id,$recipient_arr);
                        //$this->get('logger')->info('{DmdeliveryController}'. "email:".var_export($value['email'], true) .",status:". var_export($send->status, true).'key:'.$key);
                        if($send->status != "ERROR"){
                            try{
                                $em->getConnection()->beginTransaction();
                                $this->insertSendPointFail($em, $value['id'],$failTime);
                                if($failTime == 180){
                                    $this->updatePointZero($em, $value['id']);
                                }
                                echo 'key :'.$key. ',userid:'.$value['id']."-> Send email successfully  \n";
                                $em->getConnection()->commit();
                            } catch (Exception $ex) {
                                $em->getConnection()->rollback();
                                $content = $this->setALertEmailBody('pointFailure','something error happend when insert or update)');
                                $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo, $content);
                                throw $e;
                            }
                        } else {
                            $send_fail_email_count++;
                            echo 'key :'.$key. ',userid:'.$value['id'].'-> Cannot send email:'.$send->statusMsg." \n";
                        }
                    }
                }
                if ($send_fail_email_count > 0){
                    $content = $this->setALertEmailBody('pointFailure','Cannot send email，count = '.$send_fail_email_count);
                    echo 'Cannot send email，count = '.$send_fail_email_count."\n";
                    $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo, $content);
                } else {
                    $content = $this->setALertEmailBody('pointFailure','Send finish!!!',true);
                    echo 'Send finish!!!'."\n";
                    $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo,$content);
                }
            }else{
                $content = $this->setALertEmailBody('pointFailure','Cannot add group:'.$group->statusMsg);
                echo 'Cannot add group:'.$group->statusMsg."\n";
                $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo, $content);
            }
        }else{
            $content = $this->setALertEmailBody('pointFailure','Email list is empty');
            echo 'Email list is empty'."\n";
            $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo,$content);
        }
    }
    
    
    public function handleSendPointFailTemp($em, $failTime,$companyId,$mailingId)
    {
        $user = $em->getRepository('JiliApiBundle:User')->pointFailTemp($failTime);
        echo "select count : ".count($user)."\n";
        if(!empty($user)){
            $group = $this->addgroup($companyId);
            if($group->status != "ERROR"){
                $send_fail_email_count = 0;
                foreach ($user as $key => $value) {
                    $failId = $this->issetFailRecord($em, $value['id'],$failTime);
                    if(!$failId){
                        $recipient_arr = array(
                                            array(
                                                'fields'=>
                                                    array(
                                                        array('name'=>'email','value'=>$value['email']),
                                                        array('name'=>'nick','value'=>$value['nick'])
                                                         )
                                                 )
                                        );
                        $send = $this->addRecipientsSendMailing($companyId,$mailingId,$group->id,$recipient_arr);
                        //$this->get('logger')->info('{DmdeliveryController}'. "email:".var_export($value['email'], true) .",status:". var_export($send->status, true).'key:'.$key);
                        if($send->status != "ERROR"){
                            try{
                                $em->getConnection()->beginTransaction();
                                $this->insertSendPointFail($em, $value['id'],$failTime);
                                if($failTime == 180){
                                    $this->updatePointZero($em, $value['id']);
                                }
                                echo 'key :'.$key. ',userid:'.$value['id']."-> Send email successfully  \n";
                                $em->getConnection()->commit();
                            } catch (Exception $ex) {
                                $em->getConnection()->rollback();
                                $content = $this->setALertEmailBody('pointFailure','something error happend when insert or update)');
                                $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo, $content);
                                throw $e;
                            }
                        } else {
                            $send_fail_email_count++;
                            echo 'key :'.$key. ',userid:'.$value['id'].'-> Cannot send email:'.$send->statusMsg." \n";
                        }
                    }
                }
                if ($send_fail_email_count > 0){
                    $content = $this->setALertEmailBody('pointFailure','Cannot send email，count = '.$send_fail_email_count);
                    $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo, $content);
                } else {
                    $content = $this->setALertEmailBody('pointFailure','Send finish!!!',true);
                    $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo,$content);
                }
            }else{
                $content = $this->setALertEmailBody('pointFailure','Cannot add group:'.$group->statusMsg);
                $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo, $content);
            }
        }else{
            $content = $this->setALertEmailBody('pointFailure','Email list is empty');
            echo 'Email list is empty'."\n";
            $this->getContainer()->get('send_mail')->sendMails($this->alertSubject, $this->alertTo,$content);
        }
    }
    
    public function addRecipientsSendMailing($companyId,$mailingId,$groupId,$recipient_arr)
    {
        $login = array('username' => $this->username, 'password' => $this->password);
        $client = $this->init_client();
        try {
            $result = $client->addRecipientsSendMailing(
                $login,
                $companyId,
                $mailingId,
                array($groupId),
                $recipient_arr,
                true,
                true
                   );
            return $result;
        } catch (SoapFault $exception) {
            echo $exception;
        }

    }
    
    public function addgroup($companyId)
    {
        $login = array('username' => $this->username, 'password' => $this->password );
        $client = $this->init_client();
        try {
            $result = $client->addGroup(
                $login,
                $companyId,
                array('name'=>'test',
                    'is_test'=>'true',
                )
            );
            return $result;
        }catch (SoapFault $exception) {
            echo $exception;
        }
    }
    
    public function init_client()
    {
        ini_set('soap.wsdl_cache_enabled','0');
        $client = new \SoapClient($this->soap,array('encoding'=>'utf-8', 'features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
        return $client;
    }
    
    public function issetFailRecord($em, $user_id,$failTime)
    {
        switch ($failTime) {
        case 150:
              $failRecord = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,180);
              if(empty($failRecord)){
                    $failRecordWeek = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,173);
                    if(empty($failRecordWeek)){
                        $failRecordMonth = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,150);
                        if(empty($failRecordMonth)){
                            return '';
                        }else{
                            return $failRecordMonth[0]['userId'];
                        }
                    }else
                        return $failRecordWeek[0]['userId'];
              }else
                return $failRecord[0]['userId'];
          break;
        case 173:
            $failRecord = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,180);
            if(empty($failRecord)){
                $failRecordWeek = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,173);
                if(empty($failRecordWeek)){
                    return '';
                }else
                    return $failRecordWeek[0]['userId'];
            }else
                return $failRecord[0]['userId'];
          break;
        case 180:
            $failRecord = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,180);
            if(empty($failRecord))
                return '';
            else
                return $failRecord[0]['userId'];
          break;
        default:
             return '';
        }

    }
    
    public function updatePointZero($em, $userId)
    {
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);
        $oldPoint = $user->getPoints();
        $user->setPoints($this->getContainer()->getParameter('init'));
        $em->persist($user);
        $em->flush();
        $params = array('userid'=>$userId,'point'=>'-'.$oldPoint,'type'=>$this->getContainer()->getParameter('init_fifteen'));
        $this->getContainer()->get('general_api.point_history')->get($params);
    }

    public function insertSendPointFail($em, $userId,$type)
    {
        $sendPoint = new SendPointFail();
        $sendPoint->setUserId($userId);
        $sendPoint->setSendType($type);
        $em->persist($sendPoint);
        $em->flush();
    }
    
    public function setALertEmailBody($batch_name, $message, $successflag = false){
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
}
