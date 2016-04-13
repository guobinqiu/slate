<?php
namespace Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Jili\ApiBundle\Utility\String;

class DeliveryNotification91wenwenUtil
{

    public static function retrieveValidRecipientData($app_mid, $em)
    {
        return $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientDataBySopRespondentId($app_mid);
    }

    public static function sendMailing($container, $respondents, $em)
    {
        $respondent_groups = array_chunk($respondents, static::$EMAIL_PER_JOB, true);
        $job_ids = array ();

        $group_name_tmpl = sprintf('tmp_sop_notification-%d-%s', date('YmdHis'), bin2hex(openssl_random_pseudo_bytes(4)));

        foreach ($respondent_groups as $key => $respondent_group) {

            # group_name
            $group_name = $group_name_tmpl . "-page" . ($key + 1);

            $add_recipients = array ();

            foreach ($respondent_group as $respondent) {
                $data = static::getRecipientFromRespondent($respondent);
                $add_recipients[] = String::encodeForCommandArgument($data);
            }

            $args = array (
                '--campaign_id=' . static::$CAMPAIGN_ID,
                '--mailing_id=' . static::$MAILING_ID,
                '--group_name=' . $group_name,
                'recipients=' . implode(' ', $add_recipients)
            );

            //调用共通的发邮件系统
            $job = new Job('research_survey:delivery_notification', $args, true, '91wenwen');
            $em->persist($job);
            $em->flush($job);
            $job_ids[] = $job->getId();
        }
        return $job_ids;
    }

    public static function getRecipientFromRespondent($respondent)
    {
        throw new Exception("Implemet getRecipientFromRespondent", 1);
    }
}
