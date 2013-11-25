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

class DmdeliveryController extends Controller
{
	private $soap = 'http://91jili.dmdelivery.com/x/soap-v4/wsdl.php';
	private $username = 'admin';
	private $password = 'Nvpiyjh1-';
	/**
	 * @Route("/pointFailure", name="_dmdelivery_pointFailure")
	 */
	public function pointFailureAction()
	{	
		$failTime = 180;
		$companyId = 4;
		$mailingId = 23;
		$rs = $this->handleSendPointFail($failTime,$companyId,$mailingId);
		return new Response($rs);

	}

	/**
	 * @Route("/pointFailureForWeek", name="_dmdelivery_pointFailureForWeek")
	 */
	public function pointFailureForWeekAction()
	{
		$failTime = 173;
		$companyId = 4;
		$mailingId = 23;
		$rs = $this->handleSendPointFail($failTime,$companyId,$mailingId);
		return new Response($rs);

	}

	/**
	 * @Route("/pointFailureForMonth", name="_dmdelivery_pointFailureForMonth")
	 */
	public function pointFailureForMonthAction()
	{
		$failTime = 150;
		$companyId = 4;
		$mailingId = 23;
		$rs = $this->handleSendPointFail($failTime,$companyId,$mailingId);
		return new Response($rs);

	}

	public function issetFailRecord($user_id,$failTime){
		$em = $this->getDoctrine()->getManager();
		$failRecord = $em->getRepository('JiliApiBundle:SendPointFail')->issetFailRecord($user_id,$failTime);
		if(empty($failRecord))
			return '';
		else
			return $failRecord[0]['userId'];
	}

	public function handleSendPointFail($failTime,$companyId,$mailingId){
		$recipient_arr = array();
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->pointFail($failTime);
		if(!empty($user)){
			$group = $this->addgroup($companyId);
			if($group->status != "ERROR"){
				foreach ($user as $key => $value) {
					$failId = $this->issetFailRecord($value['id'],$failTime);
					if(!$failId)
						$recipient_arr[] = array(array('name'=>'email','value'=>$value['email']),
												 array('name'=>'nick','value'=>$value['nick']));
					else 
						unset($user[$key]);
				}
				if(!empty($recipient_arr)){
					$send = $this->addRecipientsSendMailing($companyId,$mailingId,$group->id,$recipient_arr);
	                if($send->status == "OK"){
	                	foreach ($user as $key => $value){
							$this->insertSendPointFail($value['id'],$failTime);
							if($failTime == 180){
								$this->updatePointZero($value['id']);
							}
						}
						$rs = 'Send email successfully';
	                }elseif($send->status == "DUPLICATE"){
						$rs = 'Email has already send';
	                }else{
						$rs = 'Cannot send email:'.$send->statusMsg;
	                }
				}else{
					$rs = 'Email list is empty';
				}  
			}else{
				$rs = 'Cannot add group:'.$group->statusMsg;
			}
		}else{
			$rs = 'Email list is empty';
		}
		return $rs;
	}

	public function updatePointZero($userId){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($userId);
		$user->setPoints($this->container->getParameter('init'));
		$em->persist($user);
		$em->flush();

	}

	public function insertSendPointFail($userId,$type){
		$em = $this->getDoctrine()->getManager();
		$sendPoint = new SendPointFail();
		$sendPoint->setUserId($userId);
		$sendPoint->setSendType($type);
		$em->persist($sendPoint);
		$em->flush();
	}

	public function init_client(){
		ini_set('soap.wsdl_cache_enabled','0');
		$client = new \SoapClient($this->soap,array('encoding'=>'utf-8', 'features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
		return $client;
	}

	public function addRecipientsSendMailing($companyId,$mailingId,$groupId,$recipient_arr){
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
		}         
		catch (SoapFault $exception) { 
		    echo $exception;
		}

	}

	public function sendmailing($companyId,$mailingId,$groupId){
		$login = array('username' => $this->username, 'password' => $this->password);
		$client = $this->init_client();
		try {
		    $result = $client->sendMailing(
		        $login,
				$companyId,
		        $mailingId,
				true,
				"leeqinm@sina.cn",
		        array($groupId),
				"",
				"",
				"",
				""
	       		);
		    return $result;
		}         
		catch (SoapFault $exception) { 
		    echo $exception;
		}

	}

	public function addRecipient($companyId,$recipient_arr,$groupId){
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
		}         
		catch (SoapFault $exception) { 
		    echo $exception;
		}
	}

	public function addgroup($companyId){
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


}
?>