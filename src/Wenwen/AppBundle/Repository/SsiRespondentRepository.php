<?php

namespace Wenwen\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SsiRespondentRepository extends EntityRepository
{
    public function retrieveRecipientDataToSendMailById($ssiRespondentId)
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare('
            SELECT
                user.email,
                IFNULL(user.nick, user.email) AS name1,
                IFNULL(IF(user.sex = "2", "女士", "先生"), "先生") AS title
            FROM ssi_respondent
            INNER JOIN user
                ON ssi_respondent.user_id = user.id
            WHERE
                ssi_respondent.id = ?
                AND
                ssi_respondent.status_flag = ?
        ');
        $stmt->execute(array(
            $ssiRespondentId,
            \Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE,
        ));
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $res;
    }
}
