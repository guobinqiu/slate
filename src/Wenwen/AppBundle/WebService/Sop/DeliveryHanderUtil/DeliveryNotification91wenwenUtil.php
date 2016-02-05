<?php
namespace Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil;

class DeliveryNotification91wenwenUtil
{
    public static function retrieveValidRecipientData($app_mid)
    {
         //todo: 根据app_mid 获得用户 email, nick, 性别（女士，先生）， SopRespondent.status_flag= STATUS_ACTIVE
//         return SopRespondentPeer::retrieve91wenwenRecipientData($app_mid);
return array();
    }

    public static function sendMailing($respondents)
    {
        //todo: 调用共通的发邮件系统
//         $pager = new rpaPager(sizeof($respondents), static::$EMAIL_PER_JOB, 1);
//         $job_ids = array();

//         $group_name_tmpl = sprintf('tmp_sop_notification-%d-%s', date('YmdHis'), bin2hex(openssl_random_pseudo_bytes(4)));

//         for ($p = 1; $p <= $pager->getLastPage(); $p++) {

//             $respondent_group = array_splice(
//                 $respondents,
//                 $pager->getOffset(),
//                 $pager->getEntriesPerPage()
//             );

//             # 送信先がない
//             if (!sizeof($respondent_group)) {
//                 break;
//             }

//             # group_name
//             $group_name = $group_name_tmpl . "-page$p";

//             $add_recipients = array();
//             foreach ($respondent_group as $respondent) {
//                 $add_recipients[] = static::getRecipientFromRespondent($respondent);
//             }

//             $job = new TheSchwartzJob(array(
//                 'funcname' => 'RPA::TheSchwartz::Worker::DMDelivery',
//                 'arg' => array(
//                     'platform' => static::$PLATFORM,
//                     'campaign_id' => static::$CAMPAIGN_ID,
//                     'mailing_id' => static::$MAILING_ID,
//                     'add_group' => array(
//                         'group_name' => $group_name,
//                     ),
//                     'add_recipients' => $add_recipients,
//                     'send_mailing' => array(
//                         'results_email' => 'rpa-sys@d8aspring.com',
//                     ),
//                 ),
//             ));
//             $job_ids[] = $job->save();

//             $pager->setCurrentPage($pager->getCurrentPage() + 1);
//         }

//         return $job_ids;
return array();
    }

    public static function getRecipientFromRespondent($respondent)
    {
        throw new Exception("Implemet getRecipientFromRespondent", 1);
    }
}
