<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


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
        $this->em  = $em;
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
            'user_id'=> 1120386, 
            'token'=>  '71b1b99cfbbb75c363300f051f5c57af',
        );
        $result = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken($params);
        $this->assertEquals( 16631 , $result->getId());


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
     * @group debug 
     **/
    public function testCreateTimeFindOneValidateSignUpToken() 
    {
        $em = $this->em;

        $this->assertEquals(1,'1');
    }
}
