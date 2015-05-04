<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;


use Jili\ApiBundle\DataFixtures\ORM\Advertiserment\LoadAdCategoryData;
use Jili\ApiBundle\DataFixtures\ORM\Advertiserment\LoadAdvertisermentData;
use Jili\ApiBundle\DataFixtures\ORM\Advertiserment\LoadCheckinAdverListData;

class AdvertisermentRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath();
        $directory .= '/DataFixtures/ORM/Advertiserment';
        $loader = new DataFixtureLoader($container);
        $loader->loadFromDirectory($directory);
        $executor->execute($loader->getFixtures());
        $this->em = $em;
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_469
     */
    public function testGetRedirect()
    {

        return 0;
        $em = $this->em;
        $uid = 105;
        $ad_ids=array(49,33,51,53,43,45,48,118,52,88,128);
        $expected_urls = array(
            'http://cms.yhd.com/cmsPage/show.do?pageId=103576&tracker_u=1787&uid=88624880176&website_id=480534',
            'http://union.dangdang.com/transfer/transfer.aspx?from=430-88624880206&backurl=http://book.dangdang.com',
            'http://t.dianping.com/redirect?id=88624880236&source=chanet&utm_source=chanet&url=http://t.dianping.com/',
            'http://www.jumei.com/track_cps.php?src=chanet&sub_src=88624880336&ret=http://www.jumei.com',
            'http://buy.111.com.cn/interfaces/cps/portal.action?partnercode=2013267&id=88624880436&source=chanet&url=http://www.111.com.cn/cmsPage/2014061801/index.html',
            'http://www.amazon.cn/?tag=adwrb4-23&ascsubtag=88624880466',
            'http://click.union.jd.com/JdClick/?unionId=20&siteId=88624880486&to=http%3A%2F%2Fwww.jd.com%2F',
            'http://www.feiniu.com/partner_ad/ad/chengguo?id=88624880556&source=chanet&url=http%3A%2F%2Fwww.feiniu.com%2Fedmv4%2F201407C21000013',
            'http://union.suning.com/aas/open/vistorAd.action?userId=1026&webSiteId=1022&adInfoId=1001&adBookId=1020&subUserEx=chanet88624880696&vistURL=http://www.suning.com',
            'http://click.union.vip.com/redirect.php?url=eyJ1Y29kZSI6ImEwNmZiNDA5Iiwic2NoZW1lY29kZSI6ImQxMmhkaTVsIn0=&desturl=http://www.vip.com&chan=88624880736',
            'http://www.staples.cn/affiliate/chanet?source=chanet&id=88624880926&url=http://www.staples.cn/%3Futm_source%3Daffiliate%26utm_medium%3Dchanet'
        );

        for($i = 0; $i<11;$i++){
            $url = $em->getRepository('JiliApiBundle:Advertiserment')->getRedirect($uid, $ad_ids[$i]); 
        //    $this->assertEquals($expected_urls[$i], $url, 'check parse target url' );
        }
    }
    /**
     * @group issue_469
     */
    public function testFindAllByCheckinAdverList() {
        $em = $this->em;
        $advertiserments = $em->getRepository('JiliApiBundle:Advertiserment')->findAllByCheckinAdverList(); 

        $this->assertCount(11, $advertiserments);

        $expeted_ad_ids=array(49,33,51,53,43,45,48,118,52,88,128);
        for($i = 0; $i<11;$i++){
            $this->assertEquals($expeted_ad_ids[$i], $advertiserments[$i]->getId(), 'check the join result by adveriserment.id');
        }
    }
}
