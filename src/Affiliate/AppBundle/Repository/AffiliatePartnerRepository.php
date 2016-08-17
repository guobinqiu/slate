<?php
namespace Affiliate\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AffiliatePartnerRepository extends EntityRepository
{
	public function getParnters($paginator, $page = 1, $limit = 10){

        $query = $this->createQueryBuilder('ap');
        $query = $query->select('ap');
        $query = $query->getQuery();

	    $pagination = $paginator->paginate(
	        $query,
	        $page,
	        $limit
	    );
	    return $pagination;
    }
}