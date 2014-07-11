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
    public function  findOneValidateSignUpToken($params )
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
}
