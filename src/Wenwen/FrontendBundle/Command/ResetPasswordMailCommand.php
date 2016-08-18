<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\IMailer;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;

class ResetPasswordMailCommand extends AbstractMailCommand
{
    protected function configure()
    {
        $this->setName('mail:reset_password');
        $this->setDescription('发送含重置密码token的邮件');
        $this->addOption('subject', null, InputOption::VALUE_REQUIRED);
        $this->addOption('email', null, InputOption::VALUE_REQUIRED);
        $this->addOption('name', null, InputOption::VALUE_REQUIRED);
        $this->addOption('reset_password_token', null, InputOption::VALUE_REQUIRED);
    }

    /**
     * @return IMailer
     */
    protected function createMailer(InputInterface $input)
    {
        // sendcloud
//        $parameterService = $this->getContainer()->get('app.parameter_service');
//        $httpClient = $this->getContainer()->get('app.http_client');
//        return MailerFactory::createSendCloudMailer($parameterService, $httpClient, 'channel1');

        // webpower
        $parameterService = $this->getContainer()->get('app.parameter_service');
        return MailerFactory::createWebpowerSignupMailer($parameterService);
    }

    /**
     * @return array
     */
    protected function getTemplateVars(InputInterface $input)
    {
        return array(
            'name' => $input->getOption('name'),
            'reset_password_token' => $input->getOption('reset_password_token'),
        );
    }

    /**
     * @return string
     */
    protected function getTemplatePath(InputInterface $input)
    {
        return 'WenwenFrontendBundle:EmailTemplate:reset_password.html.twig';
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
