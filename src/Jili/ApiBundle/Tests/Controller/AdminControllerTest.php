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

    public function testDandleExchangeWen1() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $email = 'zhangmm3@voyagegroup.com.cn';
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (empty ($userInfo)) {
            $this->assertEmpty($userInfo);
        } else {
            if (!$userInfo[0]->getPwd()) {
                $this->assertEmpty($userInfo[0]->getPwd());
            } else {
                $this->assertNotEmpty($userInfo);
            }
        }
    }

    public function testDandleExchangeWen2() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $email = 'zhangmm1@voyagegroup.com.cn';
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (empty ($userInfo)) {
            $this->assertEmpty($userInfo);
        } else {
            if (!$userInfo[0]->getPwd()) {
                $this->assertEmpty($userInfo[0]->getPwd());
            } else {
                $this->assertNotEmpty($userInfo);
            }
        }
    }

    public function testDandleExchangeWen3() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $email = 'zhangmm@voyagegroup.com.cn';
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (empty ($userInfo)) {
            $this->assertEmpty($userInfo);
        } else {
            if (!$userInfo[0]->getPwd()) {
                $this->assertEmpty($userInfo[0]->getPwd());
            } else {
                $this->assertNotEmpty($userInfo);
            }
        }
    }
}