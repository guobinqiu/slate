<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\SopRespondent;

class SopRespondentRepository extends EntityRepository
{
    public function insertByUser($userId, $appId)
    {
        $em = $this->getEntityManager();
        $sopRespondent = new SopRespondent();
        $i = 0;
        while ($this->isAppMidDuplicated($sopRespondent->getAppMid())) {
            $sopRespondent->setAppMid(SopRespondent::generateAppMid());
            $i++;
            if ($i > 1000) {
                break;
            }
        }
        $sopRespondent->setUserId($userId);
        $sopRespondent->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $sopRespondent->setAppId($appId);
        $em->persist($sopRespondent);
        $em->flush();
        return $sopRespondent;
    }

    public function retrieveByAppMid($appMid)
    {
        $query = $this->createQueryBuilder('sp');
        $query = $query->select('sp');
        $query = $query->Where('sp.appMid = :app_mid');
        $query = $query->andWhere('sp. statusFlag = :statusFlag');
        $query = $query->setParameter('app_mid', $appMid);
        $query = $query->setParameter('statusFlag', SopRespondent::STATUS_ACTIVE);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }

    public function retrieve91wenwenRecipientData($appMid)
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
        $stmt->execute(array($appMid, SopRespondent::STATUS_ACTIVE));
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function isAppMidDuplicated($key)
    {
        return count($this->getEntityManager()->getRepository('JiliApiBundle:SopRespondent')->findByAppMid($key)) > 0;
    }
}
