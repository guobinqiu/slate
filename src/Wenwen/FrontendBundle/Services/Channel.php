<?php

namespace Wenwen\FrontendBundle\Services;

use Guzzle\Http\Exception\RequestException;

class Channel {
    /**
     * @var string
     */
    private $apiUser;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $from;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct($apiUser, $apiKey, $url, $from, HttpClient $httpClient)
    {
        $this->apiUser = $apiUser;
        $this->apiKey = $apiKey;
        $this->url = $url;
        $this->from = $from;
        $this->httpClient = $httpClient;
    }

    /**
     * @link http://sendcloud.sohu.com/doc/email_v2/send_email/
     * @throws RequestException
     *
     * {"result":true,"statusCode":200,"message":"请求成功","info":{"emailIdList":["1464924579544_27949_26113_3130.sc-10_10_127_119-inbound0$qracle@126.com"]}}
     * {"result":false,"statusCode":40005,"message":"认证失败","info":{}}
     */
    public function send($to, $subject, $html) {
        $request = $this->httpClient->post($this->url);
        $request->addPostFields(array(
            'apiUser' => $this->apiUser,
            'apiKey' => $this->apiKey,
            'from' => $this->from,
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
        ));
        $response = $request->send();
        return json_decode($response->getBody(), true);
    }
}