<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessHistory;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\CheckinAdverList;
use Jili\ApiBundle\Entity\CheckinUserList;
use Jili\ApiBundle\Entity\CheckinClickList;
use Jili\ApiBundle\Entity\CheckinPointTimes;

/**
 * @Route( requirements={"_scheme": "http"})
 */
class CheckLocationController extends Controller
{
/**
	 * @Route("/info",name="_checkLocation_info",requirements={"_scheme"="http"})
	 */
	public function infoAction()
	{
		$em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
		$uid = $request->getSession()->get('uid');
		$markId = $request->query->get('markid');
		$aid = $request->query->get('aid');
		$type = $request->query->get('type');
		switch ($type) {
			case '1':
			    $firstUrl = $this->advInfo($uid,$aid);
				$lastUrl = "";
				break;
			case '2':
				$busiAct = $em->getRepository('JiliApiBundle:MarketActivity')->existMarket($markId);
				if(empty($busiAct)){
					return $this->redirect($this->generateUrl('_default_error'));
				}
				$firstUrl = $this->advInfo($uid,$busiAct[0]['aid']);
				$lastUrl = $busiAct[0]['activityUrl'];
				break;
			default:
				# code...
				break;
		}
		
		return $this->render('JiliApiBundle:Checkin:info.html.twig',
				array('firstUrl'=>$firstUrl,'lastUrl'=>$lastUrl,'type'=>$type));

	}

	public function advInfo($uid,$aid){
		$em = $this->getDoctrine()->getManager();
		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
		$adw_info = $advertiserment->getImageurl();
		$adw_info = explode("u=",$adw_info);
		$new_url = trim($adw_info[0])."u=".$uid.trim($adw_info[1]).$aid;
		return trim($new_url);
	}
}
