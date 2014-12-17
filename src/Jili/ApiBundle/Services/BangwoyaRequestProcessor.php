<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\BangwoyaOrder;
use Jili\ApiBundle\Util\String;

/**
 *
 **/
class BangwoyaRequestProcessor {

    /**
    * @var array
    */
    protected $configs;
    /**
    * @var \Doctrine\ORM\EntityManager
    */
    protected $em;
    /**
    * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
    */
    protected $logger;

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($tid, $partnerid, $vmoney) {

        $configs = $this->configs;

        $category_type = $configs['category_type'];
        $task_name = $configs['name'];
        $task_type = $configs['task_type'];

        $em = $this->em;

        //transaction[TODO] 失败了如何处理

        try {
            $em->getConnection()->beginTransaction();

            $order = $em->getRepository('JiliApiBundle:BangwoyaOrder')->findOneByTid($tid);
            if (is_null($order)) {
                $is_new = true;
                // insert bangwoya order
                $order = new BangwoyaOrder();
                $order->setUserid($partnerid);
                $order->setTid($tid);
                $order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
                $order->setDeleteFlag(0);
                $em->persist($order);
            }

            // insert task_history
            $em->getRepository('JiliApiBundle:TaskHistory00')->init(array (
                'userid' => $partnerid,
                'orderId' => $order->getId(),
                'taskType' => $task_type,
                'categoryType' => $category_type,
                'reward_percent' => 0,
                'task_name' => $task_name,
                'point' => $vmoney,
                'date' => new \ Datetime(),
                'status' => 1
            ));

            // insert point_history
            $em->getRepository('JiliApiBundle:PointHistory00')->get(array (
                'userid' => $partnerid,
                'point' => $vmoney,
                'type' => $category_type
            ));

            // update user.point更新user表总分数
            $user = $em->getRepository('JiliApiBundle:User')->find($partnerid);
            $oldPoint = $user->getPoints();
            $user->setPoints(intval($oldPoint + $vmoney));
            //$em->persist($user);

            $em->flush();
            $em->getConnection()->commit();
            $em->clear();

            return $order->getId();

        } catch (\ Exception $e) {
            // internal error[todo]
            echo $e->getMessage();
            $em->getConnection()->rollback();

            return false;
        }
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }
}