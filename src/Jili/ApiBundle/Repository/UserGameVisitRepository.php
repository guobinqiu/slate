<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserGameVisitRepository extends EntityRepository
{
    public function getGameVisit($userid,$date)
    {
        $query = $this->createQueryBuilder('ug');
        $query = $query->select('ug.id,ug.visitDate');
        $query = $query->Where('ug.userid = :userid');
        $query = $query->andWhere('ug.visitDate = :date');
        $query = $query->setParameters(array('userid'=>$userid,'date'=>$date));
        $query =  $query->getQuery();
        return $query->getResult();
    }


}
