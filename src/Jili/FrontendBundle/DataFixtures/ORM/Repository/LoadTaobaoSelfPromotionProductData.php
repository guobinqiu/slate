<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadTaobaoSelfPromotionProductData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface ,OrderedFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    static  public $PRODUCTS;

    public function __construct() {
        self::$PRODUCTS = array();
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
        return 2;
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        // with image, without image ,

        //load data for testing .
        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures' ;
        $file = $fixture_dir.DIRECTORY_SEPARATOR. 'taobao_self_promotion_products.sql';

        $r = $manager->getConnection()->query(file_get_contents($file));
        $r->closeCursor();

        self::$PRODUCTS  = $manager->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->findAll();

        // copy the image  
        $directory = $this->container->getParameter('taobao_self_promotion_picture_dir');
        $fixture_dir = $this->container->getParameter('kernel.root_dir');
        $source = $fixture_dir.'/fixtures/taobao/';
        $fs = new Filesystem();
        if( ! $fs->exists($directory) ) {
            $fs->mkdir( $directory);
        }

        $fs->copy( $source. 'pro01_01.jpg',
            $directory.  LoadTaobaoSelfPromotionProductData::$PRODUCTS[0]->getPictureName() , true);
        $fs->copy( $source. 'pro11_04.jpg',
            $directory.  LoadTaobaoSelfPromotionProductData::$PRODUCTS[99]->getPictureName());

    }
}

?>
