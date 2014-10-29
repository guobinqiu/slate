<?php
namespace Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserTaobaoVisitRepository extends EntityRepository
{
    public function getTaobaoVisit($userId, $date)
    {
        $query = $this->createQueryBuilder('utv');
        $query = $query->select('utv.id,utv.visitDate');
        $query = $query->Where('utv.userId = :userId');
        $query = $query->andWhere('utv.visitDate = :date');
        $query = $query->setParameters(array('userId'=>$userId,'date'=>$date));
        $query =  $query->getQuery();
        return $query->getResult();
    }
}
