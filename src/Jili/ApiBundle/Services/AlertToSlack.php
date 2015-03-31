<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Jili\ApiBundle\Utility\CurlUtil;

class AlertToSlack {

    public function sendAlertToSlack($content) {
        $url = $this->getParameter('slack_alert_url');
        $data['channel'] = $this->getParameter('slack_alert_channel');
        $data['username'] = $this->getParameter('slack_alert_username');
        $text_prefix = $this->getParameter('slack_alert_text_prefix');
        $data['text'] = "@" . $text_prefix . $content;
        $post_data = 'payload=' . json_encode($data);
        try {
            CurlUtil :: curl($url, $post_data);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }

    public function getParameter($key) {
        return $this->container->getParameter($key);
    }

    public function setContainer($container) {
        $this->container = $container;
        return $this;
    }
}