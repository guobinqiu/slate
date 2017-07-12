<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\ServiceDependency\Mailer\MailerFactory;

class SopDeliveryNotificationBatchMailCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this->setName('mail:sop_delivery_notification_batch');
        $this->setDescription('批量通知会员有新的SOP问卷');
        $this->addOption('respondents', null, InputOption::VALUE_REQUIRED, 'https://console.partners.surveyon.com/docs/v1_1/research_survey_delivery_notification');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templating = $this->getContainer()->get('templating');
        $logger = $this->getContainer()->get('monolog.logger.email_delivery');

        $parameterService = $this->getContainer()->get('app.parameter_service');
        $mailer = MailerFactory::createWebpowerMailer($parameterService);

        $respondents = json_decode($input->getOption('respondents'), true);
//        var_dump($respondents);

        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $recipient = $this->getRecipient($respondent['app_mid']);
            if ($recipient['email']) {
                if ($this->isSubscribed($recipient['email'])) {
                    $respondent['recipient'] = $recipient;

                    $result = $this->sendEmail($respondent, $templating, $mailer);

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
            }
        }
    }

    private function sendEmail($respondent, $templating, $mailer) {
        $name1 = $respondent['recipient']['name1'];
        if ($name1 == null) {
            $name1 = $respondent['recipient']['email'];
        }
        $toAddress = $respondent['recipient']['email'];
        $surveyTitle = $respondent['title'];
        $completePoint = $respondent['extra_info']['point']['complete'];
        $loi = $respondent['loi'];
        $surveyId = $respondent['survey_id'];
        $subject = '亲爱的' . $name1 . '，为您呈上一份价值' . $completePoint . '分的新问卷（编号：r' . $surveyId . '）';

        $html = $templating->render('WenwenFrontendBundle:EmailTemplate:sop_delivery_notification.html.twig', array(
            'name1' => $name1,
            'survey_title' => $surveyTitle,
            'survey_point' => $completePoint,
            'survey_length' => $loi,
        ));
        return $mailer->send($toAddress, $subject, $html);
    }

    private function getRecipient($app_mid) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        return $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($app_mid);
    }

    private function isSubscribed($email) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userEdmUnsubscribes = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->findByEmail($email);
        return count($userEdmUnsubscribes) == 0;
    }
}