<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadApiDupEmailCodeData;

class CheckinControllerTest extends WebTestCase
{

    /**
     * @group issue_469
     * @group debug 
     */
    public function testUserCheckin()
    {

        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get('session');

        $url =  $container->get('router')->generate('_checkin_userCheckIn');
        $this->assertEquals('/checkin/userCheckin',$url);

        //  
        $client->request('GET', $url );
        $response =  $client->getResponse();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"statusCode":404,"userCheckin":null}'; //需要登录
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        $session->set('uid', 1);
        $session->save();

        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(405, $client->getResponse()->getStatusCode(),'check request status code ');

        $client->request('GET', $url  );
        $expected = '{"statusCode":400,"message":"\u8bf7\u6c42\u65b9\u6cd5\u4e0d\u5bf9"}'; //请求方法不对
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对, ajax required');

        // chekcin is done
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"userCheckin":0,"confirmPoints":0,"statusCode":200}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'false ');


        // chekcin is not done 
        $session->set('task_list.checkin_visit',1);
        $session->save();
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code');
        $this->assertEquals('{"userCheckin":1,"statusCode":200}', $client->getResponse()->getContent(),'1, 未签到');


        // checkin other status
        $session->set('task_list.checkin_visit',0);
        $session->save();
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code');
        $this->assertEquals('{"userCheckin":false,"statusCode":200}', $client->getResponse()->getContent(),'false, 已签到');
    }
}
