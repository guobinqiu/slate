<?php
namespace Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil;

class SopDeliveryNotification91wenwenUtil extends DeliveryNotification91wenwenUtil
{
    public static $PLATFORM = 'message';
    public static $CAMPAIGN_ID = '23'; # 91wenwen-survey-mailing2
    public static $MAILING_ID = '90025'; # survey-mail(Copy 20160512 R-)  before: 89998
    public static $EMAIL_PER_JOB = 100;

    public static function getRecipientFromRespondent($respondent)
    {
        return array (
            'name1' => $respondent['recipient']['name1'],
            'email' => $respondent['recipient']['email'],
            'title' => $respondent['recipient']['title'],
            'survey_title' => $respondent['title'],
            'survey_point' => $respondent['extra_info']['point']['complete'],
            'survey_length' => $respondent['loi']
        );
    }
}
