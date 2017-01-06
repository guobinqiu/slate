<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wenwen\FrontendBundle\Entity\SurveySopParticipationHistory;

class SurveySopParticipationHistoryRepository extends EntityRepository
{
	public function getBySurveyId($surveyPartner, $paginator, $page = 1, $limit = 50){

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

    public function getByUserId($userId, $paginator, $page = 1, $limit = 50){

        $query = $this->createQueryBuilder('ssph');
        $query = $query->select('ssph');
        $query = $query->where('ssph.userId = :userId');
        $query = $query->addOrderBy('ssph.updatedAt', 'DESC');
        $query = $query->setParameter('userId',$userId);
        $query = $query->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );
        return $pagination;
    }

    public function getCountByUserAndSurveyId(){

    }

    public function getCountBySurveyIdAndStatus(){

    }

    public function getCountByStatus(){

    }

}
