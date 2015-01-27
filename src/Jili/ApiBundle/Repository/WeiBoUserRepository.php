<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\WeiBoUser;
class WeiBoUserRepository extends EntityRepository
{
    /**
    * create the user when regist by weibo
    * @param array('user_id'=> , 'open_id'=>);
    * @return the WeiBouser
    */
    public function weibo_user_insert(array $param)
    {
    $weibo_user = new WeiBoUser;
    $weibo_user->setUserId($param['user_id']);
    $weibo_user->setOpenId($param['open_id']);
    $em = $this->getEntityManager();
    $em->persist($weibo_user);
    $em->flush();
    return $weibo_user;
    }
}