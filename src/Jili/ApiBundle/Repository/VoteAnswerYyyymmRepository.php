<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VoteAnswerYyyymmRepository extends EntityRepository
{

    public function getTableNameByYyyymm($yyyymm)
    {
        if (!preg_match('/^\d{4}\d{2}$/', $yyyymm)) {
            throw new Exception('invalid $yyyymm: ' . $yyyymm);
            return false;
        }
        return sprintf('vote_answer_%s', $yyyymm);
    }

    public function createYyyymmTable($yyyymm)
    {
        $tablename = $this->getTableNameByYyyymm($yyyymm);
        $em = $this->getEntityManager();
        $stm = $em->getConnection()->prepare("
                CREATE TABLE IF NOT EXISTS $tablename (
                id int(11) NOT NULL auto_increment,
                user_id int(11) NOT NULL,
                vote_id int(11) NOT NULL,
                answer_number tinyint(4) NOT NULL,
                updated_at datetime default NULL,
                created_at datetime default NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY (user_id,vote_id),
                KEY  (vote_id)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
            ");
        return $stm->execute();
    }

    public function getAnswerCount($vote_id, $yyyymm)
    {
        $tablename = $this->getTableNameByYyyymm($yyyymm);
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare("SELECT COUNT(id) FROM $tablename where vote_id = :vote_id");
        $params['vote_id'] = $vote_id;
        $stmt->execute($params);
        $return = $stmt->fetchColumn();
        return $return;
    }
}