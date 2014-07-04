<?php
namespace Jili\ApiBundle\Tests\Utility;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\ApiBundle\Controller\UserController;

class SendMailBySoapTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

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
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    public function testSendMailBySoap() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new UserController();
        $controller->setContainer($container);

        $email = "zhangmm@voyagegroup.com.cn";
        $code = "testcode111";
        $user = $this->em->getRepository('JiliApiBundle:User')->findByEmail($email);
        $return = $controller->sendMailBySoap($user[0],$code);
        $this->assertTrue($return);
    }
}
