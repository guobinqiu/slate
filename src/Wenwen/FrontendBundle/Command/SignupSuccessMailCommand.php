<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Services\IMailer;
use Wenwen\FrontendBundle\Services\SendCloudMailer;

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
        $mailer = $this->getContainer()->getParameter('mailer');
        $sendcloud = $mailer['sendcloud'];
        $account = $sendcloud['channel1'];

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
            'name' => $input->getOption('name'),
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
