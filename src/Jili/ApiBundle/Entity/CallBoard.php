<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CallBoard
 */
class CallBoard
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
     */
    private $title;

    /**
     * @var varchar
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $start_time;

    /**
     * @var \DateTime
     */
    private $end_time;

    /**
     * @var varchar
     */
    private $url;


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
     * Set title
     *
     * @param \varchar $title
     * @return CallBoard
     */
    public function setTitle(\varchar $title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return \varchar 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param \varchar $content
     * @return CallBoard
     */
    public function setContent(\varchar $content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return \varchar 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return CallBoard
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;
    
        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     * @return CallBoard
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;
    
        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set url
     *
     * @param \varchar $url
     * @return CallBoard
     */
    public function setUrl(\varchar $url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return \varchar 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
