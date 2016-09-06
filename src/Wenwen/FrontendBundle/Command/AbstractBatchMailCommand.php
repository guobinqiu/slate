<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\IMailer;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;

abstract class AbstractBatchMailCommand extends ContainerAwareCommand {

    protected $parameterService;
    protected $logger;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uniqid = '[' . $this->getName() . ':uniqid:' . uniqid(). '] ';
        $output->write($uniqid . 'Start ' . PHP_EOL);

        $this->logger = $this->getContainer()->get('monolog.logger.email_delivery');
        $this->parameterService = $this->getContainer()->get('app.parameter_service');

        $this->logger->info($uniqid . 'Start ');
        try{
            $emailParams = $this->getEmailParams($input);
        } catch (\Exception $e){
            $rtnCode = 1;
            $this->logger->error($e->getMessage());
            $output->write($e->getMessage() . PHP_EOL); 
            return $rtnCode;
        }

        $rtnCode = 0;
        foreach($emailParams as $emailParam){
            try{
                $mailer = MailerFactory::createWebpowerMailer($this->parameterService);
                $result = $mailer->send($emailParam['email'], $emailParam['subject'], $emailParam['content']);
                $message = $uniqid . 'sended ' . json_encode($result);
                if (!$result['result']) {
                    $this->logger->error($message);
                    $output->write($message . PHP_EOL); 
                } else {
                    $this->logger->info($message);
                    $output->write($message . PHP_EOL); 
                }
            } catch(\Exception $e){
                $rtnCode = 1;
                $this->logger->error($e->getMessage());
                $output->write($e->getMessage() . PHP_EOL); 
            }
        }

        $this->logger->info($uniqid . 'End ');
        $output->write($uniqid . 'End ' . PHP_EOL);
        return $rtnCode;
    }

    /*
    * Provide params for email sending (email address, subject, content)
    */
    abstract protected function getEmailParams(InputInterface $input);
}