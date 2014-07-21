<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class WenwenControllerTest extends WebTestCase
{
    /**
     * @group user
     */
    public function test91wenwenRegister()
    {
        $client = static :: createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register');
        echo $client->getResponse()->getStatusCode(), PHP_EOL;
        echo $client->getResponse()->getContent(), PHP_EOL;
    }

    /**
     * @group user
     */
    public function test91wenwenRegister1()
    {
        $client = static :: createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register', array (
            'email' => '',
            'signature' => '',
            'uniqkey' => ''
        ));
        echo $client->getResponse()->getStatusCode(), PHP_EOL;
        echo $client->getResponse()->getContent(), PHP_EOL;
    }

    /**
     * @group user
     */
    public function test91wenwenRegister2()
    {
        $client = static :: createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register', array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '',
            'uniqkey' => ''
        ));
        echo $client->getResponse()->getStatusCode(), PHP_EOL;
        echo $client->getResponse()->getContent(), PHP_EOL;
    }

    /**
     * @group user
     */
    public function test91wenwenRegister3()
    {
        $client = static :: createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register', array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '11',
            'uniqkey' => ''
        ));
        echo $client->getResponse()->getStatusCode(), PHP_EOL;
        echo $client->getResponse()->getContent(), PHP_EOL;
    }

    /**
     * @group user
     */
    public function test91wenwenRegister4()
    {
        $client = static :: createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register', array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '11',
            'uniqkey' => 'test'
        ));
        echo $client->getResponse()->getStatusCode(), PHP_EOL;
        echo $client->getResponse()->getContent(), PHP_EOL;
    }

    /**
     * @group user
     */
    public function test91wenwenRegister5()
    {
        $client = static :: createClient();
        $crawler = $client->request('POST', '/api/91wenwen/register', array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '88ed4ef124e926ea1df1ea6cdddf8377771327ab',
            'uniqkey' => 'test'
        ));
        echo $client->getResponse()->getStatusCode(), PHP_EOL;
        echo $client->getResponse()->getContent(), PHP_EOL;
    }
}
