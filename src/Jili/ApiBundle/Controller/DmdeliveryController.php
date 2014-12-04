<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\User;
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

/**
 * @Route( requirements={"_scheme" = "http"})
 */
class DmdeliveryController extends Controller
{
    private $soap = 'http://91jili.dmdelivery.com/x/soap-v4/wsdl.php';
    private $username = 'admin';
    private $password = 'nUshkwu#9';//'Nvpiyjh1-';
    /**
	 * @Route("/pointFailure", name="_dmdelivery_pointFailure")
	 */
    public function pointFailureAction()
    {

        set_time_limit(0);
        $failTime = 180;
        $companyId = 4;
        $mailingId = 28;
        $rs = $this->handleSendPointFail($failTime,$companyId,$mailingId);
        return new Response($rs);

    }

     /**
	 * @Route("/pointFailureTemp", name="_dmdelivery_pointFailureTemp")
	 */
    public function pointFailureTempAction()
    {

        set_time_limit(0);
        $failTime = 180;
        $companyId = 4;
        $mailingId = 28;
        $rs = $this->handleSendPointFailTemp($failTime,$companyId,$mailingId);
        return new Response($rs);

    }
    
    /**
	 * @Route("/pointFailureForWeek", name="_dmdelivery_pointFailureForWeek")
	 */
    public function pointFailureForWeekAction()
    {
        set_time_limit(0);
        $failTime = 173;
        $companyId = 4;
        $mailingId = 31;
        $rs = $this->handleSendPointFail($failTime,$companyId,$mailingId);
        return new Response($rs);

    }

    /**
	 * @Route("/pointFailureForMonth", name="_dmdelivery_pointFailureForMonth")
	 */
    public function pointFailureForMonthAction()
    {
        set_time_limit(0);
        $failTime = 150;
        $companyId = 4;
        $mailingId = 30;
        $rs = $this->handleSendPointFail($failTime,$companyId,$mailingId);
        return new Response($rs);

    }

    public function issetFailRecord($user_id,$failTime)
    {
        $em = $this->getDoctrine()->getManager();
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

    public function handleSendPointFail($failTime,$companyId,$mailingId)
    {
        $rs = "";
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->pointFail($failTime);
        //$logger = $this->get('logger');
        if(!empty($user)){
            $group = $this->addgroup($companyId);
            if($group->status != "ERROR"){
                foreach ($user as $key => $value) {
                    $failId = $this->issetFailRecord($value['id'],$failTime);
                    if(!$failId){
                        $recipient_arr = array();
                        $recipient_arr[] = array(array('name'=>'email','value'=>$value['email']),
                                                 array('name'=>'nick','value'=>$value['nick']));
                        $send = $this->addRecipientsSendMailing($companyId,$mailingId,$group->id,$recipient_arr);
                        //$this->get('logger')->info('{DmdeliveryController}'. "email:".$value['email'].",stauts:".$send->status);
                        if($send->status != "ERROR"){
                            $this->insertSendPointFail($value['id'],$failTime);
                            if($failTime == 180){
                                $this->updatePointZero($value['id']);
                            }
                            $rs = 'Send email successfully';
                        }else{
                            $rs = 'Cannot send email:'.$send->statusMsg;
                        }
                    }
                }
            }else{
                $rs = 'Cannot add group:'.$group->statusMsg;
            }
        }else{
            $rs = 'Email list is empty';
        }
        return $rs;
    }

    public function handleSendPointFailTemp($failTime,$companyId,$mailingId)
    {
        $rs = "";
        $em = $this->getDoctrine()->getManager();
        //$user = $em->getRepository('JiliApiBundle:User')->pointFail($failTime);
        $user = $em->getRepository('JiliApiBundle:User')->pointFailTemp($failTime);
        //$logger = $this->get('logger');
        if(!empty($user)){
            $group = $this->addgroup($companyId);
            if($group->status != "ERROR"){
                foreach ($user as $key => $value) {
                    $failId = $this->issetFailRecord($value['id'],$failTime);
                    if(!$failId && isset($value['email']) && strlen($value['email']) > 0){
                        $recipient_arr = array();
                        $recipient_arr[] = array(array('name'=>'email','value'=>$value['email']),
                                                 array('name'=>'nick','value'=>$value['nick']));
                        $send = $this->addRecipientsSendMailing($companyId,$mailingId,$group->id,$recipient_arr);
                        $this->get('logger')->info('{DmdeliveryController}'. "email:".$value['email'].",status:".$send->status.'key:'.$key);
                        if($send->status != "ERROR"){
                            $this->insertSendPointFail($value['id'],$failTime);
                            if($failTime == 180){
                                $this->updatePointZero($value['id']);
                            }
                            $rs = 'Send email successfully';
                            echo 'key :'.$key. ',userid:'.$value['id'].'-> Send email successfully  \n';
                        }else{
                            $rs = 'Cannot send email:'.$send->statusMsg;
                            echo 'key :'.$key. ',userid:'.$value['id'].'-> Cannot send email:'.$send->statusMsg.' \n';
                        }
                    }
                }
            }else{
                $rs = 'Cannot add group:'.$group->statusMsg;
            }
        }else{
            $rs = 'Email list is empty';
        }
        return $rs;
    }
    
    public function updatePointZero($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);
        $oldPoint = $user->getPoints();
        $user->setPoints($this->container->getParameter('init'));
        $em->persist($user);
        $em->flush();
        $this->getPoint($userId,'-'.$oldPoint,$this->container->getParameter('init_fifteen'));
    }

