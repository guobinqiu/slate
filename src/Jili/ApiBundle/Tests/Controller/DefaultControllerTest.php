<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        $this->em  = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
       $this->em->close();
    }

    /**
     * landingAction with not exists: wenwen code exists email 
     */

    /**
     * landingAction with not exists: fresh email 
     */
    public function testLandingActionFresh() {
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');

        $em = $this->em;
        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        $user_wenwen = $em->getRepository('JiliApiBundle:WenWenUser')->findOneByEmail($query['email']);

        if( $user_wenwen) {
            $em->remove($user_wenwen);
            $em->flush();
            $em->clear();
        }
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if( $user) {
            $em->remove($user);
            $em->flush();
            $em->clear();
        }

        $secret_token= $this->genSecretToken($query);
        $url = $router->generate('_default_landing', array('secret_token'=>$secret_token));
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after visit landing page'  );
        $form = $crawler->selectButton('1秒注册积粒网')->form();

        $form['nick'] = 'alice323';//str_replace( '@.', '',$query['email']);
        $form['pwd'] = 'dddddd';
        $form['newPwd'] = 'dddddd';

        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        ///  check db status
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        $this->assertEquals(1, count($user));
        echo 'nick:',$user->getNick(),PHP_EOL;
        echo 'email:',$user->getEmail(),PHP_EOL;
        echo 'pwd:','dddddd',PHP_EOL;

//        restore 
    }
    /**
     *@param $plain => array( email, uniqkey )
     */
    private function genSecretToken($plain) {
        $plain['signature'] = $this->getToken($plain['email']);
        return  strtr(base64_encode(json_encode($plain)), '+/', '-_');
    }
    /**
     * copied from wenwenController.php to gen the signature
     */
	private function getToken($email) {
		$seed = "ADF93768CF";
		$hash = sha1($email . $seed);
		for ($i = 0; $i < 5; $i++) {
			$hash = sha1($hash);
		}
		return $hash;
	}
}
#    public function testFastLoginAction()
#    {
#        $client = static::createClient();
#        $container = $client->getContainer();
#        $logger= $container->get('logger');
#
#        $query = array('email'=> 'alice.nima@gmail.com', 'pwd'=>'aaaaaa' );
#        $url = $container->get('router')->generate('_default_fastLogin', $query ) ;
#        // $crawler = $client->request('GET', '/hello/Fabien');
#        echo $url, PHP_EOL;
#
#        $client->request('POST', $url ) ;
#        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
#        $this->assertEquals('1', $client->getResponse()->getContent());
#
#        $this->assertEquals('0', '0');
#        //$this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
#    }
