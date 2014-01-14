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

	public function __construct() {
	}

	public function setCampaignId($campaignId) {
		$this->campaignId = $campaignId;
	}

	public function setMailingId($mailingId) {
		$this->mailingId = $mailingId;
	}

	public function sendMailBySoap($recipient_arr) {

		$login = array (
			'username' => $this->username,
			'password' => $this->password
		);
		$client = $this->init_client();
		try {
			$group = $client->addGroup($login, $this->campaignId, array (
				'name' => '积粒网',
				'is_test' => 'false'
			));

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

			$sendMailing_result = $client->sendMailing($login, $this->campaignId, $this->mailingId, false, "yang@voyagegroup.com.cn", array (
				$group->id
			), "", "", "", "");

			if ($sendMailing_result->status != "ERROR") {
				$rs = 'Email send success';
			} else {
				$rs = 'Email send fail';
			}
			return $rs;
		} catch (SoapFault $exception) {

		}
	}

	public function init_client() {
		ini_set('soap.wsdl_cache_enabled', '0');
		$client = new \ SoapClient($this->soap, array (
			'encoding' => 'utf-8',
			'features' => SOAP_SINGLE_ELEMENT_ARRAYS
		));
		return $client;
	}

}
?>