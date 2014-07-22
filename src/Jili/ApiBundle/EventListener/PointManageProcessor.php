<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 **/
class PointManageProcessor
{
    private $em;
    private $logger;
    private $container_;

    private $task_logger;
    private $point_logger;

    public function __construct(LoggerInterface $logger, EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function process($path, $log_path)
    {
        $code = array ();
        //打开上传的文件
        $handle = fopen($path, 'r');
        if (!$handle) {
            //die("指定文件不能打开，操作中断!");
            $arr['code'][] = "can not open upload file.";
            return $arr;
        }

        //打开要写日志的文件
        $log_handle = fopen($log_path, 'w');
        if (!$log_handle) {
            //die("指定文件不能打开，操作中断!");
            $arr['code'][] = "can not open log file.";
            return $arr;
        }

        $i = 0;
        fwrite($log_handle, "user_id,email,point,task_name,category_type,task_type\n");
        while ($data = fgetcsv($handle)) {
            if ($i != 0 && $data) {
                //user_id,email,point,task_name,category_type,task_type
                $return = $this->updatePoint($data);
                if ($return) {
                    $code[] = "[ ".$data['0'] ." ". $data['1'] . " ] " . $return;
                    fwrite($log_handle, implode(",", $data) . "," . $return . "\n");
                } else {
                    fwrite($log_handle, implode(",", $data) . "," . "point import success\n");
                }
            }
            $i++;
        }
        fclose($handle);
        fclose($log_handle);

        if ($code) {
            $code[] = "These users point import fail";
        } else {
            $arr['success'] = "point import success";
        }
        $arr['code'] = $code;
        return $arr;
    }

    //更新point: user, point_history , task_history
    private function updatePoint($data)
    {
        //user_id,email,point,task_name,category_type,task_type
        $user_id = $data[0];
        $email = $data[1];
        $point = $data[2];
        $task_name = $data[3];
        $category_type = $data[4];
        $task_type = $data[5];

        $message = "";

        if (!(($user_id || $email) && $point && $task_name && $category_type && $task_type)) {
            $message = "need necessary items";
            return $message;
        }

        $em = $this->em;
        $user = "";
        if ($user_id) {
            $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        } else {
            $user = $em->getRepository('JiliApiBundle:User')->getUserByEmail($email);
        }
        if (!$user) {
            $message = "account not exist";
            return $message;
        }

        //更新user表总分数
        $userId = $user->getId();
        $oldPoint = $user->getPoints();
        $user->setPoints(intval($oldPoint + $point));
        $em->persist($user);
        $em->flush();

        //更新point_history表分数
        $params = array (
            'userid' => $userId,
            'point' => $point,
            'type' => $category_type,
        );
        $this->getPointHistory($params);

        //更新task_history表分数
        $params = array (
            'userid' => $userId,
            'orderId' => 0,
            'taskType' => $task_type,
            'categoryType' => $category_type,
            'task_name' => $task_name,
            'point' => $point,
            'date' => date_create(date('Y-m-d H:i:s')),
            'status' => 1
        );
        $this->initTaskHistory($params);

        return $message;
    }

    private function initTaskHistory($params = array ())
    {
        extract($params);
        return $this->task_logger->init($params);
    }

    private function getPointHistory($params = array ())
    {
        $this->point_logger->get($params);
    }

    public function getParameter($key)
    {
        return $this->container_->getParameter($key);
    }

    public function setContainer($c)
    {
        $this->container_ = $c;
    }

    public function setTaskLogger(TaskHistory $task_logger)
    {
        $this->task_logger = $task_logger;
    }

    public function setPointLogger(PointHistory $point_logger)
    {
        $this->point_logger = $point_logger;
    }
}
