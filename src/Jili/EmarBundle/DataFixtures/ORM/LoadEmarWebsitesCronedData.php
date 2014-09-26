<?php
namespace Jili\EmarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\EmarBundle\Entity\EmarWebsitesCroned;

class LoadEmarWebsitesCronedData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $EMAR_WEBSITES_CRONED;

    /**
    * @var ContainerInterface
    */
    private $container;

    public function __construct() {
        self :: $EMAR_WEBSITES_CRONED = array ();
    }

    /**
    * {@inheritDoc}
    */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
    * {@inheritDoc}
    */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {

        $emarWebsite = new EmarWebsitesCroned;
        $emarWebsite->setWebId(3659);
        $emarWebsite->setWebName('雅昌影像');
        $emarWebsite->setWebCatid(24);
        $emarWebsite->setLogoUrl('http://image.yiqifa.com/ad_images/reguser/24/4/60/1376643810386.jpg');
        $emarWebsite->setWebUrl('http://p.yiqifa.com/n?k=2mLErnWe6nDOrI6HCZg7Rnu_fmUmUSebRcgsRIeEYOsH2mq1KQul6ZXHWNRlWEUH1n2LWEyHWZLErJoH2mLOW9Bb6lb96QLE&e=APIMemberId&spm=139599061334718017.1.1.1');
        $emarWebsite->setInformation('雅昌影艺，国内首家专注影像艺术的电子商务平台。轻松鼠标一点，花上几十元到几百元，您就可以将世界各地摄影大师的作品带回家，妆点家居环境或馈赠亲友。更有数百万级的作品，在网站和实体连锁画廊，供您自由挑选并用于典藏与投资！');
        $emarWebsite->setBeginDate('2013-08-01 00:00:00');
        $emarWebsite->setEndDate('2013-08-01 00:00:00');
        $emarWebsite->setCommission('10.5%');
        $manager->persist($emarWebsite);
        $manager->flush();
        self :: $EMAR_WEBSITES_CRONED[] = $emarWebsite;

        $emarWebsite = new EmarWebsitesCroned;
        $emarWebsite->setWebId(3674);
        $emarWebsite->setWebName('139彩票网');
        $emarWebsite->setWebCatid(24);
        $emarWebsite->setLogoUrl('http://image.yiqifa.com/ad_images/reguser/24/32/0/1375146051050.jpg');
        $emarWebsite->setWebUrl('http://p.yiqifa.com/n?k=2mLErnWe6E2SrI6HCZg7Rnu_fmUmUSFqWlyE35wd3OoVrI6H2mLq6lWSWQLFWEjS1QLLrI6HYmLErJ683NDL3EWqrBwepNBS1ZL-&e=APIMemberId&spm=139599061334718017.1.1.1');
        $emarWebsite->setInformation('139彩票网是彩民中奖的福地，专业安全的彩票代购合买平台，涵盖福彩、体彩、足彩、竞彩、快频等彩种，奖金优化、智能过滤功能齐全，专家预测、足球数据资讯丰富，不仅线上投注截止时间最晚，而且即时开奖、随时提现，提现无需手续费服务更贴心。');
        $emarWebsite->setBeginDate('2013-07-31 00:00:00');
        $emarWebsite->setEndDate('2014-07-30 00:00:00');
        $emarWebsite->setCommission('4.2%');
        $manager->persist($emarWebsite);
        $manager->flush();
        self :: $EMAR_WEBSITES_CRONED[] = $emarWebsite;
    }
}
