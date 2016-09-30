<?php

namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\Offer99Order;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Services\UserService;

class Offer99RequestProcessor
{
    private $em;
    private $logger;
    private $userService;

    public function __construct(LoggerInterface $logger, EntityManager $em, UserService $userService) {
        $this->logger = $logger;
        $this->em = $em;
        $this->userService = $userService;
    }

    public function process(Request $request, array $config)
    {
        $task_name = $config['name'];

        $this->logger->debug('{jaord}' . __FILE__ . ':' . __LINE__ . var_export($request->query, true));

        $tid = $request->query->get('tid');
        $user_id = $request->query->get('uid');
        $points = $request->query->get('vcpoints');
        // 20160716 暂时懒得重构这块的代码，先把需要的数据补齐
        // 记录offer99那边回传的任务名称，因为现在没有检查这个任务名称的参数是否存在，万一没有的话用固定名称
        $offer_name = $request->query->get('offer_name', $task_name);
        $happen_time = date_create();

        $em = $this->em;

        // init log.
        $this->logger->debug('{jaord}' . __FILE__ . ':' . __LINE__ . ':HANGUP_SUSPEND');

        $order = $em->getRepository('JiliApiBundle:Offer99Order')->findOneByTid($tid);
        if (is_null($order)) {
            $is_new = true;
            // init offerorder & task history
            $order = new Offer99Order();
            // update offerorder
            $order->setUserid($user_id); // order
            $order->setTid($tid); // order
            $order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
            $order->setDeleteFlag(0);
            $em->persist($order);
            $em->flush();
        }

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $this->userService->addPoints($user, $points, CategoryType::OFFER99_COST, TaskType::CPA, $offer_name, $happen_time);
    }
}
