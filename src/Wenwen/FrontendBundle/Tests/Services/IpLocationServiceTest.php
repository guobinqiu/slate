<?php
namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\Entity\CityList;

class IpLocationServiceTest extends WebTestCase
{

    private $ipLocationService;

    private $application;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->ipLocationService = static::$kernel->getContainer()->get('app.ip_location_service');
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

    /**
    *  实际调用amap的API接口进行测试 
    */
    /*
    public function testGetLocationId_amap()
    {
        // a fake user_id for input
        $ipAddress = '180.168.105.42';

        // call function for testing
        $this->ipLocationService->setDummy(false);
        $responseBody = $this->ipLocationService->getLocationJson($ipAddress);

        print($responseBody);
    }
    */

    public function testGetLocationId_Error()
    {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $cityId = 11;
        $provinceId = 13;
        $cityName = '上海市';
        $provinceName = '上海市';


        // a fake user_id for input
        $ipAddress = '123.123.332.333';

        // call function for testing
        $this->ipLocationService->setDummy(true, '北京', $provinceName);
        $locationId = $this->ipLocationService->getLocationId($ipAddress);

        $this->assertEquals(false, $locationId['status'], 'status NG');
        $this->assertEquals(0, $locationId['cityId'], 'cityId NG');
        $this->assertEquals(0, $locationId['provinceId'], 'provinceId NG');

        //print_r($locationId);
        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    public function testGetLocationId_OK()
    {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $cityId = 11;
        $provinceId = 13;
        $cityName = '上海市';
        $provinceName = '上海市';

        $city = new CityList();
        $city->setCityName($cityName);
        $city->setProvinceId($provinceId);
        $city->setCityId($cityId);

        $em->persist($city);
        $em->flush();
        $em->clear();


        // a fake user_id for input
        $ipAddress = '123.123.332.333';

        // call function for testing
        $this->ipLocationService->setDummy(true, '上海', $provinceName);
        $locationId = $this->ipLocationService->getLocationId($ipAddress);

        $this->assertEquals(true, $locationId['status'], 'status NG');
        $this->assertEquals($cityId, $locationId['cityId'], 'cityId NG');
        $this->assertEquals($provinceId, $locationId['provinceId'], 'provinceId NG');

        //print_r($locationId);
    }

    public function testGetLocationId_CityNameNotFound()
    {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $cityId = 11;
        $provinceId = 13;
        $cityName = '上海市';
        $provinceName = '上海市';

        $city = new CityList();
        $city->setCityName($cityName);
        $city->setProvinceId($provinceId);
        $city->setCityId($cityId);

        $em->persist($city);
        $em->flush();
        $em->clear();


        // a fake user_id for input
        $ipAddress = '123.123.332.333';

        // call function for testing
        $this->ipLocationService->setDummy(true, '北京', $provinceName);
        $locationId = $this->ipLocationService->getLocationId($ipAddress);

        $this->assertEquals(false, $locationId['status'], 'status NG');
        $this->assertEquals(0, $locationId['cityId'], 'cityId NG');
        $this->assertEquals(0, $locationId['provinceId'], 'provinceId NG');

        //print_r($locationId);
    }

}
