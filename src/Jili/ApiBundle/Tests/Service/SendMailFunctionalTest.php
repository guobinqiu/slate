<?php
namespace Jili\ApiBundle\Tests\Service;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendMailFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    public function testsendMailForRegisterFromWenwen()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $send_mail = $container->get('send_mail');

        $email = 'zhangmm@voyagegroup.com.cn';
        $url = 'https://localhost/PointMedia/web/app_dev.php/user/setPassFromWenwen/11fe83aa9baac88ce489967a6d0cf0bb/1057703';
        $result = $send_mail->sendMailForRegisterFromWenwen($email, $url);
        $this->assertTrue($result);
    }
}
