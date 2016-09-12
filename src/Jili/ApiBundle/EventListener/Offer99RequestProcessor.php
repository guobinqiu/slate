<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\Offer99Order;
use Jili\ApiBundle\Util\String;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

/**
 *
 **/
class Offer99RequestProcessor
{
    private $em;
    private $logger;

    public function __construct(LoggerInterface $logger, EntityManager $em ) {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function process(Request $request, array $config)
    {
        $task_name = $config['name'];
        $task_type = $config['task_type'];

        $this->logger->debug('{jaord}' . __FILE__ . ':' . __LINE__ . var_export($request->query, true));

        $tid = $request->query->get('tid');
        $user_id = $request->query->get('uid');
        $point = $request->query->get('vcpoints');
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

        // Create new object of point_history0x
        $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ( $user_id % 10);
        $pointHistory = new $classPointHistory();
        $pointHistory->setUserId($user_id);
        $pointHistory->setPointChangeNum($point);
        $pointHistory->setReason(CategoryType::OFFER99_COST);

        $vote_time = date_create();
        // Create new object of task_history0x
        $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ( $user_id % 10);
        $taskHistory = new $classTaskHistory();
        $taskHistory->setUserid($user_id);
        $taskHistory->setOrderId($order->getId());
        $taskHistory->setOcdCreatedDate($happen_time);
        $taskHistory->setCategoryType(CategoryType::OFFER99_COST);
        $taskHistory->setTaskType(TaskType::CPA);
        $taskHistory->setRewardPercent(0);
        $taskHistory->setTaskName($offer_name);
        $taskHistory->setDate($happen_time);
        $taskHistory->setPoint($point);
        $taskHistory->setStatus(1);
        $db_connection = $em->getConnection();
        $db_connection->beginTransaction();

        // update user.point更新user表总分数
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $user->setPoints(intval($user->getPoints()) + intval($point));
        $user->setLastGetPointsAt(new \DateTime());

        try {
            $em->persist($user);
            $em->persist($pointHistory);
            $em->persist($taskHistory);
            $em->flush();

            $db_connection->commit();
        } catch (\Exception $e) {
            $db_connection->rollback();
            $this->get('logger')->error(__METHOD__ . 'Offer99 Request reward failed: ' . $e->getMessage());
        }

    }

}
