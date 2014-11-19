<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\FrontendBundle\Entity\GameSeekerDaily;

class GameSeekerDailyRepository extends EntityRepository 
{
    /**
     * insertIfNotExist/update the game_seek_daily
     * @param integer $uid user id 
     * @return GameSeekerDaily instance 
     */
    public function getInfoByUser($uid)
    {
        $em = $this->getEntityManager();
        $today_day = new \DateTime() ;
        $today_day->setTime(0,0);

        $gameSeekerDaily  = $this->findOneBy(array('userId'=> $uid, 'clickedDay'=> $today_day));

        if($gameSeekerDaily ) {
            $gameSeekerDaily->setToken();
        } else {
            $gameSeekerDaily  = new GameSeekerDaily();
            $gameSeekerDaily->setUserId($uid);
            $gameSeekerDaily->setToken();
        }
        $em->persist($gameSeekerDaily);
        $em->flush();
        return $gameSeekerDaily;
    }

}
