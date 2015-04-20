<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarCommission
 *
 * @ORM\Table(name="emar_commission", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GeneralCommissionRepository")
 */
class EmarCommission extends CommissionBase
{
}
