<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Wenwen\FrontendBundle\Entity\User;

class UserRepository extends EntityRepository
{
    public function findNick($email, $nick)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id');
        $query = $query->Where('u.nick = :nick');
        $query = $query->andWhere('u.email <> :email');
        //$query = $query->andWhere('u.pwd is not null');
        //$query = $query->andWhere('u.deleteFlag IS NULL OR u.deleteFlag = 0');
        $query = $query->setParameters(array (
            'email' => $email,
            'nick' => $nick
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function getUserByEmail($email)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u');
        $query = $query->Where('u.email = :email');
        $query = $query->andWhere('u.pwd is not null');
        $query = $query->andWhere('u.deleteFlag IS NULL OR u.deleteFlag = 0');
        $query = $query->setParameter('email', $email);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * @param $date_str date("Y-m-d")
     */
    public function getRecentPoint($date_str)
    {
        $start = $date_str . ' 00:00:00';
        $end = $date_str . ' 23:59:59';

        $s = <<<EOT
select b.id,  a.nick, a.icon_path, b.create_time, b.reason, b.point_change_num, c.display_name from  (
SELECT  id , user_id ,point_change_num,create_time, reason from point_history00 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history01 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history02 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history03 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history04 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history05 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history06 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history07 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history08 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
union all
SELECT  id , user_id ,point_change_num,create_time, reason from point_history09 where reason != 13 and  reason != 15 and point_change_num != 0 and create_time >= :start and create_time <= :end
) b
inner join user a on  b.user_id = a.id
inner join ad_category c on b.reason = c.id
ORDER BY abs(b.point_change_num) desc , b.create_time asc limit 99
EOT;
        $stmt = $this->getEntityManager()->getConnection()->prepare($s);
        $stmt->execute(compact('start', 'end'));
        $return = $stmt->fetchAll();
        return $return;
    }

    public function getRanking($start, $end)
    {
        $sql = "select
            a.nick,
            sum(b.point_change_num) total
            from
            user a
            left join (
                select
                user_id,
                create_time ,
                point_change_num
                from
                point_history00
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history01
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history02
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history03
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history04
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history05
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history06
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history07
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history08
                union
                select
                user_id,
                create_time ,point_change_num
                from
                point_history09
            ) b on a.id = b.user_id
            where create_time >= '" . $start . "'
            and create_time <= '" . $end . "'
            and point_change_num > 0
            group by nick
            order by total desc
            limit 5
            ";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    /**
     *
     */
    public function findEmailById(array $ids)
    {
        $r = array ();
        if (count($ids) > 0) {
            $qb = $this->createQueryBuilder('u');
            $qb = $qb->select('u.id as id , u.email as email');
            $qb = $qb->Where($qb->expr()->in('u.id', $ids));
            $qb = $qb->getQuery();

            $emails = $qb->getResult(Query::HYDRATE_OBJECT);
            foreach ($emails as $e) {
                $r[$e['id']] = $e['email']; //['email'];
            }
        }
        return $r;
    }

    public function addPointHistorySearch($start, $end, $category_type, $email, $user_id)
    {
        $sql1 = "1=1";
        $sql2 = "";
        if ($category_type) {
            $sql1 .= " and category_type = " . $category_type;
        }
        if ($start) {
            $start = $start . " 00:00:00";
            $sql1 .= " and date >= '" . $start . "'";
        } else {
            $sql1 .= " and date >= '" . date('Y-m-d') . " 00:00:00'";
        }
        if ($end) {
            $end = $end . " 23:59:59";
            $sql1 .= " and date <= '" . $end . "'";
        } else {
            $sql1 .= " and date <= '" . date('Y-m-d') . " 23:59:59'";
        }
        if ($user_id) {
            $sql2 .= " and a.id = " . $user_id;
        }
        if ($email) {
            $sql2 .= " and a.email = '" . $email . "'";
        }

        $sql = "select a.id, a.email,b.point,b.category_type,b.task_type,b.task_name,b.date,b.status from user a inner join
            (select user_id,point,category_type,task_type,task_name,date,status from task_history00 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history01 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history02 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history03 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history04 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history05 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history06 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history07 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history08 where " . $sql1 . "
            union all select user_id,point,category_type,task_type,task_name,date,status from task_history09 where " . $sql1 . " )b
            on a.id = b.user_id where (a.delete_flag IS NULL OR a.delete_flag = 0) " . $sql2 . " order by b.date desc";
        //echo $sql;
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    public function memberSearch($user_id, $email, $nick)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u');
        $query = $query->Where('1 = 1');
        $param = array ();
        if ($user_id) {
            $query = $query->andWhere('u.id = :user_id');
            $param['user_id'] = trim($user_id);
        }
        if ($email) {
            $query = $query->andWhere('u.email = :email');
            $param['email'] = trim($email);
        }
        if ($nick) {
            $query = $query->andWhere('u.nick = :nick');
            $param['nick'] = trim($nick);
        }
        $query = $query->setParameters($param);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * @param $start date("Y-m-d")
     * @param $end date("Y-m-d")
     * @param $limit
     * @param $offset
     */
    public function getTotalCPAPointsByTime($start, $end, $limit, $offset)
    {
        $s = <<<EOT
select a.id,a.email,a.nick,b.points from user a inner join
(
select user_id, sum(point) as points from task_history00 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history01 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history02 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history03 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history04 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history05 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history06 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history07 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history08 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
union all
select user_id, sum(point) as points from task_history09 t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end group by user_id
 ) b
 on b.user_id = a.id order by b.points desc LIMIT :offset, :limit
EOT;
        $stmt = $this->getEntityManager()->getConnection()->prepare($s);

        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);

        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    /**
     * @param array array('id'=> , 'points');
     * @return integer rows updated
     */
    public function updatePointById($params)
    {
        $em = $this->getEntityManager();
        $stm = $em->getConnection()->prepare('update user u set u.points = u.points + :points where u.id  = :id');
        return $stm->execute($params);
    }

    /**
     * Get the total number of users
     * @param array $values search condition
     * @param String $type registered or withdrawal
     * @return integer count
     */
    public function getSearchUserCount($values, $type)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('COUNT(u.id)');
        $query = $this->getSearchUserSqlQuery($query, $values, $type);
        $count = $query->getSingleScalarResult();
        return $count;
    }

    /**
     * Get user list by search condition
     * @param array $values search condition
     * @param String $type registered or withdrawal
     * @param integer $pageSize
     * @param integer $currentPage
     * @return array user
     */
    public function getSearchUserList($values, $type, $pageSize, $currentPage)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id,u.email,u.nick,u.tel,u.registerCompleteDate,u.lastLoginDate,u.createdRemoteAddr,sp.id as app_mid');
        $query = $this->getSearchUserSqlQuery($query, $values, $type);

        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $query = $query->setFirstResult($pageSize * ($currentPage - 1));
        $query = $query->setMaxResults($pageSize);

        return $query->getResult();
    }

    /**
     * @param $query
     * @param array $values search condition
     * @param String $type registered or withdrawal
     * @return $query
     */
    public function getSearchUserSqlQuery($query, $values, $type)
    {
        $query = $query->Where('1 = 1');
        $param = array ();

        if (isset($values['app_mid']) && $type == 'registered') {
            $query = $query->innerJoin('JiliApiBundle:SopRespondent', 'sp', 'WITH', 'u.id = sp.userId');
            $query = $query->andWhere('sp.id = :app_mid');
            $param['app_mid'] = $values['app_mid'];
        } else {
            $query = $query->leftJoin('JiliApiBundle:SopRespondent', 'sp', 'WITH', 'u.id = sp.userId');
        }

        if (isset($values['user_id'])) {
            $query = $query->andWhere('u.id = :id');
            $param['id'] = $values['user_id'];
        }

        if (isset($values['email'])) {
            $query = $query->andWhere('u.email = :email');
            $param['email'] = $values['email'];
        }

        if (isset($values['nickname'])) {
            $query = $query->andWhere('u.nick LIKE :nick');
            $param['nick'] = '%' . $values['nickname'] . '%';
        }

        if (isset($values['mobile_number'])) {
            $query = $query->andWhere('u.tel = :tel');
            $param['tel'] = $values['mobile_number'];
        }

        if (isset($values['birthday'])) {
            $query = $query->andWhere('u.birthday = :birthday');
            $param['birthday'] = $values['birthday'];
        }

        if (isset($values['registered_from'])) {
            $query = $query->andWhere('u.registerCompleteDate >= :registerCompleteDateFrom');
            $param['registerCompleteDateFrom'] = $values['registered_from'] . ' 00:00:00';
        }

        if (isset($values['registered_to'])) {
            $query = $query->andWhere('u.registerCompleteDate <= :registerCompleteDateTo');
            $param['registerCompleteDateTo'] = $values['registered_to'] . ' 23:59:59';
        }

        if ($type == 'registered') {
            $query = $query->andWhere('u.deleteFlag IS NULL OR u.deleteFlag = 0');
        } elseif ($type == 'withdrawal') {
            $query = $query->andWhere('u.deleteFlag = 1');
        }

        $query = $query->addOrderBy('u.id', 'DESC');

        $query = $query->setParameters($param);

        return $query->getQuery();
    }

    /**
    * select u.id, u.email, u.points from user u where u.points > 0 and u.last_get_points_at >= $from and u.last_get_points_at < $to
    * @param DateTime $from 
    * @param DateTime $to
    * @return array $result
    */
    public function findExpiringUsers($from, $to)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id');
        $query = $query->addSelect('u.email');
        $query = $query->addSelect('u.nick');
        $query = $query->addSelect('u.points');
        $query = $query->Where('u.points > 0');
        $query = $query->andWhere('u.lastGetPointsAt >= :from');
        $query = $query->andWhere('u.lastGetPointsAt < :to');
        $query = $query->setParameters(array (
            'from' => $from,
            'to' => $to
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function getRecruitRouteDailyCount(\DateTime $from, \DateTime $to){
        $sql = "SELECT DATE_FORMAT(u.register_complete_date, '%Y-%m-%d') as date, IFNULL(ut.register_route, 'N/A') as recruit_route, count(*) as count"
        . " FROM user u LEFT JOIN user_track ut ON( u.id = ut.user_id) "
        . " WHERE u.register_complete_date >= :from  AND u.register_complete_date < :to "
        . " GROUP BY DATE_FORMAT(u.register_complete_date, '%Y-%m-%d'), ut.register_route "
        . " ORDER BY DATE_FORMAT(u.register_complete_date, '%Y-%m-%d') DESC";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $params = array(
            "from"  => $from->format('Y-m-d'),
            "to"  => $to->format('Y-m-d')
        );
        $stmt->execute($params);

        $results = $stmt->fetchAll();  

        return $results;
    }

    public function getRecruitRouteMonthlyCount(\DateTime $from, \DateTime $to){
        $sql = "SELECT DATE_FORMAT(u.register_complete_date, '%Y-%m') as date, IFNULL(ut.register_route, 'N/A') as recruit_route, count(*) as count"
        . " FROM user u LEFT JOIN user_track ut ON( u.id = ut.user_id) "
        . " WHERE u.register_complete_date >= :from  AND u.register_complete_date < :to "
        . " GROUP BY DATE_FORMAT(u.register_complete_date, '%Y-%m'), ut.register_route "
        . " ORDER BY DATE_FORMAT(u.register_complete_date, '%Y-%m') DESC";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $params = array(
            "from"  => $from->format('Y-m-01 00:00:00'),
            "to"  => $to->format('Y-m-01 00:00:00')
        );
        $stmt->execute($params);

        $results = $stmt->fetchAll();  

        return $results;
    }
}