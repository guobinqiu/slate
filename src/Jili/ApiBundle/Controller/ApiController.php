<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessHistory;
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

class ApiController extends Controller
{
	

	/**
	 * @Route("/getAdInfo", name="_api_getAdInfo")
	 */
	public function getAdInfoAction()
	{
		$code = array('code'=>'','msg'=>'');
		
	
	
//     	echo md5('20130516&104600&1&20&100&8845114535&XLGt8P9wgCz9QPfJ');
        
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
// 				$repository = $em->getRepository('JiliApiBundle:AdwAccessHistory');
// 				$adwaccess = $repository->getAccessExist($u_userinfo,$u_extinfo);
// 				if(!empty($adwaccess[0])){
					$adwAccessRecord = new  AdwAccessHistory();
					$adwAccessRecord->setUserId($u[0]);
					$adwAccessRecord->setAdId($u[1]);
					$adwAccessRecord->setAction('点击广告');
					$adwAccessRecord->setAdTime(date_create(date('Y-m-d H:i:s')));
					$adwAccessRecord->setAdKey('');
					$adwAccessRecord->setFlag(1);
					$em->persist($adwAccessRecord);
					$em->flush();
					$user_num = substr($u[1], 0, -1);
					if(!$user_num)
						$user_num = $u[1];
                    switch ($user_num){
                    	case '0':
                    		$pointHistory = new  PointHistory00();
                    		break;
                        case '1':
                        	$pointHistory = new  PointHistory01();
                        	break;
                        case '2':
                        	$pointHistory = new  PointHistory02();
                        	break;
                    	case '3':
                    		$pointHistory = new  PointHistory03();
                    		break;
                		case '4':
                			$pointHistory = new  PointHistory04();
                			break;
            			case '5':
            				$pointHistory = new  PointHistory05();
            				break;
        				case '6':
        					$pointHistory = new  PointHistory06();
        					break;
    					case '7':
    						$pointHistory = new  PointHistory07();
    						break;
						case '8':
							$pointHistory = new  PointHistory08();
							break;
						case '9':
							$pointHistory = new  PointHistory09();
							break;
                    }
                    $pointHistory->setUserId($u[1]);
                    $pointHistory->setPointChangeNum('50');
                    $pointHistory->setReason(1);
                    $em->persist($pointHistory);
                    $em->flush();
                    $user = $em->getRepository('JiliApiBundle:User')->find($u[1]);
                    $oldPoint = $user->getPoints();
                    $user->setPoints($oldPoint+50);
                    $em->persist($user);
                    $em->flush();
					$code = array('code'=>'1','msg'=>'The information is correct');
// 				}else{
// 					$code = array('code'=>'5','msg'=>'Orders already exists');
// 				}
				
			}else{
				$code = array('code'=>'3','msg'=>'Signature verification is incorrect');
			}
		}else{
			$code = array('code'=>'2','msg'=>'Incorrect parameter');
		}

		return new Response(json_encode($code));
    }
    
    
    
}
