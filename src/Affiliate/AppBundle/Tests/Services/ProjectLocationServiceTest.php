<?php

namespace Affiliate\AppBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\ProvinceList;
use Wenwen\FrontendBundle\Entity\CityList;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliatePartner;

class ProjectLocationServiceTest extends WebTestCase
{

    private $em;

    private $projectLocationService;


    /**
     * {@inheritDoc}
     */
    public function setUp() {   
        static::$kernel = static::createKernel();
        static::$kernel->boot(); 
        $this->projectLocationService = static::$kernel->getContainer()->get('app.af_location_service');
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
    }
 
 
    public function testCheckInputLocation_true_normal() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $affiliateProject->setProvince('河南省');
        $affiliateProject->setCity('上海市');
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'上海','上海市');
        $rtn = $this->projectLocationService->confirmLocation('xxx.xxx.xxx.xxx', $affiliateProject->getId());
        
        $this->assertEquals(true, $rtn, 'should be true');

    }

    public function testCheckInputLocation_false_normal() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $affiliateProject->setProvince('河南省');
        $affiliateProject->setCity('广州市');
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'上海','上海市');
        $rtn = $this->projectLocationService->confirmLocation('xxx.xxx.xxx.xxx', $affiliateProject->getId());
        
        $this->assertEquals(false, $rtn, 'should be true');

    }

    public function testCheckInputLocation_true_no_restriction() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'上海','上海市');
        $rtn = $this->projectLocationService->confirmLocation('xxx.xxx.xxx.xxx', $affiliateProject->getId());
        
        $this->assertEquals(true, $rtn, 'should be true');
 
    }

    public function testCheckInputLocation_true_no_iplocation() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        //$this->projectLocationService->setDummy(true,'上海','上海市');
        $rtn = $this->projectLocationService->confirmLocation('111.111.111.111', $affiliateProject->getId());
        
        $this->assertEquals(true, $rtn, 'should be true');
 
    }


    public function testCheckInputLocation_true_only_province_restriction() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $affiliateProject->setProvince('河南省');
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'郑州市','河南省');
        $rtn = $this->projectLocationService->confirmLocation('111.111.111.111', $affiliateProject->getId());
        
        $this->assertEquals(true, $rtn, 'should be true');
    }

    public function testCheckInputLocation_false_only_province_restriction() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $affiliateProject->setProvince('河南省');
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'石家庄市','河北省');
        $rtn = $this->projectLocationService->confirmLocation('111.111.111.111', $affiliateProject->getId());
        
        $this->assertEquals(false, $rtn, 'should be true');
    }

    public function testCheckInputLocation_true_only_city_restriction() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $affiliateProject->setCity('郑州市');
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'郑州市','河南省');
        $rtn = $this->projectLocationService->confirmLocation('111.111.111.111', $affiliateProject->getId());
        
        $this->assertEquals(true, $rtn, 'should be true');
    }

    public function testCheckInputLocation_false_only_city_restriction() {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $affiliateProject->setCity('郑州市');
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $this->projectLocationService->setDummy(true,'石家庄市','河北省');
        $rtn = $this->projectLocationService->confirmLocation('111.111.111.111', $affiliateProject->getId());
        
        $this->assertEquals(false, $rtn, 'should be true');
    }
}
