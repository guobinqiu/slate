<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IsReadFile
 *
 * @ORM\Table(name="is_read_file")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\IsReadFileRepository")
 */
class IsReadFile
{
    public function __construct()
    {
        $this->createTime = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_name", type="string", length=250 )
     */
    private $csvFileName;

    /**
     * @var \DateTime
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
     * Set csvFileName
     *
     * @param string $csvFileName
     * @return IsReadFile
     */
    public function setCsvFileName($csvFileName)
    {
        $this->csvFileName = $csvFileName;

        return $this;
    }

    /**
     * Get csvFileName
     *
     * @return string
     */
    public function getCsvFileName()
    {
        return $this->csvFileName;
    }


    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return IsReadFile
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
