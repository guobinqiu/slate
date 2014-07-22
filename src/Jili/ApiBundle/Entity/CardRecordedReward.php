<?php

namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CardRecordedReward
 *
 * @ORM\Table(name="card_recorded_reward")
 * @ORM\Entity
 */
class CardRecordedReward
{
    public function __construct()
    {
        $this->createTime = new \DateTime();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="match_id",  type="integer", nullable=false)
     */
    private $matchId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id",  type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="reward_count",  type="integer")
     */
    private $rewardCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="reward_point",  type="integer")
     */
    private $rewardPoint;


    /**
     * @var datetime $createTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set matchId
     *
     * @param integer $matchId
     * @return CardRecordedReward
     */
    public function setMatchId($matchId)
    {
        $this->matchId = $matchId;

        return $this;
    }



    /**
     * Get matchId
     *
     * @return integer
     */
    public function getMatchId()
    {
        return $this->matchId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return CardRecordedReward
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }



    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set rewardCount
     *
     * @param integer $rewardCount
     * @return CardRecordedReward
     */
    public function setRewardCount($rewardCount)
    {
        $this->rewardCount = $rewardCount;

        return $this;
    }



    /**
     * Get rewardCount
     *
     * @return integer
     */
    public function getRewardCount()
    {
        return $this->rewardCount;
    }

    /**
     * Set rewardPoint
     *
     * @param integer $rewardPoint
     * @return CardRecordedReward
     */
    public function setRewardPoint($rewardPoint)
    {
        $this->rewardPoint = $rewardPoint;

        return $this;
    }



    /**
     * Get rewardPoint
     *
     * @return integer
     */
    public function getRewardPoint()
    {
        return $this->rewardPoint;
    }

     /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CardRecordedReward
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }


}
