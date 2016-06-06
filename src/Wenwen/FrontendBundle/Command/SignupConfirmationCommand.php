<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SignupConfirmationCommand extends ContainerAwareCommand
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subject = $input->getOption('subject');
        $email = $input->getOption('email');
        $name = $input->getOption('name');

        $mailer = $this->getContainer()->get('app.send_cloud_mail_service');
        $logger = $this->getContainer()->get('logger');

        $result = $mailer->sendSignupConfirmation($email, $subject, array('name' => $name));

        if (!$result['result']) {
            $logger->error($this->stringify($result, $email));
        } else {
            $logger->info($this->stringify($result, $email));
        }
    }

    private function stringify($result, $email) {
        $result['email'] = $email;
        $result['who'] = $this->getName();
        return json_encode($result);
    }
}