    public function insertSendPointFail($userId,$type)
    {
        $em = $this->getDoctrine()->getManager();
        $sendPoint = new SendPointFail();
        $sendPoint->setUserId($userId);
        $sendPoint->setSendType($type);
        $em->persist($sendPoint);
        $em->flush();

    }

    public function init_client()
    {
        ini_set('soap.wsdl_cache_enabled','0');
        $client = new \SoapClient($this->soap,array('encoding'=>'utf-8', 'features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
        return $client;
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

    public function sendmailing($companyId,$mailingId,$groupId)
    {
        $login = array('username' => $this->username, 'password' => $this->password);
        $client = $this->init_client();
        try {
            $result = $client->sendMailing(
                $login,
                $companyId,
                $mailingId,
                true,
                "yang@voyagegroup.com.cn",
                array($groupId),
                "",
                "",
                "",
                ""
                   );
            return $result;
        } catch (SoapFault $exception) {
            echo $exception;
        }

    }

    public function addRecipient($companyId,$recipient_arr,$groupId)
    {
        $login = array('username' => $this->username, 'password' => $this->password );
        $client = $this->init_client();
        try {
            $result = $client->addRecipients(
                $login,
                $companyId,
                array($groupId), // GroupID
                // array('name'=>'email','value'=>'278583642@qq.com')
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

    public function setSoap($soap)
    {
        $this->soap = $soap;

        return $this;
    }

    public function getSoap()
    {
        return $this->soap;
    }

    public function setUsername($username)
    {
        $this->username= $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password= $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    private function getPoint($userid,$point,$type)
    {
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      switch($uid){
            case 0:
                  $po = new PointHistory00();
                  break;
            case 1:
                  $po = new PointHistory01();
                  break;
            case 2:
                  $po = new PointHistory02();
                  break;
            case 3:
                  $po = new PointHistory03();
                  break;
            case 4:
                  $po = new PointHistory04();
                  break;
            case 5:
                  $po = new PointHistory05();
                  break;
            case 6:
                  $po = new PointHistory06();
                  break;
            case 7:
                  $po = new PointHistory07();
                  break;
            case 8:
                  $po = new PointHistory08();
                  break;
            case 9:
                  $po = new PointHistory09();
                  break;
      }
      $em = $this->getDoctrine()->getManager();
      $po->setUserId($userid);
      $po->setPointChangeNum($point);
      $po->setReason($type);
      $em->persist($po);
      $em->flush();
    }



}
