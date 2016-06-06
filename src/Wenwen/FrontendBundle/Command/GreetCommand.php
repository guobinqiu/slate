<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GreetCommand extends ContainerAwareCommand {
    protected function configure()
    {
        $this->setName('demo:greet');
        $this->setDescription('Greet someone');
        $this->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?', 'Guobin');
        $this->addOption('uppercase', 'u', InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters', null);
        //$this->setHelp("php app/console demo:greet --uppercase Guobin\nphp app/console demo:greet -u");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $text = 'Hello ' . $name;

        if($input->getOption('uppercase')) {
            $text = strtoupper($text);
        }

        $output->write($text);
    }
}