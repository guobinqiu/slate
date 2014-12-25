<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Jili\ApiBundle\Entity\User;

class UserRepository extends EntityRepository
{
    public function getUserCount($start = false, $end = false, $pwd = false, $is_from_wenwen= false, $delete_flag = false)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('count(u.id) as num');
        $query = $query->Where('1 = 1');
        $param = array();
        if ($start){
            $start_time = $start . ' 00:00:00';
            $query = $query->andWhere('u.registerDate>=:start_time');
            $param['start_time'] = $start_time;
        }
        if ($end){
            $end_time = $end . ' 23:59:59';
            $query = $query->andWhere('u.registerDate<=:end_time');
            $param['end_time'] = $end_time;
        }
        if($pwd){
            $query = $query->andWhere('u.pwd IS NOT NULL');
        }
        if($is_from_wenwen){
            $query = $query->andWhere('u.isFromWenwen = :isFromWenwen');
            $param['isFromWenwen'] = $is_from_wenwen;
        }
        if($delete_flag){
            $query = $query->andWhere('u.deleteFlag = :deleteFlag');
            $param['deleteFlag'] = 1;//已删除用户
        }
        $query = $query->setParameters($param);
        $query = $query->getQuery();
        //echo $query->getSQL(); echo "<br>";
        return $query->getOneOrNullResult();
    }

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

    /**
     * The user of $email is registered already for the pwd is NOT null anymore.
     */
    public function getWenwenUser($email)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id');
        $query = $query->Where('u.email = :email');
        $query = $query->andWhere('u.pwd is not null');
        $query = $query->setParameter('email', $email);
        $query = $query->getQuery();
        return $query->getResult();

    }

    public function getNotActiveUserByEmail($email)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u');
        $query = $query->Where('u.email = :email');
        $query = $query->andWhere('u.isFromWenwen = 2');
        $query = $query->setParameter('email', $email);
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function getUserList($id)
    {
        $query = $this->createQueryBuilder('u');

        $query = $query->select('u.id,u.nick,u.email,sp.code');
        $query = $query->innerJoin('JiliApiBundle:SetPasswordCode', 'sp', 'WITH', 'u.id = sp.userId');

        $query = $query->Where('u.id = :id');
        $query = $query->setParameter('id', $id);
        $query = $query->getQuery();
        return $query->getResult();

    }

    public function isFromWenwen($email)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id,u.nick,u.email');
        $query = $query->Where('u.email = :email');
        $query = $query->andWhere('u.isFromWenwen = 1');
        $query = $query->setParameter('email', $email);
        $query = $query->getQuery();
        return $query->getResult();

    }

    public function isPwd($email)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.pwd');
        $query = $query->Where('u.email = :email');
        $query = $query->setParameter('email', $email);
        $query = $query->getQuery();
        $result = $query->getResult();
        return $result[0]['pwd'];

    }

    public function getSearch($email)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
        $query = $query->Where('u.email = :email');
        $query = $query->setParameter('email', $email);
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

    public function getMultiple($times)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
        $query = $query->Where('u.rewardMultiple > :times');
        $query = $query->setParameter('times', $times);
        $query = $query->getQuery();
        return $query->getResult();
    }
    
    public function pointFail($type)
    {
        $daydate = date("Y-m-d H:i:s", strtotime(' -' . $type . ' day'));
        $point_histories = array('point_history00','point_history01','point_history02','point_history03','point_history04','point_history05',
                                 'point_history06','point_history07','point_history08','point_history09');
        $task_histories = array('task_history00','task_history01','task_history02','task_history03','task_history04','task_history05',
                                 'task_history06','task_history07','task_history08','task_history09');
        
        $merged_point_result = array();
        for($i=0;$i<count($point_histories);$i++){
            $sql = "select distinct user_id from ".$point_histories[$i]." where create_time > '" . $daydate . "' ";
            $result = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
            $temp = array();
            foreach ($result as $key => $valus){
                $temp[]=$valus['user_id'];
            }
            $merged_point_result = array_merge($merged_point_result,$temp);
            unset($result);
            unset($temp);
        }
        $merged_task_result = array();
        for($i=0;$i<count($task_histories);$i++){
            $sql = "select distinct user_id from ".$task_histories[$i]." where status=2 and date > '" . $daydate . "' ";
            $result = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
            $temp = array();
            foreach ($result as $key => $valus){
                $temp[]=$valus['user_id'];
            }
            $merged_task_result = array_merge($merged_task_result,$temp);
            unset($result);
            unset($temp);
        }
        $user_ids = array_unique(array_merge($merged_point_result,$merged_task_result));
        $user_ids = implode(',', $user_ids);
        $sql = "select e.id,e.email,e.nick from user e where e.points>0 and (e.delete_flag IS NULL OR e.delete_flag =0) and e.register_date < '" . $daydate 
             . "' and e.id not in (" . $user_ids.")";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    
    public function pointFailTemp()
    {
        $sql_tmp = "(select distinct user_id from user_last)";
        $sql = "select e.id,e.email,e.nick from user e where e.points>0  and e.id in ".$sql_tmp;
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }
    
    /**
     * @param $date_str date("Y-m-d")
     */
    public function getRecentPoint($date_str)
    {
        $start = $date_str . ' 00:00:00';
        $end = $date_str . ' 23:59:59';

        $s =<<<EOT
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
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
        $stmt->execute( compact('start','end') );
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
        $r = array();
        if( count($ids) > 0) {
            $qb= $this->createQueryBuilder('u');
            $qb= $qb->select('u.id as id , u.email as email');
            $qb = $qb->Where($qb->expr()->in('u.id', $ids ));
            $qb = $qb->getQuery( );

            $emails = $qb->getResult(Query::HYDRATE_OBJECT);
            foreach ($emails as $e) {
                $r [$e['id']] = $e['email'];//['email'];
            }
        }
        return $r;
    }

    //七天未登陆提醒
    public function getUserListForRemindLogin($day)
    {
        $daydate = date("Y-m-d", strtotime(' -' . $day . ' day'));
        $sql = "SELECT user.id, user.email, user.nick
                FROM user
                WHERE register_date LIKE '".$daydate."%'
                AND (
                delete_flag IS NULL
                OR delete_flag =0
                )
                AND (
                last_login_date IS NULL
                OR last_login_date LIKE '".$daydate."%'
                )";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();

    }

    public function totalUserAndCount()
    {
        $sql = "SELECT count( * ) total_user , sum( `points` ) total_points FROM user";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetch();

    }

    //1,2,3 广告体验,购物返利,游戏广告,获得积分提醒
    public function getUserListForRemindPoint($day)
    {
        $daydate = date("Y-m-d", strtotime(' -' . $day . ' day'));
        $sql = "select a.nick, a.email, b.date, b.point, b.task_name, c.display_name from user a inner join
                 ( select id, user_id, date, point, category_type, task_name, status from task_history00 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history01 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history02 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history03 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history04 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history05 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history06 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history07 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history08 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)
                union select id, user_id, date, point, category_type, task_name, status from task_history09 where status=3 AND point >0 AND date like '".$daydate."%' and category_type in (1,2,3)) b
                on a.id = b.user_id inner join ad_category c on b.category_type = c.id WHERE a.delete_flag IS NULL OR a.delete_flag = 0";
        //1,2,3 广告体验,购物返利,游戏广告
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    //每个月2号凌晨发一封edm,统计3个月内有历史积分的人
    public function getUserListForRemindTotalPoint($start, $end)
    {
        $sql = "select a.id, a.email, a.points from user a inner join
                (select distinct user_id from point_history00 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history01 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history02 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history03 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history04 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history05 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history06 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history07 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history08 where create_time >= '".$start."' and create_time <= '".$end."'
                union all select distinct user_id from point_history09 where create_time >= '".$start."' and create_time <= '".$end."' )b
                on a.id = b.user_id where a.points > 0 AND (a.delete_flag IS NULL OR a.delete_flag = 0)";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    public function addPointHistorySearch($start, $end, $category_type, $email, $user_id)
    {
        $sql1 = "1=1";
        $sql2 = "";
        if($category_type){
            $sql1 .= " and category_type = ".$category_type;
        }
        if($start){
            $start = $start." 00:00:00";
            $sql1 .= " and date >= '".$start."'";
        }else{
            $sql1 .= " and date >= '".date('Y-m-d')." 00:00:00'";
        }
        if($end){
            $end = $end." 23:59:59";
            $sql1 .= " and date <= '".$end."'";
        }else{
            $sql1 .= " and date <= '".date('Y-m-d')." 23:59:59'";
        }
        if($user_id){
            $sql2 .= " and a.id = ".$user_id;
        }
        if($email){
            $sql2 .= " and a.email = '".$email."'";
        }

        $sql = "select a.id, a.email,b.point,b.category_type,b.task_type,b.task_name,b.date from user a inner join
                (select user_id,point,category_type,task_type,task_name,date from task_history00 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history01 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history02 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history03 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history04 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history05 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history06 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history07 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history08 where ".$sql1."
                union all select user_id,point,category_type,task_type,task_name,date from task_history09 where ".$sql1." )b
                on a.id = b.user_id where (a.delete_flag IS NULL OR a.delete_flag = 0) ".$sql2." order by b.date desc";
                //echo $sql;
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    public function findWenWenUsersForRemmindRegister($start_time, $end_time)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u.id,u.email');
        $query = $query->Where('u.isFromWenwen = 2');
        $query = $query->andWhere('u.pwd is null');
        $query = $query->andWhere('u.registerDate >= :start_time');
        $query = $query->andWhere('u.registerDate <= :end_time');
        $query = $query->setParameters(array (
             'start_time' => $start_time,
             'end_time' => $end_time
         ));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function memberSearch($user_id, $email, $nick)
    {
        $query = $this->createQueryBuilder('u');
        $query = $query->select('u');
        $query = $query->Where('1 = 1');
        $param = array();
        if($user_id){
            $query = $query->andWhere('u.id = :user_id');
            $param['user_id'] = trim($user_id);
        }
        if($email){
            $query = $query->andWhere('u.email = :email');
            $param['email'] = trim($email);
        }
        if($nick){
            $query = $query->andWhere('u.nick = :nick');
            $param['nick'] = trim($nick);
        }
        $query = $query->setParameters($param);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     *
     */
    public function findByValidateToken($token)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P7D'));
        $at = $date->format('Y-m-d H:i:s');

        $query = $this->createQueryBuilder('u');

        $query = $query->Where('u.token = :token');
        $query = $query->AndWhere('u.tokenCreatedAt >= :at');
        $query = $query->setParameters(array( 'token'=> $token, 'at'=> $at)  );
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function cleanToken($uid)
    {
        $entity  = $this->find($uid);
        if($entity) {

            $entity->setToken('');

            $this->getEntityManager()->flush();

        }
        return true;
    }

    /**
     * @param $start date("Y-m-d")
     * @param $end date("Y-m-d")
     * @param $limit
     * @param $offset
     */
    public function getTotalCPAPointsByTime($start,$end,$limit,$offset)
    {
        $s =<<<EOT
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
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);

        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);

        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    /**
     * @param $start date("Y-m-d")
     * @param $end date("Y-m-d")
     * @param $user_id
     * @param $table_name
     */
    public function getUserCPAPointsByTime($start,$end,$user_id)
    {
        $suffix = substr($user_id, -1, 1);
        $table_name = sprintf('task_history%02d', $suffix);

        $s =<<<EOT
select a.id,a.email,a.nick,b.points from user a inner join
(
select user_id, sum(point) as points from $table_name t where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and t.date >= :start and t.date <= :end and t.user_id = :user_id group by user_id
 ) b
 on b.user_id = a.id
EOT;
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);

        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);

        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    /**
     * create the user when sign up
     * @param  array('nick'=> , 'email'=>);
     * @return the User
     */
    public function createOnSignup($param)
    {
        $user =  new User;
        $user->setNick($param['nick']);
        $user->setEmail($param['email']);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * create the user on landing page
     * @param  array('nick'=> , 'email'=>, 'pwd'=> ,'');
     * @return the User
     */
    public function createOnLanding($param)
    {
        $user = new User();
        $user->setNick($param['nick']);
        $user->setEmail($param['email']);
        $user->setPwd($param['pwd']);
        $user->setIsFromWenwen(User::IS_NOT_FROM_WENWEN );
        if( isset($param['uniqkey'])){
            $user->setUniqkey($param['uniqkey']);
        }
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }
    /**
     * create the user on wenwen
     * @param  array( 'email'=>, 'uniqkey'=> ,'');
     * @return the User
     */
    public function createOnWenwen($param)
    {
        $user = new User();
        $user->setEmail($param['email']);
        $user->setUniqkey($param['uniqkey']);
        $user->setPoints(User::POINT_EMPTY);
        $user->setIsInfoSet(User::INFO_NOT_SET);
        $user->setIsFromWenwen(User::IS_FROM_WENWEN); //和91问问同时注册 2
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * create the user when regist by qq
     * @param  array('nick'=> , 'email'=> ,'pwd'=>);
     * @return the User
     */
    public function qquser_quick_insert(array $param)
    {
        $user =  new User;
        $user->setNick('QQ'.$param['nick']);
        $user->setEmail($param['email']);
        $user->setPwd($param['pwd']);
        $user->setDeleteFlag(0);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }
    
    /**
     * create the user when regist by taobao
     * @param  array('nick'=> , 'email'=> ,'pwd'=>);
     * @return the User
     */
    public function taboabo_user_quick_insert(array $param)
    {
        $user =  new User;
        $user->setNick("taobao_".$param['nick']);
        $user->setEmail($param['email']);
        $user->setPwd($param['pwd']);
        $user->setDeleteFlag(0);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
        return $user;

    public function getUserByCrossId($id)
    {
        $query = $this->createQueryBuilder('u');

        $query = $query->select('u.id,u.email,u.pwd');
        $query = $query->innerJoin('JiliApiBundle:UserWenwenCross', 'uwc', 'WITH', 'u.id = uwc.userId');
        $query = $query->Where('uwc.id = :id');
        $query = $query->setParameter('id', $id);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }
}
