<?php
namespace Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil;

class FulcrumDeliveryNotification91wenwenUtil extends DeliveryNotification91wenwenUtil
{
    public static $PLATFORM = 'message';
    public static $CAMPAIGN_ID = '23'; # 91wenwen-survey-mailing2
    public static $MAILING_ID = '90024'; # survey-mail-fulcrum before: '90004'
    public static $EMAIL_PER_JOB = 100;

    public static function getRecipientFromRespondent($respondent)
    {
        return array (
            'name1' => $respondent['recipient']['name1'],
            'email' => $respondent['recipient']['email'],
            'title' => $respondent['recipient']['title'],
            'survey_title' => $respondent['title'],
            'survey_point' => $respondent['extra_info']['point']['complete']
        );
    }
}
