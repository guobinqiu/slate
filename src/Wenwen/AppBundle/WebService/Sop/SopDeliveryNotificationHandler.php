<?php
namespace Wenwen\AppBundle\WebService\Sop;

use Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\SopDeliveryNotification91wenwenUtil;
use Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\FulcrumDeliveryNotification91wenwenUtil;

class SopDeliveryNotificationHandler
{
    const TYPE_SOP = 0;
    const TYPE_FULCRUM = 1;
    private $respondents = null;
    private $panel_id = null;
    private $util_class = null;
    private $valid_respondents = array ();
    private $unsubscribed_app_mids = array ();
    private $em = null;
    private $container = null;
    private $util_map = array (
        self::TYPE_SOP => 'SopDeliveryNotification91wenwenUtil',
        self::TYPE_FULCRUM => 'FulcrumDeliveryNotification91wenwenUtil'
    );

    public function __construct($respondents, $type, $em, $container)
    {
        $this->respondents = $respondents;
        $this->util_class = $this->util_map[$type];
        $this->em = $em;
        $this->container = $container;
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
        return $this->valid_respondents;
    }

    public function getUnsubscribedAppMids()
    {
        return $this->unsubscribed_app_mids;
    }

    public function setUpRespondentsToMail()
    {
        $respondents = $this->getRespondents();
        $em = $this->em;

        $util_class = $this->util_class;
        //注: 使用use的方式找不到
        $util_class = '\Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\\' . $util_class;

        foreach ($respondents as $respondent) {

            $recipient = $util_class::retrieveValidRecipientData($respondent['app_mid'], $em);

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
        $container = $this->container;
        $em = $this->em;

        //备注: 使用use的方式找不到
        $util_class = '\Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\\' . $util_class;
        # jobids
        return $util_class::sendMailing($container, $this->getValidRespondents(), $em);
    }
}
