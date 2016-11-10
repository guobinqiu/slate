<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;

class SurveyPartnerRepository extends EntityRepository
{
	public function getSurveyPartners($paginator, $page = 1, $limit = 10){

        $query = $this->createQueryBuilder('sp');
        $query = $query->select('sp');
        $query = $query->addOrderBy('sp.status', 'DESC');
        $query = $query->addOrderBy('sp.createdAt', 'DESC');
        $query = $query->getQuery();

	    $pagination = $paginator->paginate(
	        $query,
	        $page,
	        $limit
	    );
	    return $pagination;
    }

    /**
     * 获取用户可以回答（继续回答）的问卷一览
     * survey_id
     */
    public function getSurveyPartnersForUser($user){

        $query = $this->createQueryBuilder('sp');
        $query = $query->select('sp.surveyId, sp.partnerId, sp.title, sp.content, sp.url, sp.completePoint, 
            sp.screenoutPoint, sp.quotafullPoint, sp.loi, sp.ir, spph.status');
        $query = $query->leftJoin('WenwenFrontendBundle:SurveyPartnerParticipationHistory', 'spph', 'WITH', 'sp = spph.surveyPartner and spph.user = :user');
        $query = $query->where('sp.status = :status');
        $query = $query->andWhere('spph.status is NULL or spph.status = :init or spph.status = :reentry');
        $query = $query->setParameter('status',SurveyPartner::STATUS_OPEN);
        $query = $query->setParameter('user',$user);
        $query = $query->setParameter('init',SurveyPartnerParticipationHistory::STATUS_INIT);
        $query = $query->setParameter('reentry',SurveyPartnerParticipationHistory::STATUS_REENTRY);

        $query = $query->getQuery();

	    return $query->getResult();
    }

}
