<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository {
	public function userCount() {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('count(u.id) as num');
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getUserCount($start, $end) {
		if ($start)
			$start_time = $start . ' 00:00:00';
		if ($end)
			$end_time = $end . ' 23:59:59';
		$query = $this->createQueryBuilder('u');
		$query = $query->select('count(u.id) as num');
		if ($start && $end) {
			$query = $query->Where('u.registerDate>=:start_time');
			$query = $query->andWhere('u.registerDate<=:end_time');
			$query = $query->setParameters(array (
				'start_time' => $start_time,
				'end_time' => $end_time
			));
		} else {
			if ($start) {
				$query = $query->Where('u.registerDate>=:start_time');
				$query = $query->setParameter('start_time', $start_time);
			} else {
				if ($end) {
					$query = $query->Where('u.registerDate<=:end_time');
					$query = $query->setParameter('end_time', $end_time);
				}
			}
		}
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function findNick($email, $nick) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id');
		$query = $query->Where('u.nick = :nick');
		$query = $query->andWhere('u.email <> :email');
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
	public function getWenwenUser($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.pwd is not null');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function getNotActiveUserByEmail($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.isFromWenwen = 2');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getOneOrNullResult();
	}

	public function getUserList($id) {
		$query = $this->createQueryBuilder('u');

		$query = $query->select('u.id,u.nick,u.email,sp.code');
		$query = $query->innerJoin('JiliApiBundle:setPasswordCode', 'sp', 'WITH', 'u.id = sp.userId');

		$query = $query->Where('u.id = :id');
		$query = $query->setParameter('id', $id);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function isFromWenwen($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.isFromWenwen = 1');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function isPwd($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.pwd');
		$query = $query->Where('u.email = :email');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		$result = $query->getResult();
		return $result[0]['pwd'];

	}

	public function getSearch($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
		$query = $query->Where('u.email = :email');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getUserByEmail($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.pwd is not null');
		$query = $query->andWhere('u.deleteFlag IS NULL OR u.deleteFlag = 0');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getOneOrNullResult();
	}

	public function getMultiple($times) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
		$query = $query->Where('u.rewardMultiple > :times');
		$query = $query->setParameter('times', $times);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function pointFail($type) {
		$daydate = date("Y-m-d H:i:s", strtotime(' -' . $type . ' day'));
		$sqlpoint = " (select distinct user_id from point_history00 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history01 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history02 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history03 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history04 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history05 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history06 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history07 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history08 where create_time > '" . $daydate . "' " .
                "union select distinct user_id from point_history09 where create_time > '" . $daydate . "') ";

		$sqltask = " (select distinct user_id from task_history00 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history01 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history02 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history03 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history04 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history05 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history06 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history07 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history08 where status=2 and date > '" . $daydate . "' " .
                "union select distinct user_id from task_history09 where status=2 and date > '" . $daydate . "')  ";

		$sql = "select e.id,e.email,e.nick from user e where e.points>0 and (e.delete_flag IS NULL OR e.delete_flag =0) and e.register_date < '" . $daydate . "' and e.id not in " . $sqlpoint . " and e.id not in " . $sqltask;

        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();

	}

	public function getRecentPoint($yesterday) {
		$start = $yesterday . " 00:00:00";
		$end = $yesterday . " 23:59:59";
		$sql = "select a.nick, a.icon_path, b.create_time, b.reason, b.point_change_num, c.display_name from user a inner join
                ( select user_id, create_time, point_change_num, reason from point_history00 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num,reason from point_history01 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history02 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history03 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history04 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history05 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history06 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history07 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history08 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "'
                union all select user_id, create_time, point_change_num, reason from point_history09 where reason != 13 and point_change_num != 0 and create_time >= '" . $start . "' and create_time <= '" . $end . "' ) b
                on a.id = b.user_id inner join ad_category c on b.reason = c.id
                order by abs(point_change_num) desc limit 99";
		return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
	}

	public function getRanking($start, $end) {
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
    public function findEmailById(array $ids) {

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
    public function getUserListForRemindLogin($day) {
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

    public function totalUserAndCount() {
        $sql = "SELECT count( * ) total_user , sum( `points` ) total_points FROM user";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetch();

    }

    //1,2,3 广告体验,购物返利,游戏广告,获得积分提醒
    public function getUserListForRemindPoint($day) {
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
    public function getUserListForRemindTotalPoint($start, $end) {
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

    public function addPointHistorySearch($start, $end, $category_type, $email, $user_id) {
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
                on a.id = b.user_id where a.delete_flag IS NULL OR a.delete_flag = 0 ".$sql2." order by b.date desc";
                //echo $sql;
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }

    public function findWenWenUsersForRemmindRegister($start, $end){
        $sql = "select id, email from user where is_from_wenwen = 2 and pwd IS NULL and register_date >= '$start' and register_date <= '$end'";
        //echo $sql;
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
    }
}
