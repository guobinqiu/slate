<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserSetPasswordCodeData;
use Jili\ApiBundle\Entity\SetPasswordCode;

class SetPasswordCodeRepositoryTest extends KernelTestCase
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
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container = static::$kernel->getContainer();

        $tn  = $this->getName();
        if( in_array($tn, array('testFindOneValidateSignUpToken','testIsAvailableFindOneValidateSignUpToken','testCreateTimeFindOneValidateSignUpToken')) ) {

            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

            // load fixtures
            $fixture = new LoadUserSetPasswordCodeData();
            $fixture->setContainer($container);

            $loader = new Loader();
            $loader->addFixture($fixture);

            $executor->execute($loader->getFixtures());
        }

        $this->em  = $em;
        $this->container  = $container;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @group set_password_code_repository
     * @group issue_381
     */
    public function testFindOneValidateSignUpToken()
    {
        $em = $this->em;

        // a validate test
        $params = array(
            'user_id'=> LoadUserSetPasswordCodeData::$USER[0]->getId(),
            'token'=>  LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[0]->getCode(),
        );

        $result = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);
        $this->assertNotNull( $result);
        $this->assertEquals(LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[0]->getId() ,  $result->getId() ) ;


        $params = array(
            'user_id'=> 11111,
            'token'=>  '71b1b99cfbbb75c363300f051f5c57af',
        );
        $result = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);
        $this->assertNull(  $result);

        $params = array(
            'user_id'=> null,
            'token'=>  '',
        );
        $result = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);

        $this->assertNull(  $result);

        $params = array(
            'user_id'=> null,
            'token'=>  '',
        );

        $result = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);
        $this->assertNull(  $result);
    }

    /**
     * @group set_password_code_repository
     * @group issue_381
     */
    public function testIsAvailableFindOneValidateSignUpToken()
    {
        // invalid is_available
        $params = array(
            'user_id'=> LoadUserSetPasswordCodeData::$USER[2]->getId(),
            'token'=>  LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[2]->getCode(),
        );
        $result = $this->em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);
        $this->assertNull(  $result);
    }
    /**
     * @group set_password_code_repository
     * @group issue_381
     **/
    public function testCreateTimeFindOneValidateSignUpToken()
    {
        // invalid create_time
        $params = array(
            'user_id'=> LoadUserSetPasswordCodeData::$USER[1]->getId(),
            'token'=>  LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[1]->getCode(),
        );
        $result = $this->em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);
        $this->assertNull(  $result);
    }

    /**
     * @group issue_448
     **/
    public function testCreate() 
    {
        $em = $this->em;
        $user_id = 1;
        $param = array('user_id'=> $user_id);
        $em->getRepository('JiliApiBundle:SetPasswordCode')->create($param );

        $query = array(
            'isAvailable' => 1,
            'userId'=> $user_id
        );

        $r = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneBy($query);
        $this->assertNotNull($r);


    }
}
