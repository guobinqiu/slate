<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\TaoBaoUser;

class TaoBaoUserRepository extends EntityRepository
{
    /**
     * create the user when regist by taobao
     * @param  array('user_id'=> , 'open_id'=>);
     * @return the Taobaouser
     */
    public function taobao_user_insert(array $param)
    {
        $taobao_user =  new TaoBaoUser;
        //var_dump($taobao_user);exit;
        $taobao_user->setUserId($param['user_id']);
        $taobao_user->setOpenId($param['open_id']);
        $taobao_user->setRegistDate(new \Datetime());
        $em = $this->getEntityManager();
        $em->persist($taobao_user);
        $em->flush();
        return $taobao_user;
    }
}
