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
		$query = $query->select('cb.id,cb.title,cb.author,cb.content,cb.createTime,cb.startTime,cb.url');
		$query = $query->orderBy('cb.createTime','DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	}
	

	
}