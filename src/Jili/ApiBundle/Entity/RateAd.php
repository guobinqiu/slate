<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RateAd
 *
 * @ORM\Table(name="rate_ad")
 * @ORM\Entity
 */
class RateAd
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $adId;

    /**
     * @var string
     *
     * @ORM\Column(name="incentive_rate", type="string", length=45, nullable=true)
     */
    private $incentiveRate;


}
