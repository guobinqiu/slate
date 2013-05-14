<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Entity\AdwAccessRecord;
use Jili\ApiBundle\Entity\Advertiserment;

class AdvertisermentController extends Controller
{
	/**
	 * @Route("/info/{id}", name="_advertiserment_index")
	 */
	public function infoAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
        $arr['advertiserment'] = $advertiserment;
		return $this->render('JiliApiBundle:Advertiserment:info.html.twig',$arr);
	}
	/**
	 * @Route("/list", name="_advertiserment_list")
	 */
	public function listAction(){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment();

		$arr['advertiserment'] = $advertise;

		return $this->render('JiliApiBundle:Advertiserment:list.html.twig',$arr);
	}
	
	/**
	 * @Route("/click/{id}", name="_advertiserment_click")
	 */
	public function clickAction($id){

// 		$em = $this->getDoctrine()->getManager();
// 		$sql = 'select ad.title,ad.content,a.type,a.position from advertiserment ad inner join ad_position a on ad.id = a.ad_id where ad.id=:id';
// 		$advertise = $em->getConnection()->executeQuery($sql,array('id'=>$id))->fetchAll();

		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment(1);
		
// 		$param = array(
// 				'date'=>date('Ymd'),
// 				'time'=>date('His'),
// 				'type'=>$advertise[0]['type'],
// 				'pid'=>$id,
// 				'mid'=>$advertise[0]['title'],
// 				'extinfo'=>'334455',
// 				'userinfo'=>'Uid3212',
// 				'comm'=>$advertise[0]['comm'],
// 				'totalprice'=>$advertise[0]['totalprice'],
// 				'ocd'=>$advertise[0]['ocd'],
// 				'goodspricecount'=>$advertise[0]['goodspricecount'],
// 				'paymentmethod'=>$advertise[0]['paymentmethod'],	
// 				'paid'=>$advertise[0]['paid'],	
// 				'status'=>$advertise[0]['status'],
// 				'confirm'=>$advertise[0]['confirm'],
// 				'sig'=>f754bc8afdc96f4dbf47c4ef613dfefa,
// 				);
		$param = array(
				'date'=>date('Ymd'),
				'time'=>date('His'),
				'type'=>1,
				'pid'=>1915,
				'mid'=>'%E5%A4%A9%E5%A4%A9%E7%9B%88CPA%E6%B4%BB%E5%8A%A8',
				'extinfo'=>'334455',
				'userinfo'=>'Uid3212',
				'comm'=>20.0000,
				'totalprice'=>100.0000,
				'ocd'=>'8845114535',
				'goodspricecount'=>'GOODS2/5%/0.00/20.00/2/tushu:GOODS2/20%/0.00/60.00/1/tushu',
				'paymentmethod'=>0,
				'paid'=>1,
				'status'=>2,
				'confirm'=>1,
				'sig'=>'f754bc8afdc96f4dbf47c4ef613dfefa',
		);

// 		print_r($this->getStatus($param));
		
// 		$adwAccessRecord = new AdwAccessRecord();
// 		$datetime = array('date'=>date('Y-m-d H:i:s'),'timezone_type'=>3,'timezone'=>'Europe/Berlin');
// 		$adwAccessRecord->setTime($datetime);
// 		$adwAccessRecord->setKey('');
// 		$em = $this->getDoctrine()->getManager();
// 		$em->persist($adwAccessRecord);
// 		$em->flush();
		return $this->render('JiliApiBundle:Advertiserment:list.html.twig');
	}
	
	
	private function getStatus($param){
		extract($param);
		$sig = md5("date=".$date."&time=".$time."&pid=".$pid."&comm=".$comm."&totalprice=".$totalprice."&ocd=".$ocd."&2ShQWfkHzu6MhNFZ");
		$ch = curl_init();
		if($type==1)
			$curl_url = "http://www.domain.com/api/real_time_data.php?date=".$date."&time=".$time."&type=".$type."&pid=".$pid."&mid=".$mid."&extinfo=".$extinfo."&userinfo=".$userinfo."&comm=".$comm."&sig=".$sig;
		else
    		$curl_url = "http://www.domain.com/api/real_time_data.php?date=".$date."&time=".$time."&type=".$type."&pid=".$pid."&mid=".$mid."&extinfo=".$extinfo."&userinfo=".$userinfo."&comm=".$comm."&totalPrice=".$totalprice."&ocd=".$ocd."&goodspricecount=".$goodspricecount."&paid=".$paid."&status=".$status."&paymentmethod=".$paymentmethod."&confirm=".$confrim."&sig=".$sig;
		echo $curl_url;
		curl_setopt($ch, CURLOPT_URL, $curl_url);    
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
		$curl_result = curl_exec($ch);
		$result = explode(',', $curl_result);
		curl_close($ch);
        return $result;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
