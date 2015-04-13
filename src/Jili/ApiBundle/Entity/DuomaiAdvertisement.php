<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuomaiAdvertisement
 *
 * @ORM\Table(name="duomai_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity
 */
class DuomaiAdvertisement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_name", type="string", length=64, precision=0, scale=0, nullable=false, unique=false)
     */
    private $adsName;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, precision=0, scale=0, nullable=false, unique=false)
     */
    private $adsUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_commission", type="string", length=64, precision=0, scale=0, nullable=false, unique=false)
     */
    private $adsCommission;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="date", precision=0, scale=0, nullable=false, unique=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="date", precision=0, scale=0, nullable=false, unique=false)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=128, precision=0, scale=0, nullable=false, unique=false)
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="return_day", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $returnDay;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_cycle", type="string", length=64, precision=0, scale=0, nullable=false, unique=false)
     */
    private $billingCycle;

    /**
     * @var string
     *
     * @ORM\Column(name="link_custom", type="string", length=128, precision=0, scale=0, nullable=false, unique=false)
     */
    private $linkCustom;

    /**
     * @var string
     *
     * @ORM\Column(name="link_custom_short", type="string", length=128, precision=0, scale=0, nullable=false, unique=false)
     */
    private $linkCustomShort;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fixedHash;

    /**
     * @var string
     *
     * @ORM\Column(name="is_activated", type="string", length=32, precision=0, scale=0, nullable=false, unique=false)
     */
    private $isActivated;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}
