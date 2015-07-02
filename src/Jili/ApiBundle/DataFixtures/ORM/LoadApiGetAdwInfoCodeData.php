<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\FrontendBundle\Entity\CpsAdvertisement;
use Jili\FrontendBundle\Entity\ChanetAdvertisement;

class LoadApiGetAdwInfoCodeData extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface
{
    static public $USERS;
    static public $CPS_ADVERTISEMENTS;
    static public $ADVERTISEMENTS;

    public function __construct()
    {
        self::$USERS = array();
        self::$CPS_ADVERTISEMENTS = array();
        self::$ADVERTISEMENTS = array();
    }
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //load data for testing .
        $user = new User();
        $user->setNick('alic32');
        $user->setEmail('alice.nima@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self::$USERS[] = $user;

        $advertisement = new Advertiserment();
        $advertisement->setType('5,6,8,9,10,13,70,80,90,15')
            ->setTitle('京东商城')
            ->setDecription('京东商城')
            ->setImageurl('http://count.chanet.com.cn/click.cgi?a=480534&d=22822&u=&e=')
            ->setIsExpired(0)
            ->setIncentiveType( 2)
            ->setIncentiveRate( 420)
            ->setRewardRate(95.2)
            ->setIncentive( 126)
            ->setCategory( 2)
            ->setDeleteFlag( 0);

        $manager->persist($advertisement);
        $manager->flush();
        self::$ADVERTISEMENTS[] = $advertisement;

        $chanet_advertisement = new ChanetAdvertisement();
        $chanet_advertisement->setAdsId( 514)
            ->setAdsName( '京东商城CPS')
            ->setCategory('电子/家电')
            ->setAdsUrlType('首页推广链接')
            ->setAdsUrl('http://www.jd.com')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=22338&u=&e=&url=http%3A%2F%2Fwww.jd.com')
            ->setSelectedAt(new \DateTime('2015-04-30 07:23:50'))
            ->setFixedHash('3d11fb7cc6c1b2a4c10a33ff3b6e5e61a951491088af5dc5f7324be20f02f720')
            ->setIsActivated( 1);
        $manager->persist($chanet_advertisement);
        $manager->flush();

        $cps_advertisement = new CpsAdvertisement();
        $cps_advertisement->setAdCategoryId(2)
            ->setAdId( $chanet_advertisement->getId() )
            ->setTitle('京东商城')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=22338&u=&e=&url=http%3A%2F%2Fwww.jd.com')
            ->setAdsUrl('http://www.jd.com')
            ->setWebsiteName('京东商城')
            ->setWebsiteNameDictionaryKey('J')
            ->setWebsiteCategory('电子/家电')
            ->setwebsiteHost('www.jd.com')
            ->setSelectedAt(new \DateTime('2015-04-22 13:22:43'))
            ->setIsActivated(1);
        $manager->persist($cps_advertisement);
        $manager->flush();
        self::$CPS_ADVERTISEMENTS[0] = $cps_advertisement;


        $chanet_advertisement = new ChanetAdvertisement();
        $chanet_advertisement->setAdsId( 283)
            ->setAdsName( '当当CPS推广')
            ->setCategory('其他')
            ->setAdsUrlType('首页推广链接')
            ->setAdsUrl('http://www.dangdang.com')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=5775&u=&e=&url=http%3A%2F%2Fwww.dangdang.com')
            ->setSelectedAt(new \DateTime('2015-04-30 07:23:50'))
            ->setFixedHash('d91fab7853315510a8770442973a21b9478d599c88a167f124f1e2e1e1949b39')
            ->setIsActivated( 1);
        $manager->persist($chanet_advertisement);
        $manager->flush();

        $cps_advertisement = new CpsAdvertisement();
        $cps_advertisement->setAdCategoryId(2)
            ->setAdId( $chanet_advertisement->getId() )
            ->setTitle('当当')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=5775&u=&e=&url=http%3A%2F%2Fwww.dangdang.com')
            ->setAdsUrl('http://www.dangdang.com')
            ->setWebsiteName('当当')
            ->setWebsiteNameDictionaryKey('D')
            ->setWebsiteCategory('其他')
            ->setwebsiteHost('www.dangdang.com')
            ->setSelectedAt(new \DateTime('2015-04-22 13:22:43'))
            ->setIsActivated(1);
        $manager->persist($cps_advertisement);
        $manager->flush();
        self::$CPS_ADVERTISEMENTS[1] = $cps_advertisement;


        $chanet_advertisement = new ChanetAdvertisement();
        $chanet_advertisement->setAdsId( 2466)
            ->setAdsName( '史泰博官网CPS')
            ->setCategory('其他')
            ->setAdsUrlType('首页推广链接')
            ->setAdsUrl('http://www.staples.cn')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=299417&u=&e=&url=http%3A%2F%2Fwww.staples.cn')
            ->setSelectedAt(new \DateTime('2015-04-30 07:22:29'))
            ->setFixedHash('b27441cc2026e8d378ce149016cdc1563dde084b0629cdf9d7baf0ba37a29ffb
')
            ->setIsActivated( 1);
        $manager->persist($chanet_advertisement);
        $manager->flush();
        $cps_advertisement = new CpsAdvertisement();
        $cps_advertisement->setAdCategoryId(2)
            ->setAdId( $chanet_advertisement->getId() )
            ->setTitle('史泰博官网')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=299417&u=&e=&url=http%3A%2F%2Fwww.staples.cn')
            ->setAdsUrl('http://www.staples.cn')
            ->setWebsiteName('史泰博官网')
            ->setWebsiteNameDictionaryKey('S')
            ->setWebsiteCategory('其他')
            ->setwebsiteHost('www.staples.cn')
            ->setSelectedAt(new \DateTime('2015-04-22 13:22:43'))
            ->setIsActivated(1);
        $manager->persist($cps_advertisement);
        $manager->flush();
        self::$CPS_ADVERTISEMENTS[2] = $cps_advertisement;

// for advertisement callback , the pre inserted
        $adw_order = new AdwOrder();
        $adw_order->setUserId($user->getId())
            ->setAdId( self::$ADVERTISEMENTS[0]->getId())
            ->setCreateTime(new \DateTime('2015-06-25 16:03:23'))
            ->setIncentiveType(self::$ADVERTISEMENTS[0]->getIncentiveType())
            ->setIncentiveRate(self::$ADVERTISEMENTS[0]->getIncentiveRate())
            ->setOrderStatus(0)
            ->setDeleteFlag(0);

        $manager->persist($adw_order);
        $manager->flush();

        $task_history_class = '\Jili\ApiBundle\Entity\TaskHistory0'. ($user->getId() % 10) ;
        $task_history = new $task_history_class();
        $task_history->setOrderId($adw_order->getId())
            ->setUserId($user->getId() )
            ->setTaskName(self::$ADVERTISEMENTS[0]->getTitle() )
            ->setCategoryType(self::$ADVERTISEMENTS[0]->getCategory())
            ->setPoint(0)
            ->setDate(new \DateTime('2015-06-25 16:30:18'))
            ->setStatus(1)
            ->setTaskType(1);

        $manager->persist($task_history);
        $manager->flush();
    }

}

