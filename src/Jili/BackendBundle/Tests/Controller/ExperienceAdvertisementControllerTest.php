<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadExperienceAdvertisementCodeData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Jili\FrontendBundle\Entity\ExperienceAdvertisement;

class ExperienceAdvertisementControllerTest extends WebTestCase
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
        $container  = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadExperienceAdvertisementCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->purge();
        $executor->execute($loader->getFixtures());
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
     * @group experience_advertisement
     * @group issue430
     */
    public function testeditExperienceAdvertisementAction()
    {
        $client = static :: createClient();
        $url = '/backend/editExperienceAdvertisement/1';
        $root_dir = static::$kernel->getContainer()->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures';
        
        $photo1 = array(
            'tmp_name' => $fixture_dir.'/test_photo1.jpg',
            'name' => 'test_photo.jpg',
            'type' => 'image/jpeg',
            'size' => 123,
            'error' => UPLOAD_ERR_OK
        );
        
        $photo2 = array(
            'tmp_name' => $fixture_dir.'/test_photo2.jpg',
            'name' => 'test_photo.jpg',
            'type' => 'image/jpeg',
            'size' => 123,
            'error' => UPLOAD_ERR_OK
        );
        
        $client->request('POST', $url, 
            array (
            'id' => '1',
            'missionTitle' => '测试任务1',
            'missionHall' => 1,
            'point' => 11),
            array('missionImgUrl'=>$photo1)
                );

        $client->request('POST', $url, 
            array (
            'id' => '2',
            'missionTitle' => '测试任务2',
            'missionHall' => 2,
            'point' => 22),
            array('missionImgUrl'=>$photo2)
                );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $em = $this->em;
        $record = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findAll();
        $this->assertCount(4, $record,'record count');
    }
    
    /**
     * @group issue430
     */
    public function testexperienceAdvertisementListAction()
    {
        $ea = new ExperienceAdvertisement();
        $em = $this->em;
        $url = 'backend/ExperienceAdvertisementList';
        $client = static :: createClient();
        $client->request('GET',$url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), '' . $url);
        $record = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findAll();
        $this->assertCount(4, $record,'record count');
    }

}
