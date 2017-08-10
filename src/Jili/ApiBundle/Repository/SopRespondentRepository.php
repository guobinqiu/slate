<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\SopRespondent;
use Ramsey\Uuid\Uuid;

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
        while ($this->isAppMidDupliated($sop_respondent->getAppMid())) {
            $sop_respondent->setAppMid(Uuid::uuid1()->toString());
        }
        $sop_respondent->setUserId($user_id);
        $sop_respondent->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $em->persist($sop_respondent);
        $em->flush();
        return $sop_respondent;
    }

    public function retrieveByAppMid($app_mid)
    {
        $query = $this->createQueryBuilder('sp');
        $query = $query->select('sp');
        $query = $query->Where('sp.appMid = :app_mid');
        $query = $query->andWhere('sp. statusFlag = :statusFlag');
        $query = $query->setParameter('app_mid', $app_mid);
        $query = $query->setParameter('statusFlag', SopRespondent::STATUS_ACTIVE);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }

    public function retrieve91wenwenRecipientData($app_mid)
    {
        $sql = <<<EOT
            SELECT
                u.id,
                u.email,
                u.nick AS name1
            FROM sop_respondent res
            INNER JOIN user u
                ON u.id = res.user_id
            WHERE
                res.app_mid = ?
                AND
                res.status_flag = ?
EOT;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute(array($app_mid, SopRespondent::STATUS_ACTIVE));
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function isAppMidDupliated($appMid)
    {
        return count($this->getEntityManager()->getRepository('JiliApiBundle:SopRespondent')->findByAppMid($appMid)) > 0;
    }
}
