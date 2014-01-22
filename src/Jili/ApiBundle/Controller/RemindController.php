<?php
namespace Jili\ApiBundle\Controller;
use Symfony \ Bundle \ FrameworkBundle \ Controller \ Controller;
use Sensio \ Bundle \ FrameworkExtraBundle \ Configuration \ Route;
use Symfony \ Component \ HttpFoundation \ Response;

class RemindController extends Controller {

	/**
	 * @Route("/remindLogin", name="_remind_remindLogin")
	 */
	public function remindLoginAction() {

		//七天未登陆提醒
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->getUserListForRemindLogin(8);
		$total = $em->getRepository('JiliApiBundle:User')->totalUserAndCount();

		$recipient_arr = array ();
		foreach ($user as $item) {
			$recipient_arr[] = array (
				array (
					'name' => 'email',
					'value' => $item['email']
				),
				array (
					'name' => 'userCount',
					'value' => $total['total_user']
				),
				array (
					'name' => 'totalPoints',
					'value' => $total['total_points']
				)
			);
		}

		//send email by soap
		$soapMailLister = $this->get('soap.mail.listener');
		$soapMailLister->setCampaignId($this->container->getParameter('remind_login_campaign_id')); //活动id
		$soapMailLister->setMailingId($this->container->getParameter('remind_login_mailing_id')); //邮件id
		$soapMailLister->setGroup(array ('name' => 'remindLogin', 'is_test' => 'true'));
		$return = $soapMailLister->addRecipientsSendMailing($recipient_arr);

		return new Response($return);
	}

	/**
	 * @Route("/remindPoint", name="_remind_remindPoint")
	 */
/*	public function remindPointAction() {

		//1,2,3,17 广告体验,购物返利,游戏广告,offer-wow 获得积分提醒
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->getUserListForRemindPoint(1);

		$recipient_arr = array ();
		foreach ($user as $item) {
			$recipient_arr[] = array (
				array (
					'name' => 'email',
					'value' => $item['email']
				),
				array (
					'name' => 'nick',
					'value' => $item['nick']
				),
				array (
					'name' => 'point_change_num',
					'value' => $item['point_change_num']
				),
				array (
					'name' => 'display_name',
					'value' => $item['display_name']
				)
			);
		}

		//send email by soap
		$soapMailLister = $this->get('soap.mail.listener');
		$soapMailLister->setCampaignId($this->container->getParameter('remind_point_campaign_id')); //活动id
		$soapMailLister->setMailingId($this->container->getParameter('remind_point_mailing_id')); //邮件id
		$soapMailLister->setGroup(array ('name' => 'remindPoint', 'is_test' => 'true'));
		$return = $soapMailLister->addRecipientsSendMailing($recipient_arr);

		return new Response($return);
	} */

}