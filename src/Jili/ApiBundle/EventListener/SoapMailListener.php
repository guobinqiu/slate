<?php
namespace Jili\ApiBundle\EventListener;

/**
 *
 **/
class SoapMailListener {

	private $soap = 'http://91jili.dmdelivery.com/x/soap-v4/wsdl.php';
	private $username = 'admin';
	private $password = 'Nvpiyjh1-';
	private $campaignId;
	private $mailingId;
	private $group;

	public function __construct() {
	}

	public function setCampaignId($campaignId) {
		$this->campaignId = $campaignId;
	}

	public function setMailingId($mailingId) {
		$this->mailingId = $mailingId;
	}

	public function setGroup($group) {
		$this->group = $group;
	}

	private function init_client() {
		ini_set('soap.wsdl_cache_enabled', '0');
		$client = new \ SoapClient($this->soap, array (
			'encoding' => 'utf-8',
			'features' => SOAP_SINGLE_ELEMENT_ARRAYS
		));
		return $client;
	}

	private function login_info() {
		$login = array (
			'username' => $this->username,
			'password' => $this->password
		);
		return $login;
	}

	public function sendSingleMailing($recipient_arr) {
		$login = $this->login_info();
		$client = $this->init_client();
		try {
			$group = $client->addGroup($login, $this->campaignId, $this->group);

			if ($group->status == "ERROR") {
				$rs = 'Cannot add group:' . $group->statusMsg;
				return $rs;
			}

			$addRecipient_result = $client->addRecipient($login, $this->campaignId, array (
				$group->id
			), $recipient_arr, true, true);

			if ($addRecipient_result->status == "ERROR") {
				$re = "addRecipient error';";
				return $rs;
			}

			$sendMailing_result = $client->sendSingleMailing($login, $this->campaignId, $this->mailingId, $addRecipient_result->id);

			if ($sendMailing_result) {
				$rs = 'Email send success';
			} else {
				$rs = 'Email send fail';
			}
			return $rs;
		} catch (SoapFault $exception) {

		}
	}

	public function addRecipientsSendMailing($recipient_arr) {
		$login = $this->login_info();
		$client = $this->init_client();
		try {
			$group = $client->addGroup($login, $this->campaignId, $this->group);

			if ($group->status == "ERROR") {
				$rs = 'Cannot add group:' . $group->statusMsg;
				return $rs;
			}

			$result = $client->addRecipientsSendMailing($login, $this->campaignId, $this->mailingId, array (
				$group->id
			), $recipient_arr, true, true);

			if ($result->status != "ERROR") {
				$rs = 'Email send success';
			} else {
				$rs = 'Email send fail';
			}

			return $rs;
		} catch (SoapFault $exception) {
			echo $exception;
		}
	}

}
?>