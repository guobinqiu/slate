<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserAdvertisermentVisitRepository extends EntityRepository
{
	public function getAdvertisermentVisit($userid,$date)
	{
		$query = $this->createQueryBuilder('uad');
		$query = $query->select('uad.id,uad.visitDate');
		$query = $query->Where('uad.userid = :userid');
		$query = $query->andWhere('uad.visitDate = :date');
		$query = $query->setParameters(array('userid'=>$userid,'date'=>$date));
		$query =  $query->getQuery();
		return $query->getResult();
	}
}