<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Services\Dependency\Mailer\IMailer;
use Wenwen\FrontendBundle\Services\Dependency\Mailer\SendCloudMailer;

class SopDeliveryNotificationMailCommand extends AbstractMailCommand {

    protected function configure()
    {
        $this->setName('mail:sop_delivery_notification');
        $this->setDescription('通知会员有新的SOP问卷');
        $this->addOption('name1', null, InputOption::VALUE_REQUIRED);
        $this->addOption('email', null, InputOption::VALUE_REQUIRED);
        $this->addOption('survey_title', null, InputOption::VALUE_REQUIRED);
        $this->addOption('survey_point', null, InputOption::VALUE_REQUIRED);
        $this->addOption('survey_length', null, InputOption::VALUE_REQUIRED);
        $this->addOption('subject', null, InputOption::VALUE_REQUIRED);
        $this->addOption('channel', null, InputOption::VALUE_OPTIONAL, '通道名：channel2｜channel3', 'channel2');
    }

    /**
     * @return IMailer
     */
    protected function createMailer(InputInterface $input)
    {
        $channel = $input->getOption('channel');

        $mailer = $this->getContainer()->getParameter('mailer');
        $sendcloud = $mailer['sendcloud'];
        $account = $sendcloud[$channel];

        return new SendCloudMailer(
            $account['api_user'],
            $account['api_key'],
            $sendcloud['url'],
            $account['from'],
            $this->getContainer()->get('app.http_client')
        );
    }

    /**
     * @return array
     */
    protected function getTemplateVars(InputInterface $input)
    {
        return array(
            'name1' => $input->getOption('name1'),
            'survey_title' => $input->getOption('survey_title'),
            'survey_point' => $input->getOption('survey_point'),
            'survey_length' => $input->getOption('survey_length'),
        );
    }

    /**
     * @return string
     */
    protected function getTemplatePath(InputInterface $input)
    {
        return 'WenwenFrontendBundle:EmailTemplate:sop_delivery_notification.html.twig';
    }

    /**
     * @return string
     */
    protected function getEmail(InputInterface $input)
    {
        return $input->getOption('email');
    }

    /**
     * @return string
     */
    protected function getSubject(InputInterface $input)
    {
        return $input->getOption('subject');
    }
}