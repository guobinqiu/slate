<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\IMailer;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\SendCloudMailerFactory;

class SignupSuccessMailCommand extends AbstractMailCommand
{
    protected function configure()
    {
        $this->setName('mail:signup_success');
        $this->setDescription('发送注册成功邮件');
        $this->addOption('subject', null, InputOption::VALUE_REQUIRED);
        $this->addOption('email', null, InputOption::VALUE_REQUIRED);
        $this->addOption('name', null, InputOption::VALUE_REQUIRED);
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
        );
    }

    /**
     * @return string
     */
    protected function getTemplatePath(InputInterface $input)
    {
        return 'WenwenFrontendBundle:EmailTemplate:signup_success.html.twig';
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
