<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;
use Jili\ApiBundle\Entity\SendMessage;

class SendMessageRepository extends EntityRepository
{
    public function getSendMs()
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('sm.id,sm.sendFrom,sm.sendTo,sm.title,sm.content,sm.createtime,sm.readFlag,sm.deleteFlag,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id ');
        $query = $query->Where('sm.deleteFlag = 0 ');
        $query = $query->orderBy('sm.createtime','DESC');
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function getUserSendMs($id)
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('sm.id,sm.sendFrom,sm.sendTo,sm.title,sm.content,sm.createtime,sm.readFlag,sm.deleteFlag,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id ');
        $query = $query->Where('sm.id = :id');
        $query = $query->setParameter('id',$id);
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function getSendMsById($uid)
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('sm.id,sm.sendFrom,sm.sendTo,sm.title,sm.content,sm.createtime,sm.readFlag,sm.deleteFlag,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id');
        $query = $query->Where('sm.sendTo = :uid');
        $query = $query->andWhere('sm.deleteFlag = 0 ');
        $query = $query->orderBy('sm.createtime','DESC');
        $query = $query->setParameter('uid',$uid);
        $query =  $query->getQuery();
        return $query->getResult();
    }


    public function CountSendMs($uid)
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('count(sm.id) as num');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id');
        $query = $query->Where('sm.sendTo = :uid');
        $query = $query->andWhere('sm.readFlag = 0 ');
        $query = $query->andWhere('sm.deleteFlag = 0 ');
        $query = $query->setParameter('uid',$uid);
        $query =  $query->getQuery();
        return $query->getResult();
    }


    /**
     * @param array $params
     *     $params = array(
     *             'userid' => $uid,
     *             'title' => $title,
     *             'content' => $content
     *           );
     */
    public function insertSendMs($params=array())
    {
        $em = $this->getEntityManager();

  //      $tableName = $em->getClassMetadata('JiliApiBundle:SendMessage')->getTableName();
 //       $tableName .= '0'.( $params['userid'] % 10 );

//        $this->setDataTableName($em, array('name'=>$tableName));
        $sm = SequenseEntityClassFactory :: createInstance('SendMessage', $params['userid']);
        $sm->setSendTo( $params['userid'])
            ->setTitle($params['title'])
            ->setContent($params['content']);

//        $sm->setSendFrom($this->container->getParameter('init'));
        //$sm->setReadFlag($this->container->getParameter('init'));
        //$sm->setDeleteFlag($this->container->getParameter('init'));

        $em->persist($sm);
        $em->flush();
    }

}
