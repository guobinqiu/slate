<?php

namespace Affiliate\AppBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\ProvinceList;
use Wenwen\FrontendBundle\Entity\CityList;

class ProjectLocationServiceTest extends WebTestCase
{
    private $projectLocationService;

    private $application;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {   
        static::$kernel = static::createKernel();
        static::$kernel->boot(); 
        $this->projectLocationService = static::$kernel->getContainer()->get('app.af_location_service');
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
 
    public function testCheckInput()
    {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        //Test ENV
        $cityName = array('上海市', '北京市');
        $provinceName = array('浙江省', '吉林省');
        $cityId = rand(1,10);
        $provinceId = rand(1,10);

        foreach ($cityName as $c) {
            $city = new CityList();
            $city->setCityName($c);
            $city->setProvinceId($provinceId);
            $city->setCityId($cityId);
            $city->setGmoCityId(3101);
            $em->persist($city);  
            $em->flush();
            $em->clear();  
        }

        foreach ($provinceName as $p) {
            $province = new ProvinceList();
            $province->setProvinceName($p);
            $em->persist($province);
            $em->flush();
            $em->clear(); 
        }

        $status = $this->projectLocationService->checkInputLocation('浙江省', '上海市');
        $error_status = $this->projectLocationService->checkInputLocation('广西市', null);
        $mult_status = $this->projectLocationService->checkInputLocation(null, '上海市,北京市');
        $this->assertEquals('success', $status, 'status NG');
        $this->assertEquals('failure', $error_status, 'error status NG');
        $this->assertEquals('success', $mult_status, 'mult status NG');
        
    }
}
