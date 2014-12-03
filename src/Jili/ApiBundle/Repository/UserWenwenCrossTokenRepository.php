<?php
namespace   Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\UserWenwenCrossToken;
use Jili\ApiBundle\Utility\WenwenToken;

class UserWenwenCrossTokenRepository extends EntityRepository {

    public function create($cross_id) {
        $em = $this->getEntityManager();
        $token = WenwenToken :: generateOnetimeToken();

        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->findOneByCrossId($cross_id);
        if(!$crossToken){
            $crossToken = new UserWenwenCrossToken();
            $crossToken->setCrossId($cross_id);
        }
        $crossToken->setToken($token);
        $em->persist($crossToken);
        $em->flush();
        return $crossToken;
    }

    public function delete($cross_id) {
        $em = $this->getEntityManager();
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->findOneByCrossId($cross_id);
        if ($crossToken) {
            $em->remove($crossToken);
            $em->flush();
            return true;
        }
        return false;
    }
}