<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;

class LoadLandingWenwenCodeData extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
    static public $USER ;
    public static $WENWEN_USER_TOKEN;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$USER = array();
        self::$WENWEN_USER_TOKEN = array();
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
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice.nima@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));

        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();

        $this->addReference('user0', $user);

        $wenwenUserToken = new \Jili\ApiBundle\Entity\WenwenUser();
        $wenwenUserToken->setEmail($user->getEmail() );

        $params = array(
            'email'=> $user->getEmail(),
            'uniqkey'=>md5(uniqid())
        );

        $token = $this->genSecretToken($params);

        $wenwenUserToken->setToken($token);
        $manager->persist($wenwenUserToken);
        $manager->flush();

        self::$USER[] = $user;
        self::$WENWEN_USER_TOKEN[] = $wenwenUserToken;

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
    /**
     *@param $plain => array( email, uniqkey )
     */
    private function genSecretToken($plain)
    {
        $plain['signature'] = $this->getToken($plain['email']);
        return  strtr(base64_encode(json_encode($plain)), '+/', '-_');
    }
    /**
     * copied from wenwenController.php to gen the signature
     */
    private function getToken($email)
    {
        $seed = "ADF93768CF";
        $hash = sha1($email . $seed);
        for ($i = 0; $i < 5; $i++) {
            $hash = sha1($hash);
        }
        return $hash;
    }
}
