<?php
namespace Affiliate\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AffiliateProjectRepository extends EntityRepository
{
    public function findProjects($affiliatePartner, $paginator, $page = 1, $limit = 20){
	    
        $query = $this->createQueryBuilder('ap');
        $query = $query->select('ap');
        $query = $query->Where('ap.affiliatePartner = :affiliatePartner');
        $query = $query->orderBy('ap.createdAt', 'DESC');
        $query = $query->setParameter('affiliatePartner', $affiliatePartner);
        $query = $query->getQuery();

	    $pagination = $paginator->paginate(
	        $query,
	        $page,
	        $limit
	    );
	    return $pagination;
    }
}