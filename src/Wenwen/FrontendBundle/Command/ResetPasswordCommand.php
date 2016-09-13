<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResetPasswordCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('user:reset_password');
        $this->setDescription('Reset password by email');
        $this->addOption('email', null, InputOption::VALUE_REQUIRED);
        $this->addOption('password', 'p', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        if ($password == null) {
            $password = $this->random_password();
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneByEmail($email);

        if ($user == null) {
            $output->writeln('User does not exist.');
            return;
        }

        if (strlen($password) < 6 || strlen($password) > 32) {
            $output->writeln('Password length should goes from 6 to 32.');
            return;
        }

        $user->setPwd($password);
        $em->flush();
        $em->clear();
        $output->writeln('Change password success. Your password is: ' . $password);
    }

    private function random_password($chars = 8) {
        $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        return substr(str_shuffle($letters), 0, $chars);
    }
}
