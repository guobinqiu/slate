<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Templating\EngineInterface;
use Wenwen\AppBundle\Entity\SsiRespondent;

class SendCloudMailService
{
    private $httpClient;

    private $templating;

    private $parameterReader;

    private $logger;

    private $em;

    public function __construct(HttpClient $httpClient,
                                EngineInterface $templating,
                                ParameterReader $parameterReader,
                                LoggerInterface $logger,
                                EntityManager $em)
    {
        $this->httpClient = $httpClient;
        $this->templating = $templating;
        $this->parameterReader = $parameterReader;
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * 发送注册激活邮件
     *
     * @return array 失败或成功信息
     */
    public function sendSignupConfirmation($to, $subject, array $templateVars) {
        $templatePath = 'AppBundle:EmailTemplate:signup_confirmation.html.twig';
        $html = $this->templating->render($templatePath, $templateVars);
        return $this->createChannel()->send($to, $subject, $html);
    }

    /**
     * 发送注册成功邮件
     *
     * @return array 失败或成功信息
     */
    public function sendSignupSuccess($to, $subject, array $templateVars) {
        $templatePath = 'AppBundle:EmailTemplate:signup_success.html.twig';
        $html = $this->templating->render($templatePath, $templateVars);
        return $this->createChannel()->send($to, $subject, $html);
    }

    /**
     * SSI新问卷通知
     *
     * @return array
     */
    public function sendSSIDeliveryNotification(array $respondentIds) {

        $total = count($respondentIds);
        $errors = 0;

        $cp = $this->createChannelPool();

        foreach ($respondentIds as $respondentId) {
            $ssiRespondentId = SsiRespondent::parseRespondentId($respondentId);
            $recipient = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->retrieveRecipientDataToSendMailById($ssiRespondentId);

            if ($recipient) {

                $subject = '亲爱的'.$recipient['name1'].'，您的新问卷来了！';

                $templateVars = array(
                    'name1' => $recipient['name1'],
                    'survey_title' => $this->parameterReader->getParameter('ssi_project_survey')['title'],
                    'survey_point' => $this->parameterReader->getParameter('ssi_project_survey')['point'],
                );

                $templatePath = 'AppBundle:EmailTemplate:ssi_delivery_notification.html.twig';

                $html = $this->templating->render($templatePath, $templateVars);

                $result = $cp->send($recipient['email'], $subject, $html);

                if (!$result['result']) {
                    $this->logger->error(json_encode($result));

                    $errors++;
                }
            }
        }

        return array('errors' => $errors, 'total' => $total, 'success' => round(($total - $errors) / $total * 100, 2) . '%');
    }

    /**
     * Fulcrum新问卷通知
     *
     * @return array
     */
    public function sendFulcrumDeliveryNotification(array $respondents) {
        return $this->sendDeliveryNotification($respondents, 'AppBundle:EmailTemplate:fulcrum_delivery_notification.html.twig');
    }

    /**
     * SOP新问卷通知
     *
     * @return array
     */
    public function sendSOPDeliveryNotification(array $respondents) {
        return $this->sendDeliveryNotification($respondents, 'AppBundle:EmailTemplate:sop_delivery_notification.html.twig');
    }

    private function sendDeliveryNotification(array $respondents, $templatePath) {

        $total = count($respondents);
        $errors = 0;

        $cp = $this->createChannelPool();

        foreach ($respondents as $respondent) {
            $recipient = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($respondent['app_mid']);

            if ($recipient) {

                $subject = '亲爱的'.$recipient['name1'].'，您的新问卷来了！';

                $templateVars = array(
                    'name1' => $recipient['name1'],
                    'survey_title' => $respondent['title'],
                    'survey_point' => $respondent['extra_info']['point']['complete'],
                    'survey_length' => $respondent['loi']
                );

                $html = $this->templating->render($templatePath, $templateVars);

                $result = $cp->send($recipient['email'], $subject, $html);

                if (!$result['result']) {
                    $this->logger->error(json_encode($result));

                    $errors++;
                }
            }
        }

        return array('errors' => $errors, 'total' => $total, 'success' => round(($total - $errors) / $total * 100, 2) . '%');
    }

    private function createChannel() {

        $url = $this->parameterReader->getParameter('mail')['sendcloud']['url'];
        $accounts = $this->parameterReader->getParameter('mail')['sendcloud']['trigger_mode'];

        $ch = new Channel($accounts[0]['api_user'], $accounts[0]['api_key'], $url, $accounts[0]['from'], $this->httpClient);

        return $ch;
    }

    private function createChannelPool() {

        $url = $this->parameterReader->getParameter('mail')['sendcloud']['url'];
        $accounts = $this->parameterReader->getParameter('mail')['sendcloud']['batch_mode'];

        $cp = new ChannelPool(new Channel($accounts[0]['api_user'], $accounts[0]['api_key'], $url, $accounts[0]['from'], $this->httpClient));
        $cp->addChannel(new Channel($accounts[1]['api_user'], $accounts[1]['api_key'], $url, $accounts[1]['from'], $this->httpClient));

        return $cp;
    }

}