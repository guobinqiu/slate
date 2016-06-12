<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Services\IMailer;

abstract class AbstractMailCommand extends ContainerAwareCommand {

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templating = $this->getContainer()->get('templating');

        $html = $templating->render($this->getTemplatePath(), $this->getTemplateVars($input));
        $result = $this->createMailer()->send($this->getEmail($input), $this->getSubject($input), $html);

        $message = $this->stringify($result);

        $logger = $this->getContainer()->get('logger');

        if (!$result['result']) {
            $logger->error($message);
        } else {
            $logger->info($message);
        }

        $output->write($message);
    }

    protected function stringify($result) {
        $result['who'] = $this->getName();
        return json_encode($result);
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