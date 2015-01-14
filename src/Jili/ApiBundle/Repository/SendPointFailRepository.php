<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class SendPointFailRepository extends EntityRepository
{
    public function issetFailRecord($user_id,$send_type)
    {
        $daydate =  date("Y-m-d H:i:s", strtotime(' -180 day'));
        $query = $this->createQueryBuilder('spf');
        $query = $query->select('spf.userId');
        $query = $query->Where('spf.userId = :user_id');
        $query = $query->andWhere('spf.sendType = :send_type');
        $query = $query->andWhere('spf.createtime > :daydate');
        $query = $query->setParameters(array('user_id'=>$user_id,'send_type'=>$send_type,'daydate'=>$daydate));
        $query = $query->getQuery();
        return $query->getArrayResult();
    }
    
    //get sended users in half year
    public function gethasSendedUsers($not_in_users,$send_type)
    {
        $daydate =  date("Y-m-d H:i:s", strtotime(' -180 day'));
        $query = $this->createQueryBuilder('spf');
        $query = $query->select('distinct spf.userId');
        $query = $query->Where('spf.userId not in (:notUser)');
        $query = $query->andWhere('spf.sendType in (:send_type)');
        $query = $query->andWhere('spf.createtime > :daydate');
        $query = $query->setParameters(array('notUser'=>$not_in_users,'send_type'=>$send_type,'daydate'=>$daydate));
        $query = $query->getQuery();
        return $query->getArrayResult();
    }
}
