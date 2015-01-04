<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadTaobaoCategoryData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface,OrderedFixtureInterface
{

    static $SELF_PROMOTION_CATEGORIES;
    /**
    * @var ContainerInterface
    */
    private $container;

    public function __construct() {
        self::$SELF_PROMOTION_CATEGORIES = array();
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
    public function getOrder()
    {
        return 1;
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) 
    {
        //load data for testing .
        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures' ;
        $file = $fixture_dir.DIRECTORY_SEPARATOR. 'taobao_category.sql';
        $r = $manager->getConnection()->query(file_get_contents($file));
        $r->closeCursor();

        $manager->clear();
        self::$SELF_PROMOTION_CATEGORIES = $manager->getRepository('JiliFrontendBundle:TaobaoCategory')
            ->createQueryBuilder('tc')
            ->Where('tc.unionProduct = :unionProduct')
            ->andWhere('tc.deleteFlag = 0')
            ->setParameter('unionProduct', \Jili\FrontendBundle\Entity\TaobaoCategory::SELF_PROMOTION) 
            ->getQuery()->getResult();
    }
}

?>
