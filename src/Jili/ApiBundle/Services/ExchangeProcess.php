<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBagInterface;

use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\PointsExchangeType;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;

class ExchangeProcess {
    private $em;

    public function exchangeOK($exchange_id, $points, $finish_time, $type, $log_path) {
        $em = $this->em;

        $adCatogory = new AdCategory();
        $pointsExchangeType = new PointsExchangeType();
        $pointsExchange = new PointsExchange();
        $reason = array (
            $pointsExchangeType :: TYPE_91WENWEN => $adCatogory :: ID_91WENWEN_POINTS,
            $pointsExchangeType :: TYPE_AMAZON => $adCatogory :: ID_AMAZON,
            $pointsExchangeType :: TYPE_ALIPAY => $adCatogory :: ID_ALIPAY,
            $pointsExchangeType :: TYPE_MOBILE => $adCatogory :: ID_MOBILE,
            $pointsExchangeType :: TYPE_FLOW => $adCatogory :: ID_FLOW
        );

        $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);

        if (!$points) {
            $points = $exchanges->getTargetPoint();
        }

        if (!$finish_time) {
            $finish_time = date('Y-m-d H:i:s');
        }

        if (!$exchanges->getStatus()) {

            //transaction
            try {
                $em->getConnection()->beginTransaction();

                $user_id = $exchanges->getUserId();
                $pointHistory = 'Jili\ApiBundle\Entity\PointHistory0' . ($user_id % 10);
                $po = new $pointHistory ();
                $po->setUserId($user_id);
                $po->setPointChangeNum('-' . $points);
                $po->setReason($reason[$type]);
                $em->persist($po);
                $em->flush();
                $exchanges->setStatus($pointsExchange :: STATUS_OK);
                $exchanges->setFinishDate(date_create($finish_time));
                $em->persist($exchanges);
                $em->flush();

                $this->exchangeSendMs($type, $user_id);

                $em->getConnection()->commit();

            } catch (\ Exception $e) {
                // internal error
                $em->getConnection()->rollback();
                $em->close();

                //写log
                $content = "db操作失败" . $e->getMessage();
                FileUtil :: writeContents($log_path, $content);

                return false;
            }

        }
        return true;
    }

    public function exchangeNg($exchange_id, $points, $finish_time, $type, $log_path) {
        $em = $this->em;

        $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        if (!$exchanges->getStatus()) {

            //transaction
            try {
                $em->getConnection()->beginTransaction();

                $user_id = $exchanges->getUserId();
                if (!$points) {
                    $points = $exchanges->getTargetAccount();
                }

                if (!$finish_time) {
                    $finish_time = date('Y-m-d H:i:s');
                }

                $userInfo = $em->getRepository('JiliApiBundle:User')->find($user_id);
                $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
                $user->setPoints(intval($user->getPoints() + $points));
                $em->persist($user);
                $em->flush();
                $exchanges->setStatus($this->getParameter('init_two'));
                $exchanges->setFinishDate(date_create($finish_time));
                $em->persist($exchanges);
                $em->flush();

                $this->exchangeSendMsFail($type, $user_id);

                $em->getConnection()->commit();

            } catch (\ Exception $e) {
                // internal error
                $em->getConnection()->rollback();
                $em->close();

                //写log
                $content = "db操作失败" . $e->getMessage();
                FileUtil :: writeContents($log_path, $content);

                return false;
            }
        }
        return true;
    }

    public function exchangeSendMs($type, $uid) {
        $title = '';
        $content = '';
        $pointsExchangeType = new PointsExchangeType();
        switch ($type) {
            case $pointsExchangeType :: TYPE_AMAZON :
                $title = $this->getParameter('exchange_finish_amazon_tilte');
                $content = $this->getParameter('exchange_finish_amazon_content');
                break;
            case $pointsExchangeType :: TYPE_ALIPAY :
                $title = $this->getParameter('exchange_finish_alipay_tilte');
                $content = $this->getParameter('exchange_finish_alipay_content');
                break;
            case $pointsExchangeType :: TYPE_MOBILE :
                $title = $this->getParameter('exchange_finish_mobile_tilte');
                $content = $this->getParameter('exchange_finish_mobile_content');
                break;
            case $pointsExchangeType :: TYPE_FLOW :
                $title = $this->getParameter('exchange_finish_flow_tilte');
                $content = $this->getParameter('exchange_finish_flow_content');
                break;
            default :
                break;
        }
        if ($title && $content) {
            $parms = array (
                'userid' => $uid,
                'title' => $title,
                'content' => $content
            );
            $this->insertSendMs($parms);
        }
    }

    public function exchangeSendMsFail($type, $uid) {
        $title = '';
        $content = '';
        $pointsExchangeType = new PointsExchangeType();
        switch ($type) {
            case $pointsExchangeType :: TYPE_AMAZON :
                $title = $this->getParameter('exchange_fail_amazon_tilte');
                $content = $this->getParameter('exchange_fail_amazon_content');
                break;
            case $pointsExchangeType :: TYPE_ALIPAY :
                $title = $this->getParameter('exchange_fail_alipay_tilte');
                $content = $this->getParameter('exchange_fail_alipay_content');
                break;
            case $pointsExchangeType :: TYPE_MOBILE :
                $title = $this->getParameter('exchange_fail_mobile_tilte');
                $content = $this->getParameter('exchange_fail_mobile_content');
                break;
            case $pointsExchangeType :: TYPE_FLOW :
                $title = $this->getParameter('exchange_fail_flow_tilte');
                $content = $this->getParameter('exchange_fail_flow_content');
                break;
            default :
                break;
        }
        if ($title && $content) {
            $parms = array (
                'userid' => $uid,
                'title' => $title,
                'content' => $content
            );
            $this->insertSendMs($parms);
        }
    }

    public function insertSendMs($parms = array ()) {
        extract($parms);
        $em = $this->em;
        $sm = SequenseEntityClassFactory :: createInstance('SendMessage', $userid);
        $sm->setSendFrom($this->getParameter('init'));
        $sm->setSendTo($userid);
        $sm->setTitle($title);
        $sm->setContent($content);
        $sm->setReadFlag($this->getParameter('init'));
        $sm->setDeleteFlag($this->getParameter('init'));
        $em->persist($sm);
        $em->flush();
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function getParameter($key) {
        return $this->container->getParameter($key);
    }

    public function setContainer($container) {
        $this->container = $container;
        return $this;
    }
}