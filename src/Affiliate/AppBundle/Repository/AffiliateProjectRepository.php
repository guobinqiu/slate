<?php
namespace Affiliate\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AffiliateProjectRepository extends EntityRepository
{
    public function getProjects($partnerId, $paginator, $page = 1, $limit = 20){
	    
        $query = $this->createQueryBuilder('ap');
        $query = $query->select('ap');
        $query = $query->Where('ap.partnerId = :partnerId');
        $query = $query->orderBy('ap.createdAt', 'DESC');
        $query = $query->setParameter('partnerId', $partnerId);
        $query = $query->getQuery();

	    $pagination = $paginator->paginate(
	        $query,
	        $page,
	        $limit
	    );
	    return $pagination;
    }
}