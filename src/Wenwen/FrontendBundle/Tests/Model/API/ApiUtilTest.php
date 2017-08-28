<?php

namespace Wenwen\FrontendBundle\Tests\Model\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\Model\API\ApiUtil;

class ApiUtilTest extends WebTestCase
{
    public function testObjectToArray()
    {
        $a = [1, 2, 3];
        $b = [4, 5, 6];
        $c = [ 'name' => 'dataspring', 'age' => 10 ];
        $a[] = $b;
        $a[] = $c;
        //        print_r($a);

        echo ApiUtil::objectToJSON($a);
    }
}