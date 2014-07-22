<?php
namespace Jili\ApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\CheckinPointTimes;

/**
 *
 **/
class CheckInListener
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    //获取签到积分
    public function getCheckinPoint(Request $request)
    {
        $em = $this->em;
        //判断日期，和是否是当天注册,判断后台分数，默认值
        //默认值
        $maxPoints = $this->getParameter('init_one');
        //普通 type=1
        $pointTimes = $em->getRepository('JiliApiBundle:CheckinPointTimes')->getCheckinTimes(1);
        if($pointTimes){
            $maxPoints = $pointTimes[0]['pointTimes'] >= $maxPoints ? $pointTimes[0]['pointTimes']: $maxPoints;
        }
        //判断是否是当天注册
        $uid = $request->getSession()->get('uid');
        if($uid){
            $user = $em->getRepository('JiliApiBundle:User')->find($uid);
            $reg_date = $user->getRegisterDate()->format('Y-m-d');
            if(date('Y-m-d') == $reg_date){
                //注册当天签到 type=2
                $pointTimes = $em->getRepository('JiliApiBundle:CheckinPointTimes')->getCheckinTimes(2);
                if($pointTimes){
                    $maxPoints = $pointTimes[0]['pointTimes'] >= $maxPoints ? $pointTimes[0]['pointTimes']: $maxPoints;
                }
            }
        }
        return $maxPoints;
    }

    //获取签到积分
    public function getCheckinPointForReg(Request $request)
    {
        $em = $this->em;
        //判断日期，和是否是当天注册,判断后台分数，默认值
        //默认值
        $maxPoints = $this->getParameter('init_one');
        //普通 type=1
        $pointTimes = $em->getRepository('JiliApiBundle:CheckinPointTimes')->getCheckinTimes(1);
        if($pointTimes){
            $maxPoints = $pointTimes[0]['pointTimes'] >= $maxPoints ? $pointTimes[0]['pointTimes']: $maxPoints;
        }
        //当天注册 type=2
        $pointTimes = $em->getRepository('JiliApiBundle:CheckinPointTimes')->getCheckinTimes(2);
        if($pointTimes){
            $maxPoints = $pointTimes[0]['pointTimes'] >= $maxPoints ? $pointTimes[0]['pointTimes']: $maxPoints;
        }
        return $maxPoints;
    }

    public function getParameter($key)
    {
        return $this->container_->getParameter($key);
    }

    public function setContainer($c)
    {
        $this->container_ = $c;
    }

}
