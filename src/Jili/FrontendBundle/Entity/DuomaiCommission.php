<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuomaiCommission
 *
 * @ORM\Table(name="duomai_commission", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GeneralCommissionRepository")
 */
class DuomaiCommission extends CommissionBase
{
}
