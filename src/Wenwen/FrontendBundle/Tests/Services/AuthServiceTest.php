<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\AuthEmail;
use Wenwen\FrontendBundle\Entity\AuthRememberMe;
use Wenwen\FrontendBundle\Services\AuthService;



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

        // print 'memory_get_usage=' . memory_get_usage() . ' memory_get_peak_usage=' . memory_get_peak_usage() . PHP_EOL;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();

        // Do remember to clean up everything initialed at setUp
        $this->em = null;
        $this->container = null;
        $this->authService = null;
        $this->application = null;
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    /**
     * Invalid params test
     */
    public function testSendConfirmationEmail_failure_invalidparams() {

        $rtn = $this->authService->sendConfirmationEmail(null, 's', 'xx');

        $this->assertEquals(AuthService::MSG_INVALID_PARAMS, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);

        $rtn = $this->authService->sendConfirmationEmail('xxx', null, 's');

        $this->assertEquals(AuthService::MSG_INVALID_PARAMS, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);

        $rtn = $this->authService->sendConfirmationEmail('xxx', 's', null);

        $this->assertEquals(AuthService::MSG_INVALID_PARAMS, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Too frequent request for email confirmation
     */
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

        $this->assertEquals(AuthService::MSG_MALICIOUS_REQUEST, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

        $this->assertEquals($original_token, $authEmail->getToken());
    }


    /**
     * Success for request to confirm email again after 60 seconds.
     */
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

        $this->assertEquals(AuthService::MSG_TOKEN_UPDATED, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_SUCCESS, $rtn[AuthService::KEY_STATUS]);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

        $this->assertNotEquals($original_token, $authEmail->getToken());
        $this->assertEquals($token, $authEmail->getToken());
        $this->assertTrue($authEmail->getExpiredAt() > $originalExpiredAt);
    }

    /**
     * Success to request send a confirmation email
     */
    public function testSendConfirmationEmail_success_create() {
        $token = md5(uniqid(rand(), true));

        $email = 'success_create@test.com';

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();


        $rtn = $this->authService->sendConfirmationEmail($user->getId(), $email, $token);

        $this->assertEquals(AuthService::MSG_TOKEN_CREATED, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_SUCCESS, $rtn[AuthService::KEY_STATUS]);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

        $diffInSeconds = $authEmail->getExpiredAt()->getTimestamp() - (new \DateTime())->getTimestamp();
        $this->assertTrue($diffInSeconds > 3600 * 24 - 10);
        $this->assertTrue($diffInSeconds <= 3600 * 24);
    }

    /**
     * System error
     */
    public function testSendConfirmationEmail_error() {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));
        $token = md5(uniqid(rand(), true));
        $email = 'error@test.com';
        $userId = 1234;


        $rtn = $this->authService->sendConfirmationEmail($userId, $email, $token);

        $this->assertEquals(AuthService::STATUS_ERROR, $rtn[AuthService::KEY_STATUS]);
        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    /**
     * Invalid params
     */
    public function testConfirmEmail_failure_invalidparams() {

        $rtn = $this->authService->confirmEmail(null);

        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn['status']);
        $this->assertEquals(AuthService::MSG_INVALID_PARAMS, $rtn[AuthService::KEY_MESSAGE]);
    }

    /**
     * Not exist token
     */
    public function testConfirmEmail_failure_notexist() {
        $token = 'xxx';

        $rtn = $this->authService->confirmEmail($token);

        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
        $this->assertEquals(AuthService::MSG_TOKEN_NOTFOUND, $rtn[AuthService::KEY_MESSAGE]);
    }

    /**
     * Token expired
     */
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

        $this->assertEquals(AuthService::MSG_TOKEN_EXPIRED, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Confirmation success
     */
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


        $this->assertEquals(AuthService::STATUS_SUCCESS, $rtn[AuthService::KEY_STATUS]);
        $this->assertEquals($user->getId(), $rtn[AuthService::KEY_USERID]);

        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());

        $this->assertEquals(User::EMAIL_CONFIRMED, $user->getIsEmailConfirmed());
        $this->assertTrue($user->getRegisterCompleteDate() != null);

        $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));
        $this->assertNull($authEmail);
    }

    /**
     * System error
     */
    public function testConfirmEmail_error() {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));
        $token = 'xxx';
        $email = 'error@test.com';


        $rtn = $this->authService->confirmEmail($token);

        $this->assertEquals(AuthService::STATUS_ERROR, $rtn[AuthService::KEY_STATUS]);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    /**
     * Invalid params test
     */
    public function testGenerateRememberMeToken_failure_invalidparams() {

        $rtn = $this->authService->generateRememberMeToken(null);

        $this->assertEquals(AuthService::MSG_INVALID_PARAMS, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Invalid userId
     */
    public function testGenerateRememberMeToken_failure_usernotexist() {

        $rtn = $this->authService->generateRememberMeToken('not_exist_user_id');

        $this->assertEquals(AuthService::MSG_INVALID_USER, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Success on creating a token
     */
    public function testGenerateRememberMeToken_success_generate() {
        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $rtn = $this->authService->generateRememberMeToken($user->getId());

        $this->assertEquals(AuthService::STATUS_SUCCESS, $rtn[AuthService::KEY_STATUS]);

        $authRememberMe = $this->em->getRepository('WenwenFrontendBundle:AuthRememberMe')->findOneByUser($user);

        $this->assertNotNull($authRememberMe);
        $this->assertEquals($rtn[AuthService::KEY_TOKEN], $authRememberMe->getToken());
        $this->assertEquals($rtn[AuthService::KEY_EXPIREDAT], $authRememberMe->getExpiredAt());
    }

    /**
     * Success on updating a token
     */
    public function testGenerateRememberMeToken_success_update() {
        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $originalToken = md5(uniqid(rand(), true));
        $originalExpiredAt = new \DateTime('+ 30 days');
        $authRememberMe = new AuthRememberMe();
        $authRememberMe->setUser($user);
        $authRememberMe->setToken($originalToken);
        $authRememberMe->setExpiredAt($originalExpiredAt);
        $this->em->persist($authRememberMe);
        $this->em->flush();

        $rtn = $this->authService->generateRememberMeToken($user->getId());

        $this->assertEquals(AuthService::STATUS_SUCCESS, $rtn[AuthService::KEY_STATUS]);

        $authRememberMe = $this->em->getRepository('WenwenFrontendBundle:AuthRememberMe')->findOneByUser($user);

        $this->assertNotNull($authRememberMe);
        $this->assertNotEquals($originalToken, $rtn[AuthService::KEY_TOKEN]);
        $this->assertEquals($rtn[AuthService::KEY_TOKEN], $authRememberMe->getToken());
        $this->assertEquals($rtn[AuthService::KEY_EXPIREDAT], $authRememberMe->getExpiredAt());
    }

    /**
     * System error
     */
    public function testGenerateRememberMeToken_error() {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $rtn = $this->authService->generateRememberMeToken(1);

        $this->assertEquals(AuthService::STATUS_ERROR, $rtn[AuthService::KEY_STATUS]);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    /**
     * Invalid params test
     */
    public function testFindRememberMeToken_failure_invalidparams() {

        $rtn = $this->authService->findRememberMeToken(null);

        $this->assertEquals(AuthService::MSG_INVALID_PARAMS, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Token not found
     */
    public function testFindRememberMeToken_failure_tokennotfound() {

        $rtn = $this->authService->findRememberMeToken('not_exist_token');

        $this->assertEquals(AuthService::MSG_TOKEN_NOTFOUND, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Token expired
     */
    public function testFindRememberMeToken_failure_tokenexpired() {

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $originalToken = md5(uniqid(rand(), true));
        $originalExpiredAt = new \DateTime('- 1 seconds');
        $authRememberMe = new AuthRememberMe();
        $authRememberMe->setUser($user);
        $authRememberMe->setToken($originalToken);
        $authRememberMe->setExpiredAt($originalExpiredAt);
        $this->em->persist($authRememberMe);
        $this->em->flush();

        $rtn = $this->authService->findRememberMeToken($originalToken);

        $this->assertEquals(AuthService::MSG_TOKEN_EXPIRED, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_FAILURE, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * Token found
     */
    public function testFindRememberMeToken_success_tokenfound() {

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $originalToken = md5(uniqid(rand(), true));
        $originalExpiredAt = new \DateTime('+ 1 seconds');
        $authRememberMe = new AuthRememberMe();
        $authRememberMe->setUser($user);
        $authRememberMe->setToken($originalToken);
        $authRememberMe->setExpiredAt($originalExpiredAt);
        $this->em->persist($authRememberMe);
        $this->em->flush();

        $rtn = $this->authService->findRememberMeToken($originalToken);

        $this->assertEquals(AuthService::MSG_TOKEN_FOUND, $rtn[AuthService::KEY_MESSAGE]);
        $this->assertEquals(AuthService::STATUS_SUCCESS, $rtn[AuthService::KEY_STATUS]);
    }

    /**
     * error
     */
    public function testFindRememberMeToken_error() {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $rtn = $this->authService->findRememberMeToken(1);

        $this->assertEquals(AuthService::STATUS_ERROR, $rtn[AuthService::KEY_STATUS]);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

}
