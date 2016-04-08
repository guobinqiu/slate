<?php
namespace Wenwen\AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Wenwen\AppBundle\Entity\UserDeleted;

class WithdrawHandler
{
    private $em;
    private $logger;

    public function __construct()
    {
    }

    public function doWithdraw($user_id, $reason)
    {
        $user = $this->em->getRepository('JiliApiBundle:User')->find($user_id);

        if (!$user) {
            $this->logger->warn('user withdraw: user is not defined');
            return false;
        }

        $this->em->getConnection()->beginTransaction();

        try {

            $user_arr = $this->em->getRepository('JiliApiBundle:User')->getUserInfoArray($user_id);
            $user_info = json_encode($user_arr );

            // insert user_deleted
            $user_deleted = new UserDeleted();
            $user_deleted->setUserId($user_id);
            $user_deleted->setReason($reason);
            $user_deleted->setUserInfo($user_info);
            $this->em->persist($user_deleted);
            $this->em->flush();

            //delete user
            $this->em->remove($user);
            $this->em->flush();

            $this->em->getConnection()->commit();

            return true;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            $this->em->close();

            $this->logger->crit('user withdraw fail:' . $e->getMessage());

            return false;
        }
    }

    public function getUserJson($user){
        return json_encode($user);
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}