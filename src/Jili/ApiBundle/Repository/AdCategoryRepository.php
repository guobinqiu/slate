<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdCategoryRepository extends EntityRepository {
	public function getCategoryList() {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.categoryName,a.asp,a.displayName');
		$query = $query->getQuery();
		return $query->getResult();
	}
}