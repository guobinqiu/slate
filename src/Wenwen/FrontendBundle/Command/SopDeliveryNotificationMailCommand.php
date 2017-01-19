<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\IMailer;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;

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
        $this->addOption('survey_id', null, InputOption::VALUE_REQUIRED);
        $this->addOption('survey_difficulty', null, InputOption::VALUE_REQUIRED);
        //$this->addOption('channel', null, InputOption::VALUE_REQUIRED, '可选值：channel2|channel3');//sendcloud
    }

    /**
     * @return IMailer
     */
    protected function createMailer(InputInterface $input)
    {
        // sendcloud
//        $channel = $input->getOption('channel');
//        $parameterService = $this->getContainer()->get('app.parameter_service');
//        $httpClient = $this->getContainer()->get('app.http_client');
//        return MailerFactory::createSendCloudMailer($parameterService, $httpClient, $channel);

        // webpower
        $parameterService = $this->getContainer()->get('app.parameter_service');
        return MailerFactory::createWebpowerMailer($parameterService);
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
            'survey_difficulty' => $input->getOption('survey_difficulty'),
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