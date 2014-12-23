<?php
namespace Jili\ApiBundle\Services\Bangwoya;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Utility\FileUtil;

/**
 *
 **/
class BangwoyaRequestProcessor {

    private $em;
    private $configs;
    private $logger;
    private $send_mail;

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($tid, $partnerid, $vmoney) {
        $configs = $this->configs;

        $category_type = $configs['category_type'];
        $task_name = $configs['name'];
        $task_type = $configs['task_type'];

        $em = $this->em;

        //transaction
        try {
            $em->getConnection()->beginTransaction();

            // insert bangwoya order
            $order = $em->getRepository('JiliApiBundle:BangwoyaOrder')->insert(array (
                'userId' => $partnerid,
                'tid' => $tid
            ));

            // insert task_history
            $em->getRepository('JiliApiBundle:TaskHistory00')->init(array (
                'userid' => $partnerid,
                'orderId' => $order->getId(),
                'taskType' => $task_type,
                'categoryType' => $category_type,
                'reward_percent' => 0,
                'task_name' => $task_name,
                'point' => $vmoney,
                'date' => new \Datetime(),
                'status' => 1
            ));

            // insert point_history
            $em->getRepository('JiliApiBundle:PointHistory00')->get(array (
                'userid' => $partnerid,
                'point' => $vmoney,
                'type' => $category_type
            ));

            // update user point更新user表总分数
            $user = $em->getRepository('JiliApiBundle:User')->find($partnerid);
            $oldPoint = $user->getPoints();
            $user->setPoints(intval($oldPoint + $vmoney));
            $em->persist($user);
            $em->flush();

            $em->getConnection()->commit();

            return $order->getId();

        } catch (\Exception $e) {
            // internal error
            $em->getConnection()->rollback();
            $em->close();

            // 出错处理
            $content = "user_id:" . $partnerid . " tid:" . $tid . " vmoney:" . $vmoney . "<br><br>" . $e->getMessage();
            $this->rollbackHandle($configs, $content);

            return false;
        }
    }

    public function rollbackHandle($configs, $content) {
        //send email
        $content = $configs['mail_subject'] . "<br><br>" . $content;
        $alertTo = $configs['alertTo_contacts'];
        $this->send_mail->sendMails($configs['mail_subject'], $alertTo, $content);

        //write log
        $file_name = $configs['bangwoya_api_log'];
        FileUtil :: writeContents($file_name, $content);
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

    public function setSendMail($send_mail)
    {
        $this->send_mail = $send_mail;
    }
}