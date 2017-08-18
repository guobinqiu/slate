<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Exceptions\NoDataFoundException;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;

class SopDeliveryNotificationBatchMailCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this->setName('mail:sop_delivery_notification_batch');
        $this->setDescription('批量通知会员有新的SOP问卷');
        $this->addOption('respondents', null, InputOption::VALUE_REQUIRED, 'https://console.partners.surveyon.com/docs/v1_1/research_survey_delivery_notification');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer 0: success, 1: failed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templating = $this->getContainer()->get('templating');
        $logger = $this->getContainer()->get('monolog.logger.email_delivery');
        $parameterService = $this->getContainer()->get('app.parameter_service');
        $mailer = MailerFactory::createWebpowerMailer($parameterService);

        $exitCode = 0;

        // check external passed in argument
        $respondents = json_decode($input->getOption('respondents'), true);
        if (!is_array($respondents)) {
            $message = __METHOD__ . " Invalid option value. respondents=" . $input->getOption('respondents');
            $logger->error($message);
            $output->write($message);
            $exitCode = 1;
            return $exitCode;
        }

        $logger->info(__METHOD__ . ' START SEND Email. Total count:' . count($respondents));

        $countSent = 0;
        $countNoEmail = 0;
        $countUnsubscribe = 0;

        for ($i = 0; $i < count($respondents); $i++) {
            try {
                $respondent = $respondents[$i];
                $recipient = $this->getRecipient($respondent['app_mid']);
                if ($recipient['email']) {
                    if ($this->isSubscribed($recipient['email'])) {
                        $respondent['recipient'] = $recipient;
                        $toAddress = $respondent['recipient']['email'];
                        $name1 = $respondent['recipient']['name1'];
                        if ($name1 == null) {
                            $name1 = $toAddress;
                        }
                        $surveyTitle = $respondent['title'];
                        $completePoint = $respondent['extra_info']['point']['complete'];
                        $loi = $respondent['loi'];
                        $surveyId = $respondent['survey_id'];
                        $subject = '亲爱的' . $name1 . '，为您呈上一份价值' . $completePoint . '分的新问卷（编号：r' . $surveyId . '）';
                        $templatePath = 'WenwenFrontendBundle:EmailTemplate:sop_delivery_notification.html.twig';
                        $html = $templating->render($templatePath, array(
                            'name1' => $name1,
                            'survey_title' => $surveyTitle,
                            'survey_point' => $completePoint,
                            'survey_length' => $loi,
                        ));
                        $mailer->send($toAddress, $subject, $html);
                        $countSent++;
                        $logger->info(__METHOD__ . ' Email sent. ' . json_encode($recipient));
                    } else {
                        $countUnsubscribe++;
                        $message = __METHOD__ . ' Email unsbuscribed. ' . json_encode($recipient);
                        $logger->info($message);
                        $output->write($message);
                    }
                } else {
                    $countNoEmail++;
                    $message = __METHOD__ . ' User registered without email ' . json_encode($recipient);
                    $logger->info($message);
                    $output->write($message);
                }
            } catch(\Exception $e) {
                $logger->error(__METHOD__ . ' ' . $e->getMessage());
                $output->write($e->getMessage());
                $exitCode = 1;
            }
        }
        $logger->info(__METHOD__ . ' END   SEND Email. Total sent=' . $countSent . ' Count of no email=' . $countNoEmail . ' Count of unsubscribe=' . $countUnsubscribe);
        return $exitCode;
    }

    private function getRecipient($appMid) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $recipient = $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($appMid);
        if (!$recipient) {
            throw new NoDataFoundException("No user found with app_mid: " . $appMid);
        }
        return $recipient;
    }

    private function isSubscribed($email) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userEdmUnsubscribes = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->findByEmail($email);
        return count($userEdmUnsubscribes) == 0;
    }
}