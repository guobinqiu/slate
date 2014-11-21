<?php
namespace Jili\BackendBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GameSeekerRules extends Constraint
{
    public $message = '%string% 格式出错, 参考123:32 ';
}
