<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChanetCommission
 *
 * @ORM\Table(name="chanet_commission", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GeneralCommissionRepository")
 */
class ChanetCommission extends CommissionBase
{
}
