<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserInfoVisitRepository extends EntityRepository
{
    public function getInfoVisit($userid,$date)
    {
        $query = $this->createQueryBuilder('ui');
        $query = $query->select('ui.id,ui.visitDate');
        $query = $query->Where('ui.userid = :userid');
        $query = $query->andWhere('ui.visitDate = :date');
        $query = $query->setParameters(array('userid'=>$userid,'date'=>$date));
        $query =  $query->getQuery();
        return $query->getResult();
    }


}
