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

    public function getSurveyPartnersParticipationHistorysByUser($user, $paginator, $page = 1, $limit = 50){

        $query = $this->createQueryBuilder('spph');
        $query = $query->select('spph');
        $query = $query->where('spph.user = :user');
        $query = $query->addOrderBy('spph.createdAt', 'DESC');
        $query = $query->setParameter('user',$user);
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

    public function getRealLoi($surveyPartner){
        $sql = "
            select
            round(avg(complete_time),1) as real_complete_time,
            round(avg(screenout_time),1) as real_screenout_time,
            round(avg(quotafull_time),1) as real_quotafull_time,
            round(avg(error_time),1) as real_error_time
            from(
                select 
                user_id,
                TIME_TO_SEC(TIMEDIFF(complete_at, forward_at))/60 as complete_time, 
                TIME_TO_SEC(TIMEDIFF(screenout_at, forward_at))/60 as screenout_time,
                TIME_TO_SEC(TIMEDIFF(quotafull_at, forward_at))/60 as quotafull_time,
                TIME_TO_SEC(TIMEDIFF(error_at, forward_at))/60 as error_time
                from(
                    select 
                    user_id, 
                    MAX(CASE WHEN status='forward' THEN created_at END) as forward_at, 
                    MAX(CASE WHEN status='complete' THEN created_at END) as complete_at, 
                    MAX(CASE WHEN status='screenout' THEN created_at END) as screenout_at, 
                    MAX(CASE WHEN status='quotafull' THEN created_at END) as quotafull_at, 
                    MAX(CASE WHEN status='error' THEN created_at END) as error_at
                    from survey_partner_participation_history 
                    where
                    survey_partner_id=" . $surveyPartner->getId() . "
                    group by user_id
                ) as result
                where 
                complete_at is not null
                or
                screenout_at is not null
                or 
                quotafull_at is not null
            ) as summary
            ;
        ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();
        return $result[0];
    }

}
