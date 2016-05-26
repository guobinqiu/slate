<?php
namespace Jili\ApiBundle\Tests\Service;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendMailFunctionalTest extends WebTestCase {

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

    /**
     * @group issue_578
     */
    public function testSendMails() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $send_mail = $container->get('send_mail');

        $email = 'miaomiao.zhang@d8aspring.com';
        $result = $send_mail->sendMails('mail_subject', $email, "cont");
        $this->assertTrue($result);
    }
}