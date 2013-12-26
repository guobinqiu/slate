<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserWenwenVisitRepository extends EntityRepository
{
	public function getWenwenVisit($userid, $date)
	{
		$query = $this->createQueryBuilder('uww');
		$query = $query->select('uww.id,uww.visitDate');
		$query = $query->Where('uww.userid = :userid');
		$query = $query->andWhere('uww.visitDate = :date');
		$query = $query->setParameters(array('userid'=>$userid,'date'=>$date));
		$query =  $query->getQuery();
		return $query->getResult();
	}


}