<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\BangwoyaOrder;

class BangwoyaOrderRepository extends EntityRepository {

    /**
     * @param array $params = array (
     *      'userId' => 1057622,
     *      'tid' => 17
     *    )
     *
     * @return BangwoyaOrder Instance
     */
    public function insert($params = array ()) {
        $em = $this->getEntityManager();
        $order = new BangwoyaOrder();
        $order->setUserid($params['userId']);
        $order->setTid($params['tid']);
        $order->setDeleteFlag(0);
        $em->persist($order);
        $em->flush();
        return $order;
    }
}