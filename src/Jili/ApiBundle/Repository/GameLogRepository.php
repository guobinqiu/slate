<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;

class GameLogRepository extends EntityRepository
{
	public function getGameInfo($uid,$date)
	{
		$query = $this->createQueryBuilder('g');
		$query = $query->select('g.id,g.gamePoint');
		$query = $query->Where('g.pointUid = :uid');
		$query = $query->andWhere('g.gameDate = :date');
		$query = $query->setParameters(array('uid'=>$uid,'date'=>$date));
		$query = $query->getQuery();
		return $query->getResult();
	}
	
	
	
}