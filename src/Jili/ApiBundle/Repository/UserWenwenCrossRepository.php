<?php
namespace   Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\UserWenwenCross;

class UserWenwenCrossRepository extends EntityRepository {

    public function create($user_id) {
        $em = $this->getEntityManager();
        $cross = $em->getRepository('JiliApiBundle:UserWenwenCross')->findOneByUserId($user_id);
        if($cross){
            return $cross;
        }
        $cross = new UserWenwenCross();
        $cross->setUserId($user_id);
        $em->persist($cross);
        $em->flush();
        return $cross;
    }

}