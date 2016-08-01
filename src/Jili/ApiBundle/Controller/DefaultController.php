<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jili\ApiBundle\Mailer;

class DefaultController extends Controller
{
    public function getToken($email)
    {
        $seed = "ADF93768CF";
        $hash = sha1($email . $seed);
        for ($i = 0; $i < 5; $i++) {
            $hash = sha1($hash);
        }
        return $hash;
    }
   
    /**
	 * @Route("/error", name="_default_error")
	 */
    public function errorAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
    }

    /**
	 * @Route("/contact", name="_default_contact", options={"expose"=true})
	 */
    public function contactAction()
    {
       $request = $this->get('request');
       $content = $request->query->get('content');
       $email = $request->query->get('email');
       $code = $this->checkContact($content, $email);
       $response = new Response($code);
       //enable CORS
       $response->headers->set('Access-Control-Allow-Origin', '*');
       return $response;
    }

    private function checkContact($content, $email)
    {
        $code = 0;
        //check content null
        if (!$content) {
            $code = 1;
            return $code;
        }
        //check email null
        if (!$email) {
            $code = 2;
            return $code;
        }
        //check email format
        if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $email)) {
            $code = 3;
            return $code;
        }

        //get user info
        $session = $this->getRequest()->getSession();
        $nick = $session->get('nick');

        //send email
        $subject = "来自91问问帮助中心的咨询";
        if ($nick) {
            $id = $session->get('uid');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $subject = "来自" . $nick . " [" . $user->getEmail() . "] 的咨询";
        }
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom(array($this->container->getParameter('webpower_from') => '91问问调查网'));
        $message->setSender($this->container->getParameter('webpower_signup_sender'));
        $message->setTo($this->container->getParameter('cs_mail'));
        $message->setReplyTo($email);
        $message->setBody('<html>' .
        '<head></head>' .
        '<body>' .
        '咨询内容<br/>' .
        $content . '<br/><br/>' .
        '联系方式<br/>' .
        $email . '<br/><br/>' .
        '浏览器<br/>'.$_SERVER['HTTP_USER_AGENT'] . '<br/>' .
        '</body>' .
        '</html>', 'text/html');
        $flag = $this->get('swiftmailer.mailer.webpower_signup_mailer')->send($message);
        if (!$flag) {
            $code = 4;
        }
        return $code;
    }

    /**
	* @Route("/adLogin", name="_default_ad_login", options={"expose"=true})
	*/
    public function adLoginAction()
    {
        $request = $this->get('request');

        $session = $this->get('session');
        $code =$this->get('login.listener')->login($this->get('request'));
        $response = new Response($code);

        if ($request->request->has('remember_me')  &&  $request->request->get('remember_me') === '1') {

            if($session->has('uid')) {

                $request = $this->get('request');
                $email = $request->get('email');
                $pwd= $request->get('pwd');
                $token = $this->get('login.listener')->buildToken( array( 'email'=> $email, 'pwd'=> $pwd) );
                if( $token) {
                    $response->headers->setCookie(new Cookie("jili_rememberme", $token, time() + 3153600, '/'));
                } else {
                    // todo: set the error flash
                }
            }
        }
        return $response;
    }
}
