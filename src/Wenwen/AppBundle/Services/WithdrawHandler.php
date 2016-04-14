<?php
namespace Wenwen\AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Wenwen\AppBundle\Entity\UserWithdraw;

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

        $db_connection = $this->em->getConnection();
        $db_connection->beginTransaction();

        try {
            // insert user_withdraw
            $user_withdraw = new UserWithdraw();
            $user_withdraw->setUserId($user_id);
            $user_withdraw->setReason($reason);
            $this->em->persist($user_withdraw);
            $this->em->flush();

            // insert user_deleted
            $sth = $db_connection->prepare('INSERT INTO `user_deleted` SELECT * FROM `user` WHERE id =' . $user->getId());
            $sth->execute();

            $wenwenLogin = $this->em->getRepository('JiliApiBundle:UserWenwenLogin')->findOneByUser($user);
            if ($wenwenLogin) {
                // insert user_wenwen_login_deleted
                $sth = $db_connection->prepare('INSERT INTO `user_wenwen_login_deleted` SELECT * FROM `user_wenwen_login` WHERE user_id =' . $user->getId());
                $sth->execute();

                //delete user_wenwen_login
                $wenwenLogin = $this->em->getRepository('JiliApiBundle:UserWenwenLogin')->findOneByUser($user);
                if ($wenwenLogin) {
                    $this->em->remove($wenwenLogin);
                    $this->em->flush();
                }
            }

            //delete user
            $this->em->remove($user);
            $this->em->flush();

            $db_connection->commit();

            return true;
        } catch (\Exception $e) {
            $db_connection->rollback();
            $this->em->close();

            $this->logger->crit('user withdraw fail:' . $e->getMessage());

            return false;
        }
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
