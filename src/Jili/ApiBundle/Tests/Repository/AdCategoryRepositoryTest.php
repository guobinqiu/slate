<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdCategoryRepositoryTest extends WebTestCase
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
     * @group issue373
     */
    public function testGetCategoryList() 
    {

        $client = static::createClient();
        $container = $client->getContainer();

        $hiddens = $container->get('doctrine')->getEntityManager()->getRepository('JiliApiBundle:AdCategory')
            ->findBy( array('isHidden'=> 1)  );

#        var_dump($hiddens );

        $em = $this->em;
        $repository = $this->em->getRepository('JiliApiBundle:AdCategory');
        $list = $repository->getListToDisplay();

        foreach( $list as $i ) {
            $this->assertNotContains( $hiddens, $list,  'Display category should not include hidden ones.') ;
        }

#        var_dump($list);
        $a = array(
             array('a','z')
        );

        $b = array(
            array('a','z'),
            array('a','x'),
            array('a','y'),
        );

        $this->assertContains( $a, $b, 'test the assertContains');

    }
}

