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

    public function retrieveById($app_mid)
    {
        $sop_respondent = new SopRespondent();

        $query = $this->createQueryBuilder('sp');
        $query = $query->select('sp');
        $query = $query->Where('sp.id = :id');
        $query = $query->andWhere('sp. statusFlag = :statusFlag');
        $query = $query->setParameter('id', $app_mid);
        $query = $query->setParameter('statusFlag', $sop_respondent::STATUS_ACTIVE);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }

    public function retrieve91wenwenRecipientData($id)
    {
        $sql = <<<EOT
            SELECT
                u.email,
                IFNULL(u.nick, u.email) AS name1,
                IFNULL(IF(u.sex = '2', '女士', '先生'), '先生') AS title
            FROM sop_respondent res
            INNER JOIN user u
                ON u.id = res.user_id
            WHERE
                res.id = ?
                AND
                res.status_flag = ?
EOT;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $sop = new SopRespondent();
        $sop = $stmt->execute(array (
            $id,
            $sop::STATUS_ACTIVE
        ));
        $res = $stmt->fetchAll();
        return $res;
    }
}