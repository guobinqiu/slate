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
        $this->setDescription('人工重置密码');
        $this->addOption('email', null, InputOption::VALUE_REQUIRED);
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);

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
        $output->writeln('Change password success.');
    }
}
