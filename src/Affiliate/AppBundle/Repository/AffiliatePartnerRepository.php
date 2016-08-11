<?php
namespace Affiliate\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AffiliatePartnerRepository extends EntityRepository
{
	public function getParnters($paginator, $page = 1, $limit = 10){
	    //$query = $this->em->createQuery('SELECT ap.partnerId, ap.name, ap.description, ap.createdAt, ap.updateAt FROM AffiliateAppBundle:AffiliatePartner ap');

        $query = $this->createQueryBuilder('ap');
        $query = $query->select('ap');
        /*
        $query = $query->Where('sp.id = :id');
        $query = $query->andWhere('sp. statusFlag = :statusFlag');
        $query = $query->setParameter('id', $app_mid);
        $query = $query->setParameter('statusFlag', $sop_respondent::STATUS_ACTIVE);
        */
        $query = $query->getQuery();


	    $pagination = $paginator->paginate(
	        $query,
	        $page,
	        $limit
	    );
	    return $pagination;
    }
}