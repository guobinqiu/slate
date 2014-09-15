<?php

namespace Jili\EmarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


use Jili\EmarBundle\DataFixtures\ORM\LoadDefaultRedirectCodeData;

class DefaultControllerTest extends WebTestCase
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

        $container = static :: $kernel->getContainer();


            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

            // load fixtures
            $fixture = new LoadDefaultRedirectCodeData();
            $fixture->setContainer($container);

            $loader = new Loader();
            $loader->addFixture($fixture);

            $executor->execute($loader->getFixtures());

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
//        $this->em->close();
    }

    /**
     * @group edm-redirect
     * @dataProvider urlsProvider
     */
    public function testRedirect($target)
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $em = $this->em;

        //$email = 'alice.nima@gmail.com';
        $users = LoadDefaultRedirectCodeData::$ROWS;

        $user = $users[0];//$em->getRepository('JiliApiBundle:User')->findOneByEmail($email);

        // urls by 1. open api product; 2. visitor redirect; 3.user redirect; 4. yiqifa api
        //      to 1. amazon.cn 2. generals.

        print PHP_EOL.'<<<<<<<<<<<<'.PHP_EOL;
        print 'target: '. $target . PHP_EOL;

       $target_parsed  =  parse_url($target);
#        print PHP_EOL;
       // print_r($target_parsed );
#        print PHP_EOL;

        parse_str($target_parsed['query'],$query_parsed);
       // print_r($query_parsed);


        $m = str_replace('APIMemberId', $user->getId(), $query_parsed['m']);
        //print $m;
#        print PHP_EOL;
        // CASE: login  with session uid
        // CASE: not login
        // CASE: from EDM with ${user_id};
        $url = $container->get('router')->generate('jili_emar_default_redirect', array('m'=> $m)  , true) ;
        print 'url1: '.$url.PHP_EOL;


#        print PHP_EOL.'>>>>>>>>>>>>'.PHP_EOL;
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), PHP_EOL.$target. PHP_EOL. $url.PHP_EOL );
        $this->assertEquals('1', '1');
        // request
        // status code
        // ??

        // code...
    }


    public function urlsProvider()
    {
        $urls = array();
        # for yiqifa open platform.
        $urls [] = array( 'http://www.91jili.com/emar/default/redirect?m=http%3A%2F%2Fwww.amazon.cn%2F%25E9%25BA%25A6%25E6%2596%25AF%25E7%2594%259F%25E6%25B4%25BB%25E7%25A7%25802014%25E4%25B8%25AD%25E5%25B9%25B4%25E8%25A3%2585%25E5%2595%2586%25E5%258A%25A1%25E4%25BC%2591%25E9%2597%25B2%25E7%2594%25B7%25E8%25A3%2585%25E6%259D%25A1%25E7%25BA%25B9%25E8%25A1%25AC%25E8%25A1%25AB%25E7%259F%25AD%25E8%25A2%2596T%25E6%2581%25A4%25E7%25BF%25BB%25E9%25A2%2586%25E6%2589%2593%25E5%25BA%25953163%2Fdp%2FB00JUB056E%2Fref%3Dsr_1_9%3Fs%3Dapparel%26ie%3DUTF8%26qid%3D1402025212%26sr%3D1-9%26keywords%3D%25E4%25B8%25AD%25E5%25B9%25B4%2B%25E7%2594%25B7%25E8%25A3%2585%26tag%3Deqifarebate1094-23%26ascsubtag%3D708089%7C1%7CAPIMemberId');
        $urls[] = array('http://www.91jili.com/emar/default/redirect?m=http%3A%2F%2Fitem.yhd.com%2Fitem%2F6593051%26tag%3Deqifarebate1094-23%26ascsubtag%3D708089%7C1%7CAPIMemberId');
        $urls []= array('http://www.91jili.com/emar/default/redirect?m=http%3A%2F%2Fwww.amazon.cn%2FAnshun-Leather%25E8%2580%2581%25E4%25BA%25BA%25E5%25A4%25B4-%25E6%2596%25B0%25E6%25AC%25BE%25E7%2594%25B7%25E5%25A3%25AB%25E9%25AB%2598%25E6%25A1%25A3%25E5%2595%2586%25E5%258A%25A1%25E4%25BC%2591%25E9%2597%25B2%25E8%2587%25AA%25E5%258A%25A8%25E6%2589%25A3%25E7%2589%259B%25E7%259A%25AE%25E7%259A%25AE%25E5%25B8%25A6-LRT-6006%2Fdp%2FB007EMESE6%2Fref%3Dsr_1_1%3Fs%3Dapparel%26ie%3DUTF8%26qid%3D1402025669%26sr%3D1-1%26keywords%3D%25E7%2589%259B%25E7%259A%25AE%2B%25E7%259A%25AE%25E5%25B8%25A6%26tag%3Deqifarebate1094-23%26ascsubtag%3D708089%7C1%7CAPIMemberId');
        $urls []= array('http://www.91jili.com/emar/default/redirect?m=http%3A%2F%2Fitem.jd.com%2F1027920051.html%26tag%3Deqifarebate1094-23%26ascsubtag%3D708089%7C1%7CAPIMemberId');
        $urls []= array('http://www.91jili.com/emar/default/redirect?m=http%3A%2F%2Fitem.yixun.com%2Fitem-79973.html%3FYTAG%3D3.21012020%26tag%3Deqifarebate1094-23%26ascsubtag%3D708089%7C1%7CAPIMemberId');
        $urls []= array('http://www.91jili.com/emar/default/redirect?m=http%3A%2F%2Fitem.jd.com%2F406013.html%26tag%3Deqifarebate1094-23%26ascsubtag%3D708089%7C1%7CAPIMemberId');

        return $urls;
    }
    // todo:
    // test redirect
    // 2. with APIMemberId
    // http://jili0129.vgc.net/app_dev.php/emar/default/redirect?m=http%3A%2F%2Fp.yiqifa.com%2Fn%3Fk%3D6yUFMPAPrI6HWN3LrI6HCZg7Rnu_fOy7M57dC9Dd3OoVfltqWEDl6N2sWnzdCZgVYZLErI6HWEK7rnRlWE2L6ZLLrI6HYmLErJ6y6lRF1J2LrIW-%26e%3DAPIMemberId%26spm%3D139597428026718017.1.1.1

    // 2. with userid
    // http://jili0129.vgc.net/app_dev.php/emar/default/redirect?m=http%3A%2F%2Fp.yiqifa.com%2Fn%3Fk%3D6yUFMPAPrI6HWN3LrI6HCZg7Rnu_fOy7M57dC9Dd3OoVfltqWEDl6N2sWnzdCZgVYZLErI6HWEK7rnRlWE2L6ZLLrI6HYmLErJ6y6lRF1J2LrIW-%26e%3DAPIMemberId%26spm%3D139597428026718017.1.1.1
}
