<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WenwenControllerTest extends WebTestCase
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
     * @group experience_advertisement
     */
    public function testeditExperienceAdvertisementAction()
    {
        $client = static :: createClient();
        $url = '/backend/editExperienceAdvertisement/1';
        
        $photo1 = array(
            'tmp_name' => '/tmp/test_photo1.jpg',
            'name' => 'test_photo.jpg',
            'type' => 'image/jpeg',
            'size' => 123,
            'error' => UPLOAD_ERR_OK
        );
        
        $photo2 = array(
            'tmp_name' => '/tmp/test_photo2.jpg',
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
            array('missionImgUrl'=>$photo)
                );

        $client->request('POST', $url, 
            array (
            'id' => '2',
            'missionTitle' => '测试任务2',
            'missionHall' => 2,
            'point' => 22),
            array('missionImgUrl'=>$photo)
                );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $em = $this->em;
        $record = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findAll();
        $this->assertCount(2, $record,'record count');
    }
    
    /**
     * @group experience_advertisement
     */
    public function testexperienceAdvertisementListAction()
    {
        $ea = new ExperienceAdvertisement();
        $em = $this->em;
        $client = static :: createClient();
        $path =  $this->container->getParameter('upload_experience_advertisement_dir');
        $ea->setMissionTitle('test');
        $ea->setMissionHall(1);
        $ea->setPoint(30);
        $ea->setDeleteFlag(0);
        $ea->setCreateTime(new \Datetime());
        $ea->setUpdateTime(new \Datetime());
        $em->persist($ea);
        $code = $ea->upload($path,$ea->getMissionHall());
        if(!$code){
            $em->flush();
        }

        $ea->setMissionTitle('test2');
        $ea->setMissionHall(2);
        $ea->setPoint(300);
        $ea->setDeleteFlag(0);
        $ea->setCreateTime(new \Datetime());
        $ea->setUpdateTime(new \Datetime());
        $em->persist($ea);
        $code = $ea->upload($path,$ea->getMissionHall());
        if(!$code){
            $em->flush();
        }
        
        $ea->setMissionTitle('test3');
        $ea->setMissionHall(3);
        $ea->setPoint(3100);
        $ea->setDeleteFlag(1);
        $ea->setCreateTime(new \Datetime());
        $ea->setUpdateTime(new \Datetime());
        $em->persist($ea);
        $code = $ea->upload($path,$ea->getMissionHall());
        if(!$code){
            $em->flush();
        }
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $record = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findAll();
        $this->assertCount(2, $record,'record count');
    }

}
