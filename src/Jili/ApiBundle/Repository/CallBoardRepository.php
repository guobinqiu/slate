<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class CallBoardRepository extends EntityRepository
{
	public function getCallboard()
	{
		$query = $this->createQueryBuilder('cb');
		$query = $query->select('cb.id,cb.title,cb.author,cb.content,cb.createTime,cb.startTime,cb.url,cc.categoryName');
		$query = $query->innerJoin('JiliApiBundle:CbCategory', 'cc', 'WITH', 'cb.cbType = cc.id ');
		$query = $query->orderBy('cb.startTime','DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	}

	public function getFiveCallboard()
	{
		$query = $this->createQueryBuilder('cb');
		$query = $query->select('cb.id,cb.title,cb.author,cb.content,cb.createTime,cb.startTime,cb.url,cc.categoryName');
		$query = $query->innerJoin('JiliApiBundle:CbCategory', 'cc', 'WITH', 'cb.cbType = cc.id ');
		$query = $query->orderBy('cb.startTime','DESC');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(5);
		$query =  $query->getQuery();
		return $query->getResult();
	}
	

	
}