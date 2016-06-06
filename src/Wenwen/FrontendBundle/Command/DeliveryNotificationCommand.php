<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeliveryNotificationCommand extends ContainerAwareCommand {
    const SSI = 'ssi';
    const SOP = 'sop';
    const FULCRUM = 'fulcrum';

    protected function configure()
    {
        $this->setName('mail:delivery_notification');
        $this->setDescription('通知会员有新到问卷');
        $this->addArgument('respondents', null, InputArgument::REQUIRED, '要传序列化过的数据');
        $this->addOption('survey', null, InputOption::VALUE_REQUIRED, 'ssi|sop|fulcrum');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $respondents = unserialize($input->getArgument('respondents'));
        $survey = $input->getOption('survey');
        //$mailer = $this->getContainer()->get('app.send_cloud_mail_service');
        $logger = $this->getContainer()->get('logger');

        $result = null;

        //$result = $mailer->sendSOPDeliveryNotification($respondents);
//
//        switch($survey)
//        {
//            case self::SSI:
//                $result = $mailer->sendSSIDeliveryNotification($respondents);
//                break;
//            case self::SOP:
//                $result = $mailer->sendSOPDeliveryNotification($respondents);
//                break;
//            case self::FULCRUM:
//                $result = $mailer->sendFulcrumDeliveryNotification($respondents);
//                break;
//        }

        $logger->info(json_encode($result));
        $output->write($result);
    }
}