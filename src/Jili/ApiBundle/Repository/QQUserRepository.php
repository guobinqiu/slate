<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\QQUser;

class QQUserRepository extends EntityRepository
{
    /**
     * create the user when regist by qq
     * @param  array('user_id'=> , 'open_id'=>);
     * @return the qquser
     */
    public function qquser_insert(array $param)
    {
        $qquser =  new QQUser;
        $qquser->setUserId($param['user_id']);
        $qquser->setOpenId($param['open_id']);
        $em = $this->getEntityManager();
        $em->persist($qquser);
        $em->flush();
        return $qquser;
    }
}
