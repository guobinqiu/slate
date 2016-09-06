<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\IMailer;

abstract class AbstractMailCommand extends ContainerAwareCommand {

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templating = $this->getContainer()->get('templating');
        $logger = $this->getContainer()->get('monolog.logger.email_delivery');

        $mailer = $this->createMailer($input);
        $html = $templating->render($this->getTemplatePath($input), $this->getTemplateVars($input));
        $result = $mailer->send($this->getEmail($input), $this->getSubject($input), $html);

        // Extra info
        $result['mailer'] = $mailer->getName();
        $result['command'] = $this->getName();

        $message = json_encode($result);
        if (!$result['result']) {
            $logger->error($message);
        } else {
            $logger->info($message);
        }
        $output->write($message . PHP_EOL); // also print to console
    }

    /**
     * @return IMailer
     */
    abstract protected function createMailer(InputInterface $input);

    /**
     * @return array
     */
    abstract protected function getTemplateVars(InputInterface $input);

    /**
     * @return string
     */
    abstract protected function getTemplatePath(InputInterface $input);

    /**
     * @return string
     */
    abstract protected function getEmail(InputInterface $input);

    /**
     * @return string
     */
    abstract protected function getSubject(InputInterface $input);
}