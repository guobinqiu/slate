<?php

namespace Wenwen\FrontendBundle\ServiceDependency;

use Guzzle\Http\Client;

class HttpClient {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $options = array('timeout' => 10, 'connect_timeout' => 3);

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get($uri, $headers = null, array $options = array())
    {
        return $this->client->get($uri, $headers, array_merge($this->options, $options));
    }

    public function delete($uri, $headers = null, $body = null, array $options = array())
    {
        return $this->client->delete($uri, $headers, $body, array_merge($this->options, $options));
    }

    public function put($uri, $headers = null, $body = null, array $options = array())
    {
        return $this->client->put($uri, $headers, $body, array_merge($this->options, $options));
    }

    public function patch($uri, $headers = null, $body = null, array $options = array())
    {
        return $this->client->patch($uri, $headers, $body, array_merge($this->options, $options));
    }

    public function post($uri, $headers = null, $postBody = null, array $options = array())
    {
        return $this->client->post($uri, $headers, $postBody, array_merge($this->options, $options));
    }

    public function setDefaultOption($keyOrPath, $value)
    {
        return $this->client->setDefaultOption($keyOrPath, $value);
    }
}