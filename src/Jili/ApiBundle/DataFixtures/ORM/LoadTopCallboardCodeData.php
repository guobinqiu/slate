<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



class LoadTopCallboardCodeData  extends AbstractFixture implements ContainerAwareInterface
{
    public static $ROWS;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS = array();
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
        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir.DIRECTORY_SEPARATOR.'fixtures';

        $sql = file_get_contents($fixture_dir.DIRECTORY_SEPARATOR.'cb_category.sql');
        $r =   $manager->getConnection()->query($sql);
        $r->closeCursor();

        $sql = file_get_contents($fixture_dir.DIRECTORY_SEPARATOR.'callboard.sql');
        $r =   $manager->getConnection()->query($sql);
        $r->closeCursor();

//        self::$ROWS =  $manager->getRepository('JiliApiBundle:CallBoard')->findAll();

    }
}
?>
