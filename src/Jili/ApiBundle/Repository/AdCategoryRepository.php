<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdCategoryRepository extends EntityRepository {
	public function getCategoryList() {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.displayName');
		$query = $query->getQuery();
		return $query->getResult();
	}
}