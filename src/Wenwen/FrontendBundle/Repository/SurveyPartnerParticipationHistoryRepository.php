<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;

class SurveyPartnerParticipationHistoryRepository extends EntityRepository
{
	public function getSurveyPartnersParticipationHistorys($surveyPartner, $paginator, $page = 1, $limit = 50){

        $query = $this->createQueryBuilder('spph');
        $query = $query->select('spph');
        $query = $query->where('spph.surveyPartner = :surveyPartner');
        $query = $query->addOrderBy('spph.createdAt', 'DESC');
        $query = $query->setParameter('surveyPartner',$surveyPartner);
        $query = $query->getQuery();

	    $pagination = $paginator->paginate(
	        $query,
	        $page,
	        $limit
	    );
	    return $pagination;
    }

    public function countByUserAndSurveyPartner($user, $surveyPartner){

    	$query = $this->createQueryBuilder('spph');
        $query = $query->select('count(spph)');
        $query = $query->where('spph.surveyPartner = :surveyPartner');
        $query = $query->andWhere('spph.user= :user');
        $query = $query->setParameter('surveyPartner',$surveyPartner);
        $query = $query->setParameter('user',$user);
        $query = $query->getQuery();

        $count = $query->getSingleScalarResult();
        return $count;
    }

    public function getCountBySurveyPartnerAndStatus($surveyPartner, $status){

        $query = $this->createQueryBuilder('spph');
        $query = $query->select('count(spph)');
        $query = $query->where('spph.surveyPartner = :surveyPartner');
        $query = $query->andWhere('spph.status= :status');
        $query = $query->setParameter('surveyPartner',$surveyPartner);
        $query = $query->setParameter('status',$status);
        $query = $query->getQuery();

        $count = $query->getSingleScalarResult();
        return $count;
    }

    public function getCountByStatus($status, \DateTime $from,  \DateTime $to){

        $query = $this->createQueryBuilder('spph');
        $query = $query->select('count(spph)');
        $query = $query->where('spph.status= :status');
        $query = $query->andWhere('spph.createdAt >= :from');
        $query = $query->andWhere('spph.createdAt < :to');
        $query = $query->setParameter('status',$status);
        $query = $query->setParameter('from',$from);
        $query = $query->setParameter('to',$to);
        $query = $query->getQuery();

        $count = $query->getSingleScalarResult();
        return $count;
    }

}
