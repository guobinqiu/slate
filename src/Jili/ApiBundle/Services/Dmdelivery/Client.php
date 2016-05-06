<?php
namespace Jili\ApiBundle\Services\Dmdelivery;

use Psr\Log\LoggerInterface;

/**
 *
 **/
class Client 
{
    private $soap ;
    private $username ;
    private $password ;

    private $campaignId;
    private $mailingId;
    private $groupId;
    private $group;

    private $logger;

    private $resultsEmail;

    public function __construct($url , $user, $pass )
    {
        $this->updateConfig($url , $user, $pass);
    }

    public function updateConfig($url , $user, $pass )
    {
        $this->soap = $url; 
        $this->username = $user;
        $this->password =  $pass;
    }

    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
        return $this;
    }

    public function setMailingId($mailingId)
    {
        $this->mailingId = $mailingId;
        return $this;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    private function init_client()
    {
        ini_set('soap.wsdl_cache_enabled', '0');
        $this->logger->info('soap:'.$this->soap);
        $client = new \SoapClient($this->soap, array (
            'encoding' => 'utf-8',
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        ));
        return $client;
    }

    private function login_info()
    {
        $login = array (
            'username' => $this->username,
            'password' => $this->password
        );
        return $login;
    }

    public function sendSingleMailing($recipient_arr)
    {
        $login = $this->login_info();
        $client = $this->init_client();
        $logger = $this->logger;
        try {
            $group = $client->addGroup($login, $this->campaignId, $this->group);

            if ($group->status == "ERROR") {
                $rs = 'Cannot add group:' . $group->statusMsg;
                $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $rs  );
                return $rs;
            }

            $addRecipient_result = $client->addRecipient($login, $this->campaignId, array (
                $group->id
            ), $recipient_arr, true, true);

            if ($addRecipient_result->status == "ERROR") {
                $rs = "addRecipient error". $addRecipient_result->statusMsg;
                $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $rs  );
                return $rs;
            }

            $sendMailing_result = $client->sendSingleMailing($login, $this->campaignId, $this->mailingId, $addRecipient_result->id);

            if ($sendMailing_result) {
                $rs = 'Email send success';
            } else {
                $rs = 'Email send fail';
            }
                $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $rs  );
            return $rs;
        } catch (SoapFault $e) {
            $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $e->getMessage()  );
        }
    }

    /**
     *
     */
    public function addRecipientsSendMailing($recipient_arr)
    {
        $login = $this->login_info();
        $client = $this->init_client();
        try {
            $group = $client->addGroup($login, $this->campaignId, $this->group);

            if ($group->status == "ERROR") {
                $rs = 'Cannot add group:' . $group->statusMsg;
                $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $group->statusMsg);
                return $rs;
            }

            $result = $client->addRecipientsSendMailing($login, $this->campaignId, $this->mailingId, array (
                $group->id
            ), $this->buildRecipientData($recipient_arr), true, true);

            if ($result->status != "ERROR") {
                $rs = 'Email send success';
            } else {
                $rs = 'Email send fail';
                $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $result->statusMsg);
            }

            return $rs;
        } catch (SoapFault $exception) {
            echo $exception;
            $logger->debug( '['.implode(':',array(__LINE__,__FUNCTION__,__CLASS__)).']'. $exception->getMessage()  );
        }
    }

    public function sendMailing($recipient_arr)
    {
        $login = $this->login_info();
        $client = $this->init_client();
        $logger = $this->logger;
        try {
            $group = $client->addGroup($login, $this->campaignId, $this->group);

            if ($group->status == 'ERROR') {
                $rs = 'Cannot add group:' . $group->statusMsg;
                $logger->debug( '[SoapMailListener]'.implode(':',array(__LINE__,'')). $rs );
                return $rs;
            }

            $addRecipient_result = $client->addRecipients($login, $this->campaignId, array (
                $group->id
            ), $this->buildRecipientData($recipient_arr), true, true);

            if ($addRecipient_result->status == "ERROR") {
                $re = "addRecipient error';";
                $logger->debug( '[SoapMailListener]'.implode(':',array(__LINE__,'')). $re .$addRecipient_result->statusMsg);
                return $re;
            }


            $result = $client->sendMailing($login, $this->campaignId, $this->mailingId, true, $this->resultsEmail, array (
                $group->id
            ), "", "", "", "");

            if ($result->status != "ERROR") {
                $rs = 'Email send success';
            } else {
                $rs = 'Email send fail';
            }

            $logger->debug( '[SoapMailListener]'.implode(':',array(__LINE__,'')). $rs );
            return $rs;
        } catch (SoapFault $exception) {
            $logger->debug( '[SoapMailListener]'.implode(':',array(__LINE__,'')). $exception->getMessage()  );
            echo $exception;
        }
    }

    public function singleEmail(array $recipient) 
    {
        $login = $this->login_info();
        $client = $this->init_client();
        try {

            $addDuplisToGroups = true;
            $overwrite = true;

            $result = $client->addRecipientsSendMailing($login,
                $this->campaignId,
                $this->mailingId,
                array($this->groupId), 
                $this->buildRecipientData( array( $recipient)),
                $addDuplisToGroups,$overwrite);

            if ($result->status != 'ERROR') {
                $rs = 'Email send success';
            } else {
                $rs = 'Email send fail';
            }
            return $rs;
        } catch (\SoapFault $e) {
            $this->logger->crit($e->getMessage());
        }
    }

    public function buildRecipientData(array $recipientsData) 
    {
        $recipients = array();
        foreach(  $recipientsData as $recipient ) {
            $fields = array();
                foreach( $recipient as $name => $value ) {
                    $fields [] = array( 'name'=> $name, 'value'=> $value);
                }
            $recipients [] = array( 'fields' => $fields);
        }
        return array('recipients'=>$recipients);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setResultsEmail ( $email) 
    {
        $this->resultsEmail = $email; 
    }
}

