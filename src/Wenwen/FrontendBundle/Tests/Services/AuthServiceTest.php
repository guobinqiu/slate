<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\AuthEmail;


class AuthServiceTest extends WebTestCase
{
    private $container;
    private $em;
    private $authService;
    private $application;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->container = $container;
        $this->em = $em;
        $this->authService = $this->container->get('app.auth_service');
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        //$this->em->close();
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    public function testSendConfirmationEmail_failure_invalidparams() {

        $rtn = $this->authService->sendConfirmationEmail(null, 's', 'xx');

        $this->assertEquals('Invalid params.', $rtn['errMsg']);
        $this->assertEquals('failure', $rtn['status']);

        $rtn = $this->authService->sendConfirmationEmail('xxx', null, 's');

        $this->assertEquals('Invalid params.', $rtn['errMsg']);
        $this->assertEquals('failure', $rtn['status']);

        $rtn = $this->authService->sendConfirmationEmail('xxx', 's', null);

        $this->assertEquals('Invalid params.', $rtn['errMsg']);
        $this->assertEquals('failure', $rtn['status']);
    }

    public function testSendConfirmationEmail_failure_update() {
        $token = md5(uniqid(rand(), true));

        $email = 'failure_update@test.com';
        $original_token = 'xxx';

        $user = new User();
        $this->em->persist($user);

        $authEmail = new AuthEmail();
        $authEmail->setUser($user);
        $authEmail->setEmail($email);
        $authEmail->setToken($original_token);
        $authEmail->setUpdatedAt(new \DateTime('-59 Seconds'));

        $this->em->persist($authEmail);
        $this->em->flush();


        $rtn = $this->authService->sendConfirmationEmail($user->getId(), $email, $token);

        $this->assertEquals('Too many request for email confirmation', $rtn['errMsg']);
        $this->assertEquals('failure', $rtn['status']);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

        $this->assertEquals($original_token, $authEmail->getToken());
    }

    public function testSendConfirmationEmail_success_update() {
        $token = md5(uniqid(rand(), true));

        $email = 'success_update@test.com';
        $original_token = 'xxx';

        $user = new User();
        $this->em->persist($user);

        $authEmail = new AuthEmail();
        $authEmail->setUser($user);
        $authEmail->setEmail($email);
        $authEmail->setToken($original_token);
        $authEmail->setExpiredAt(new \DateTime('+23 hours'));
        $authEmail->setUpdatedAt(new \DateTime('-60 Seconds'));

        $this->em->persist($authEmail);

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $originalExpiredAt = $authEmail->getExpiredAt();
        $originalUpdatedAt = $authEmail->getUpdatedAt();



        $rtn = $this->authService->sendConfirmationEmail($user->getId(), $email, $token);

        $this->assertEquals('', $rtn['errMsg']);
        $this->assertEquals('success', $rtn['status']);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

        $this->assertNotEquals($original_token, $authEmail->getToken());
        $this->assertTrue($authEmail->getExpiredAt() > $originalExpiredAt);
    }

    public function testSendConfirmationEmail_success_create() {
        $token = md5(uniqid(rand(), true));

        $email = 'success_create@test.com';

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();


        $rtn = $this->authService->sendConfirmationEmail($user->getId(), $email, $token);

        $this->assertEquals('', $rtn['errMsg']);
        $this->assertEquals('success', $rtn['status']);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

        $diffInSeconds = $authEmail->getExpiredAt()->getTimestamp() - (new \DateTime())->getTimestamp();
        $this->assertTrue($diffInSeconds > 3600 * 24 - 10);
        $this->assertTrue($diffInSeconds <= 3600 * 24);
    }

    public function testSendConfirmationEmail_error() {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));
        $token = md5(uniqid(rand(), true));
        $email = 'error@test.com';
        $userId = 1234;


        $rtn = $this->authService->sendConfirmationEmail($userId, $email, $token);

        $this->assertEquals('error', $rtn['status']);
        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    public function testConfirmEmail_failure_invalidparams() {

        $rtn = $this->authService->confirmEmail(null);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Invalid params.', $rtn['errMsg']);
    }

    public function testConfirmEmail_failure_notexist() {
        $token = 'xxx';

        $rtn = $this->authService->confirmEmail($token);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('token not exist.', $rtn['errMsg']);
    }

    public function testConfirmEmail_failure_expired() {
        $token = 'xxx';
        $email = 'failure_expired@test.com';

        $user = new User();
        $this->em->persist($user);

        $authEmail = new AuthEmail();
        $authEmail->setUser($user);
        $authEmail->setEmail($email);
        $authEmail->setToken($token);
        $authEmail->setExpiredAt(new \DateTime('-1 seconds'));
        $this->em->persist($authEmail);
        $this->em->flush();

        $rtn = $this->authService->confirmEmail($token);

        $this->assertEquals('token expired.', $rtn['errMsg']);
        $this->assertEquals('failure', $rtn['status']);
    }

    public function testConfirmEmail_success() {
        $token = 'xxx';
        $email = 'success@test.com';

        $user = new User();
        $this->em->persist($user);

        $authEmail = new AuthEmail();
        $authEmail->setUser($user);
        $authEmail->setEmail($email);
        $authEmail->setToken($token);
        $authEmail->setExpiredAt(new \DateTime('+1 seconds'));
        $this->em->persist($authEmail);
        $this->em->flush();

        $rtn = $this->authService->confirmEmail($token);


        $this->assertEquals('success', $rtn['status']);
        $this->assertEquals($user->getId(), $rtn['userId']);

        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());

        $this->assertEquals(User::EMAIL_CONFIRMED, $user->getIsEmailConfirmed());
        $this->assertTrue($user->getRegisterCompleteDate() != null);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));
        $this->assertNull($authEmail);
    }

    public function testConfirmEmail_error() {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));
        $token = 'xxx';
        $email = 'error@test.com';


        $rtn = $this->authService->confirmEmail($token);

        $this->assertEquals('error', $rtn['status']);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }
}
