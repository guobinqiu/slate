<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase {

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $this->em = $em;
    }

    /**
     * todo: testing required update the schema & method to http post
     */
    public function testDandleExchangeWen1() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $email = 'zhangmm3@voyagegroup.com.cn';
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (empty ($userInfo)) {
            echo 'account not exists';
        }
        elseif (!$userInfo[0]->getPwd()) {
            echo 'password is null';
        } else {
            echo 'error account';
        }
    }

    /**
     * todo: testing required update the schema & method to http post
     */
    public function testDandleExchangeWen2() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $email = 'zhangmm1@voyagegroup.com.cn';
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (empty ($userInfo)) {
            echo 'account not exists';
        }
        elseif (!$userInfo[0]->getPwd()) {
            echo 'password is null';
        } else {
            echo 'error account';
        }
    }

    /**
     * todo: testing required update the schema & method to http post
     */
    public function testDandleExchangeWen3() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $email = 'zhangmm@voyagegroup.com.cn';
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (empty ($userInfo)) {
            echo 'account not exists';
        }
        elseif (!$userInfo[0]->getPwd()) {
            echo 'password is null';
        } else {
            echo 'right account';
        }
    }
}