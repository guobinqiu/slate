<?php

namespace Jili\EmarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello/Fabien');

//        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }
    // todo:
    // test redirect 
    // 2. with APIMemberId
    // http://jili0129.vgc.net/app_dev.php/emar/default/redirect?m=http%3A%2F%2Fp.yiqifa.com%2Fn%3Fk%3D6yUFMPAPrI6HWN3LrI6HCZg7Rnu_fOy7M57dC9Dd3OoVfltqWEDl6N2sWnzdCZgVYZLErI6HWEK7rnRlWE2L6ZLLrI6HYmLErJ6y6lRF1J2LrIW-%26e%3DAPIMemberId%26spm%3D139597428026718017.1.1.1
    //
    // 2. with userid
    // http://jili0129.vgc.net/app_dev.php/emar/default/redirect?m=http%3A%2F%2Fp.yiqifa.com%2Fn%3Fk%3D6yUFMPAPrI6HWN3LrI6HCZg7Rnu_fOy7M57dC9Dd3OoVfltqWEDl6N2sWnzdCZgVYZLErI6HWEK7rnRlWE2L6ZLLrI6HYmLErJ6y6lRF1J2LrIW-%26e%3DAPIMemberId%26spm%3D139597428026718017.1.1.1
}
