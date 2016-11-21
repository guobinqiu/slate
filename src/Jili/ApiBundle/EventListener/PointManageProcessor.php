<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 **/
class PointManageProcessor {
    private $em;
    private $logger;
    private $container_;

    public function __construct(LoggerInterface $logger, EntityManager $em) {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function process($path, $log_path) {
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
                $data_with_line_number = "line=[" . ($i+1 ) . "] [" . implode(",", $data) . "] ";
                if ($return) {
                    $code[] = $data_with_line_number . $return;
                    fwrite($log_handle, $data_with_line_number . "," . $return . "\n");
                } else {
                    fwrite($log_handle, $data_with_line_number . "," . "point import success\n");
                }
            }
            $i++;
        }

        

        fclose($handle);
        fclose($log_handle);

        if ($code) {
            $code[] = "这些该用户积分导入失败";
        } else {
            $arr['success'] = "导入成功";
        }
        $arr['code'] = $code;
        return $arr;
    }

    //更新point: user, point_history , task_history
    public function updatePoint($data) {
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

        //加上事务处理
        $em = $this->em;
        $db_connection = $em->getConnection();
        $db_connection->beginTransaction();
        try {

            $user = "";
            if ($user_id) {
                $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
            } else {
                $user = $em->getRepository('WenwenFrontendBundle:User')->getUserByEmail($email);
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
                'type' => $category_type
            );
            $this->createPointHistory($params);

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
            $this->createTaskHistory($params);
        
            $db_connection->commit();
            $em->clear();
        } catch (\Exception $e) {
            echo $e->getMessage();
            $db_connection->rollback();
            $message = "rollback.导入失败，请查明原因再操作" . $e->getMessage();

        }

        return $message;
    }

    public function createPointHistory(array $params = array() )
    {
        extract($params);

        $point_history = 'Jili\ApiBundle\Entity\PointHistory0'. ( $userid % 10);

        $po = new $point_history();

        $em = $this->em;
        $po->setUserId($userid);
        $po->setPointChangeNum($point);
        $po->setReason($type);
        $em->persist($po);
        $em->flush();
    }

    public function createTaskHistory( array $params=array())
    {
        extract($params);
        $em = $this->em;
        $flag =  $userid % 10;
        $task_history = 'Jili\ApiBundle\Entity\TaskHistory0'. $flag;
        $po = new $task_history();
        $po->setUserid($userid);
        $po->setOrderId($orderId);
        $po->setOcdCreatedDate($date);
        $po->setCategoryType($categoryType);
        $po->setTaskType($taskType);
        $po->setTaskName(trim($task_name));
        $po->setDate($date);
        $po->setPoint( $point);
        $po->setStatus($status);

        if(isset($reward_percent)) {
            $po->setRewardPercent( $reward_percent );
        }

        $em->persist($po);
        $em->flush();
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer($c) {
        $this->container_ = $c;
    }
}
