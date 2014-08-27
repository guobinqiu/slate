<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Jili\ApiBundle\Entity\SetPasswordCode;
class SetPasswordCodeRepository extends EntityRepository
{
    /**
     * @param $param  array( 'userId'=> ,'code'=>);
     * @return
     */
    public function findOneValidateSignUpToken($params)
    {
        extract($params);

        $qb = $this->createQueryBuilder('a');
        $qb->where( $qb->expr()->eq('a.isAvailable', 1 )  )
            ->AndWhere( 'a.userId = :userId'  )
            ->AndWhere( 'a.code = :code'  )
            ->AndWhere(' a.createTime >=  :min_created_at ' )
            ->setParameters( array(
                'userId'=> $user_id,
                'code'=> $token,
                'min_created_at'=> date('Y-m-d H:i:s', time() - SetPasswordCode::$VALIDATION_OF_SIGNUP_ACTIVATE  ) )
            );

        $q = $qb->getQuery();
        return $q->getOneOrNullResult();
    }

    /**
     * @param $param  array('user_id' )
     * @return the SetPasswordCode 
     */
    public function create($param) 
    {
        $user_id = $param['user_id'];
        $str = 'jilifirstregister';
        $code = md5($user_id.str_shuffle($str));
        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user_id);
        $setPasswordCode->setCode($code);
        $setPasswordCode->setCreateTime( new \Datetime());
        $setPasswordCode->setIsAvailable(1);
        $em = $this->getEntityManager();
        $em->persist($setPasswordCode);
        $em->flush();
        return $setPasswordCode;
    }
}
