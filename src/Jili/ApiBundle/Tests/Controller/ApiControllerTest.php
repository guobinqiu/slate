<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class ApiControllerTest extends WebTestCase
{
    /**
     * todo: testing required update the schema & method to http post 
     */
    public function testIsEmailDuplicated()
    {
        $client = static::createClient();

        $data = array(
            array('chiangtor@gmail2.com', '0'),
            array('zchua9999@sina.cn', '1'),
        );

        $crawler = $client->request('POST', '/api/check/email' ,array('email'=>'chiangtor@gmail.com'));
        #$this->assertEquals(200, $client->getResponse()->getStatusCode());
        #$this->assertEquals('1', $client->getResponse()->getContent());


        foreach($data as $r) {
            $email = $r[0];
            $expected = $r[1];
            $crawler = $client->request('POST', '/api/check/email' ,array('email'=>$email));
        #    $this->assertEquals(200, $client->getResponse()->getStatusCode());
        #    $this->assertEquals($expected, $client->getResponse()->getContent());
        }
    }
}
