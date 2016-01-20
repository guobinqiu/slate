<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\SopRespondent;

class SopRespondentRepository extends EntityRepository
{

    public function retrieveOrInsertByUserId($user_id)
    {
        $em = $this->getEntityManager();
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->findOneByUserId($user_id);
        if (!$sop_respondent) {
            $sop_respondent = $this->insertByUser($user_id);
        }
        return $sop_respondent;
    }

    public function insertByUser($user_id)
    {
        $em = $this->getEntityManager();
        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId($user_id);
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $em->persist($sop_respondent);
        $em->flush();
        return $sop_respondent;
    }
}
