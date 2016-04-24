<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Table(name="vote")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\VoteRepository")
 */
class Vote
{

    const S_SIDE = 90;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime")
     */
    private $endTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_value", type="integer", nullable=true)
     */
    private $pointValue;

    /**
     * @var string
     *
     * @ORM\Column(name="stash_data", type="text", nullable=true)
     */
    private $stashData;

    /**
     * @var string
     *
     * @ORM\Column(name="vote_image", type="string", length=255, nullable=true)
     */
    private $voteImage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    private $src_image_path;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Vote
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Vote
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Vote
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Vote
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set pointValue
     *
     * @param integer $pointValue
     * @return Vote
     */
    public function setPointValue($pointValue)
    {
        $this->pointValue = $pointValue;

        return $this;
    }

    /**
     * Get pointValue
     *
     * @return integer
     */
    public function getPointValue()
    {
        return $this->pointValue;
    }

    /**
     * Set stashData
     *
     * @param array $stashData
     * @return Vote
     */
    public function setStashData($stashData)
    {
        $this->stashData = json_encode($stashData);

        return $this;
    }

    /**
     * Get stashData
     *
     * @return array
     */
    public function getStashData()
    {
        return json_decode($this->stashData, true);
    }

    /**
     * Set voteImage
     *
     * @param string $voteImage
     * @return Vote
     */
    public function setVoteImage($voteImage)
    {
        $this->voteImage = $voteImage;

        return $this;
    }

    /**
     * Get voteImage
     *
     * @return string
     */
    public function getVoteImage()
    {
        return $this->voteImage;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Vote
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Vote
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

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
     * Set id
     *
     * @param
     * @return Vote
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set sPath
     *
     * @param string $sPath
     * @return VoteImage
     */
    public function setSPath($sPath)
    {
        $this->sPath = $sPath;

        return $this;
    }

    /**
     * Get sPath
     *
     * @return string
     */
    public function getSPath()
    {
        return $this->sPath;
    }

    public function setSrcImagePath($src_image_path)
    {
        $this->src_image_path = $src_image_path;
    }

    public function setFile()
    {
        $this->setVoteImage(basename($this->src_image_path));
        // S file
        $s_file = $this->getDstImagePath('s');
        $this->setSPath($s_file);
    }

    public function getDstImagePath($suffix)
    {
        $path_info = pathinfo($this->src_image_path);
        // Moon.jpg -> Moon
        $src_image_name = basename($this->src_image_path, '.' . $path_info['extension']);

        preg_match('/^(.)(.)/', $src_image_name, $res);
        return sprintf('%s/%s/%s_%s.jpg', $res[1], $res[2], $src_image_name, $suffix);
    }
}
