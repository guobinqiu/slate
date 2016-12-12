<?php

namespace Wenwen\FrontendBundle\Services;

use JMS\Serializer\Serializer;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class LatestNewsService
{
    private $redis;
    private $serializer;
    private $parameterService;

    /**
     * @param Client $redis
     * @param Serializer $serializer
     * @param ParameterService $parameterService
     * @param LoggerInterface $logger
     */
    public function __construct(Client $redis,
                                Serializer $serializer,
                                ParameterService $parameterService,
                                LoggerInterface $logger
    ) {
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->parameterService = $parameterService;
        $this->logger = $logger;
    }

    /**
     * 插入一条最新动态.
     *
     * @param string $news
     */
    public function insertLatestNews($news, $key = CacheKeys::LATEST_NEWS_LIST)
    {
        $latestNewsList = $this->getLatestNews($key);
        $count = array_unshift($latestNewsList, $news);
        if ($count > 100) {
            array_pop($latestNewsList);
        }
        $this->redis->set($key, $this->serializer->serialize($latestNewsList, 'json'));
    }

    /**
     * 显示最新动态.
     *
     * @return array
     */
    public function getLatestNews($key = CacheKeys::LATEST_NEWS_LIST)
    {
        $val = $this->redis->get($key);
        if (is_null($val)) {
            return array();
        }
        return $this->serializer->deserialize($val, 'array', 'json');
    }

    public function buildNews(User $user, $points, $categoryType, $taskType) {
        $message = date('Y-m-d') . ' ' . mb_substr($user->getNick(), 0, 3, 'utf8') . '**';
        switch($taskType) {
            case TaskType::CPA:
                $message .= '任务墙';
                break;
            case TaskType::CPS:
                $message .= '购物返利';
                break;
            case TaskType::SURVEY:
                $message .= '商业问卷';
                break;
            case TaskType::RENTENTION:
                switch($categoryType) {
                    case CategoryType::SOP_EXPENSE:
                    case CategoryType::SSI_EXPENSE:
                    case CategoryType::CINT_EXPENSE:
                    case CategoryType::FULCRUM_EXPENSE:
                        $message .= '属性问卷';
                        break;
                    case CategoryType::SIGNUP:
                        $message .= '完成注册';
                        break;
                    case CategoryType::QUICK_POLL:
                        $message .= '快速问答';
                        break;
                    case CategoryType::EVENT_INVITE_SIGNUP:
                        $message .= '邀请好友';
                        break;
                    case CategoryType::EVENT_INVITE_SURVEY:
                        $message .= '好友答问卷';
                        break;
                    case CategoryType::EVENT_PRIZE:
                        $message .= '抽奖';
                }
        }
        $message .= '获得' . $points . '积分';
        return $message;
    }
}