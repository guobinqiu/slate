<?php
namespace Wenwen\AppBundle\WebService\Sop;
use Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\SopDeliveryNotification91wenwenUtil;
use Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\FulcrumDeliveryNotification91wenwenUtil;

class SopDeliveryNotificationHandler
{
    const TYPE_SOP     = 0;
    const TYPE_FULCRUM = 1;

    private $respondents           = null;
    private $panel_id              = null;
    private $util_class            = null;
    private $valid_respondends     = array();
    private $unsubscribed_app_mids = array();

    private $util_map = array(
                self::TYPE_SOP     => 'SopDeliveryNotification91wenwenUtil',
                self::TYPE_FULCRUM => 'FulcrumDeliveryNotification91wenwenUtil',
        );

    public function __construct($respondents, $type)
    {
        $this->respondents = $respondents;
        $this->util_class  = $this->util_map[$type];
    }

    public function getUtilClass()
    {
        return $this->util_class;
    }

    public function getRespondents()
    {
        return $this->respondents;
    }

    public function getValidRespondents()
    {
        //return $this->valid_respondents;
        //todo
        return array();
    }

    public function getUnsubscribedAppMids()
    {
        return $this->unsubscribed_app_mids;
    }

    public function setUpRespondentsToMail()
    {
        $respondents = $this->getRespondents();

        $util_class = $this->util_class;
        //todo: 使用use的方式找不到
        $util_class = '\Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\\'.$util_class;

        foreach ($respondents as $respondent) {

            $recipient = $util_class::retrieveValidRecipientData($respondent['app_mid']);
            if ($recipient) {
                $respondent['recipient'] = $recipient;
                $this->valid_respondents[] = $respondent;
            } else {
                $this->unsubscribed_app_mids[] = $respondent['app_mid'];
            }
        }
    }

    public function sendMailingToRespondents()
    {
        $util_class = $this->util_class;
        //todo: 使用use的方式找不到
        $util_class = '\Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\\'.$util_class;
        # jobids
        return $util_class::sendMailing($this->getValidRespondents());
    }
}
