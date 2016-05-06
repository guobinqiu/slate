<?php

namespace Wenwen\AppBundle\Services\Notification\SurveyDelivery;

use Jili\ApiBundle\Utility\String;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\AppBundle\Entity\SsiRespondent;

class SsiProject
{
    public static $PLATFORM = 'message',
        $CAMPAIGN_ID = '23',      # 91wenwen-survey-mailing2
        $MAILING_ID = '89994',    # survey-mail-ssi(taobao_order) '89974'
        $EMAIL_PER_JOB = 100;

    private $respondentIds = null;
    private $em = null;
    private $container = null;

    private $ssiProjectConfig = null;

    public function __construct($respondentIds, $em, $container)
    {
        $this->respondentIds = $respondentIds;
        $this->em = $em;
        $this->container = $container;

        $this->ssiProjectConfig = $this->container->getParameter('ssi_project_survey');
    }

    public function getRespondentIds()
    {
        return $this->respondentIds;
    }

    public function retrieveRecipientsToMail()
    {
        $recipients = [];
        foreach ($this->getRespondentIds() as $respondentId) {
            $ssiRespondentId = \Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId($respondentId);

            $recipient = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->retrieveRecipientDataToSendMailById($ssiRespondentId);

            if ($recipient) {
                $recipients[] = $recipient;
            }
        }

        return $recipients;
    }

    public function getMailTemplateParams($ssiProjectId, $recipient)
    {
        return array(
            'name1' => $recipient['name1'],
            'email' => $recipient['email'],
            'title' => $recipient['title'],
            'survey_id' => $ssiProjectId,
            'survey_title' => $this->ssiProjectConfig['title'],
            'survey_point' => $this->ssiProjectConfig['point'],
        );
    }

    public function sendMailing($ssiProjectId, $recipients)
    {
        $recipient_groups = array_chunk($recipients, static::$EMAIL_PER_JOB, true);
        $job_ids = array ();

        $group_name_tmpl = sprintf('tmp_ssi_notification-%d-%s', date('YmdHis'), bin2hex(openssl_random_pseudo_bytes(4)));

        foreach ($recipient_groups as $key => $recipient_group) {

            $group_name = $group_name_tmpl . "-page" . ($key + 1);

            $add_recipients = array ();

            foreach ($recipient_group as $recipient) {
                $data = $this->getMailTemplateParams($ssiProjectId, $recipient);
                $add_recipients[] = String::encodeForCommandArgument($data);
            }

            $args = array (
                '--campaign_id=' . static::$CAMPAIGN_ID,
                '--mailing_id=' . static::$MAILING_ID,
                '--group_name=' . $group_name,
                implode(' ', $add_recipients)
            );

            //调用共通的发邮件系统
            $job = new Job('research_survey:delivery_notification', $args, true, '91wenwen');
            $this->em->persist($job);
            $this->em->flush($job);
            $job_ids[] = $job;
        }
        return $job_ids;
    }
}
