<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessRecord;

class ApiController extends Controller
{
	/**
	 * @Route("/getAdInfo", name="_api_getAdInfo")
	 */
    public function getAdInfoAction()
    {
//     	echo md5('20130516&104600&1&20&100&8845114535&XLGt8P9wgCz9QPfJ');
        $code = array('code'=>'','msg'=>'');
    	$request = $this->get('request');
    	$id =1;
    	$em = $this->getDoctrine()->getManager();
    	$advertise = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
    	$getUrl = $advertise->getContent();
    	$u = explode("u=",$getUrl);
    	$u = explode("&e=",$u[1]);
    	$u_extinfo = $u[1];
    	$u_userinfo = $u[0];
		if($request->query->get('date')=='20130516' && $request->query->get('time')=='104600' && $request->query->get('type')==$advertise->getCategory() &&
		$request->query->get('promotionID')==$id && $request->query->get('promotionName')==$advertise->getTitle() && $request->query->get('extinfo')==$u_extinfo&&
		$request->query->get('userinfo')==$u_userinfo&&	$request->query->get('comm')==$advertise->getComm()&&$request->query->get('totalPrice')==$advertise->getTotalprice()&&
		$request->query->get('ocd')==$advertise->getOcd()&&	$request->query->get('goodDetails')==$advertise->getGoodspricecount()&&$request->query->get('paymentmethod')==$advertise->getPaymentmethod()&&
		$request->query->get('paid')==$advertise->getPaid()	&&$request->query->get('status')==$advertise->getStatus()&&$request->query->get('confirm')==$advertise->getConfirm()){
		$u_sig =md5($request->query->get('date')."&".$request->query->get('time')."&".$request->query->get('promotionID')."&".$request->query->get('comm')."&".$request->query->get('totalPrice')."&".$request->query->get('ocd')."&XLGt8P9wgCz9QPfJ");
			if($u_sig == $request->query->get('sig')){
				$repository = $em->getRepository('JiliApiBundle:AdwAccessRecord');
				$adwaccess = $repository->getAccessExist($u_userinfo,$u_extinfo);
				if(!empty($adwaccess[0])){
					$adwAccessRecord = new  AdwAccessRecord();
					$adwAccessRecord->setUserId($u[0]);
					$adwAccessRecord->setAdId($u[1]);
					$adwAccessRecord->setAction('点击广告');
					$adwAccessRecord->setAdTime(date_create(date('Y-m-d H:i:s')));
					$adwAccessRecord->setAdKey('aaaaa');
					$adwAccessRecord->setFlag(1);
					$em->persist($adwAccessRecord);
					$em->flush();
					$code = array('code'=>'1','msg'=>'The information is correct');
				}else{
					$code = array('code'=>'5','msg'=>'Orders already exists');
				}
				
			}else{
				$code = array('code'=>'3','msg'=>'Incorrect parameter');
			}
		}else{
			$code = array('code'=>'2','msg'=>'Signature verification is incorrect');
		}

		return new Response(json_encode($code));
    }
    
    
}
