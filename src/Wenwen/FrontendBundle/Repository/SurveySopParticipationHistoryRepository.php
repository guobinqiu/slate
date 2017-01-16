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

    public function getByUserIdGroupBySurveyId($userId){
        $sql = "
            SELECT 
            survey_id, 
            MAX(CASE WHEN status='targeted' THEN updated_at END) as targeted_at, 
            MAX(CASE WHEN status='forward' THEN updated_at END) as forward_at, 
            MAX(CASE WHEN status='complete' THEN updated_at END) as complete_at, 
            MAX(CASE WHEN status='screenout' THEN updated_at END) as screenout_at, 
            MAX(CASE WHEN status='quotafull' THEN updated_at END) as quotafull_at, 
            MAX(CASE WHEN status='error' THEN updated_at END) as error_at,
            CASE WHEN status='forward' THEN client_ip END as client_ip
            FROM survey_sop_participation_history 
            WHERE
            user_id= " . $userId . "
            GROUP BY survey_id limit 100
        ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $results = $stmt->fetchAll();
        return $results;
    }

    public function getCountByUserAndSurveyId(){

    }

    public function getCountBySurveyIdAndStatus(){

    }

    public function getCountByStatus(){

    }

}
