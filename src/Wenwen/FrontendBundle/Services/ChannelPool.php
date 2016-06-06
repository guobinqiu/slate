<?php

namespace Wenwen\FrontendBundle\Services;

class ChannelPool {

    /**
     * @var int 当前索引值
     */
    private $index = 0;

    /**
     * @var int 索引上限值
     */
    private $maxIndex = 0;

    /**
     * @var array 多通道
     */
    private $channels;

    public function __construct(Channel $channel)
    {
        $this->channels = array($channel);
    }

    public function addChannel(Channel $channel) {
        $this->channels[] = $channel;
        $this->maxIndex++;
    }

    /**
     * 轮发
     */
    public function send($to, $subject, $html) {
        $result = $this->getCurrentChannel()->send($to, $subject, $html);
        $this->next();
        return $result;
    }

    public function getCurrentChannel() {
        return $this->channels[$this->index];
    }

    private function next() {

        $this->index++;

        if ($this->index > $this->maxIndex) {
            $this->index = 0;
        }
    }
}