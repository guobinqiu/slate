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
					'name' => 'total_user',
					'value' => number_format($total['total_user'])
				),
				array (
					'name' => 'total_points',
					'value' => number_format($total['total_points'])
				)
			);
		}

		//send email by soap
		$soapMailLister = $this->get('soap.mail.listener');
		$soapMailLister->setCampaignId($this->container->getParameter('remind_login_campaign_id')); //活动id
		$soapMailLister->setMailingId($this->container->getParameter('remind_login_mailing_id')); //邮件id
		$soapMailLister->setGroup(array (
			'name' => 'remindLogin',
			'is_test' => 'false'
		));
		$return = $soapMailLister->addRecipientsSendMailing($recipient_arr);

		return new Response($return);
	}

	/**
	 * @Route("/remindPoint", name="_remind_remindPoint")
	 */
	public function remindPointAction() {

		//1,2,3 广告体验,购物返利,游戏广告,获得积分提醒
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->getUserListForRemindPoint(1);

		//send email by soap
		$soapMailLister = $this->get('soap.mail.listener');
		$soapMailLister->setCampaignId($this->container->getParameter('remind_point_campaign_id')); //活动id
		$soapMailLister->setMailingId($this->container->getParameter('remind_point_mailing_id')); //邮件id
		$soapMailLister->setGroup(array (
			'name' => 'remindPoint',
			'is_test' => 'false'
		));

		foreach ($user as $item) {
			$recipient_arr = array (
				array (
					'name' => 'email',
					'value' => $item['email']
				),
				array (
					'name' => 'date',
					'value' => $item['date']
				),
				array (
					'name' => 'task_name',
					'value' => trim($item['task_name'])
				),
				array (
					'name' => 'point',
					'value' => number_format($item['point'])
				),
				array (
					'name' => 'display_name',
					'value' => trim($item['display_name'])
				)
			);
			$return = $soapMailLister->sendSingleMailing($recipient_arr);
		}

		return new Response($return);
	}

}