<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\IMailer;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\SendCloudMailerFactory;

class SignupConfirmationMailCommand extends AbstractMailCommand
{
    protected function configure()
    {
        $this->setName('mail:signup_confirmation');
        $this->setDescription('发送激活确认邮件');
        $this->addOption('subject', null, InputOption::VALUE_REQUIRED);
        $this->addOption('email', null, InputOption::VALUE_REQUIRED);
        $this->addOption('name', null, InputOption::VALUE_REQUIRED);
        $this->addOption('register_key', null, InputOption::VALUE_REQUIRED, '激活码');
    }

    /**
     * @return IMailer
     */
    protected function createMailer(InputInterface $input)
    {
        $parameterService = $this->getContainer()->get('app.parameter_service');
        $httpClient = $this->getContainer()->get('app.http_client');
        return SendCloudMailerFactory::createMailer($parameterService, $httpClient, 'channel1');
    }

    /**
     * @return array
     */
    protected function getTemplateVars(InputInterface $input)
    {
        return array(
            'name' => $input->getOption('name'),
            'register_key' => $input->getOption('register_key'),
        );
    }

    /**
     * @return string
     */
    protected function getTemplatePath(InputInterface $input)
    {
        return 'WenwenFrontendBundle:EmailTemplate:signup_confirmation.html.twig';
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
