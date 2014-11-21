<?php
namespace Jili\BackendBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GameSeekerRules extends Constraint
{
    public $message = '%string% 格式出错, <pre>123:32</pre> or <pre>123:456:678,78</pre> ';

// 'The string "%string%" contains an illegal.Allows string follow the format <pre>123:32</pre> or <pre>123:456:678,78</pre> for every line';


}
