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

        $reason = array (
            PointsExchangeType :: TYPE_91WENWEN => AdCategory :: ID_91WENWEN_POINTS,
            PointsExchangeType :: TYPE_AMAZON => AdCategory :: ID_AMAZON,
            PointsExchangeType :: TYPE_ALIPAY => AdCategory :: ID_ALIPAY,
            PointsExchangeType :: TYPE_MOBILE => AdCategory :: ID_MOBILE,
            PointsExchangeType :: TYPE_FLOW => AdCategory :: ID_FLOW
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
                $exchanges->setStatus(PointsExchange :: STATUS_OK);
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
                    $points = $exchanges->getTargetPoint();
                }

                if (!$finish_time) {
                    $finish_time = date('Y-m-d H:i:s');
                }

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
        switch ($type) {
            case PointsExchangeType :: TYPE_AMAZON :
                $title = $this->getParameter('exchange_finish_amazon_tilte');
                $content = $this->getParameter('exchange_finish_amazon_content');
                break;
            case PointsExchangeType :: TYPE_ALIPAY :
                $title = $this->getParameter('exchange_finish_alipay_tilte');
                $content = $this->getParameter('exchange_finish_alipay_content');
                break;
            case PointsExchangeType :: TYPE_MOBILE :
                $title = $this->getParameter('exchange_finish_mobile_tilte');
                $content = $this->getParameter('exchange_finish_mobile_content');
                break;
            case PointsExchangeType :: TYPE_FLOW :
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
            $this->em->getRepository('JiliApiBundle:SendMessage0' . ($uid % 10))->insertSendMs($parms);
        }
    }

    public function exchangeSendMsFail($type, $uid) {
        $title = '';
        $content = '';
        switch ($type) {
            case PointsExchangeType :: TYPE_AMAZON :
                $title = $this->getParameter('exchange_fail_amazon_tilte');
                $content = $this->getParameter('exchange_fail_amazon_content');
                break;
            case PointsExchangeType :: TYPE_ALIPAY :
                $title = $this->getParameter('exchange_fail_alipay_tilte');
                $content = $this->getParameter('exchange_fail_alipay_content');
                break;
            case PointsExchangeType :: TYPE_MOBILE :
                $title = $this->getParameter('exchange_fail_mobile_tilte');
                $content = $this->getParameter('exchange_fail_mobile_content');
                break;
            case PointsExchangeType :: TYPE_FLOW :
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
            $this->em->getRepository('JiliApiBundle:SendMessage0' . ($uid % 10))->insertSendMs($parms);
        }
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