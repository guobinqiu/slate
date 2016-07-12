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
	 * @Route("/about", name="_default_about", requirements={"_scheme"="http"})
	 */
    public function aboutAction()
    {
        return $this->render('WenwenFrontendBundle:About:company.html.twig');
    }

    /**
     * @Route("/about/map", name="_default_about_map", requirements={"_scheme"="http"})
     */
    public function mapAction()
    {
        return $this->render('WenwenFrontendBundle:About:map.html.twig');
    }

    /**
     * @Route("/about/links", name="_default_about_links", requirements={"_scheme"="http"})
     */
    public function linksAction()
    {
        return $this->render('WenwenFrontendBundle:About:links.html.twig');
    }

    /**
     * @Route("/about/regulations", name="_default_about_regulations", requirements={"_scheme"="http"})
     */
    public function regulationsAction()
    {
        return $this->render('WenwenFrontendBundle:About:regulations.html.twig');
    }

    /**
     * @Route("/about/ww", name="_default_about_ww", requirements={"_scheme"="http"})
     */
    public function wwAction()
    {
        return $this->render('WenwenFrontendBundle:About:91ww.html.twig');
    }

    /**
	 * @Route("/error", name="_default_error", requirements={"_scheme"="http"})
	 */
    public function errorAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
    }

    /**
	 * @Route("/support", name="_default_support", requirements={"_scheme"="http"})
	 */
    public function supportAction()
    {
        return $this->render('WenwenFrontendBundle:Help:index.html.twig');
    }

    /**
     * @Route("/newGuide", name="_default_support_newGuide", requirements={"_scheme"="http"})
     */
    public function guideAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuide.html.twig');
    }

    /**
     * @Route("/newGuide/detail", name="_default_support_newGuide_detail", requirements={"_scheme"="http"})
     */
    public function guideDetailAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuideDetail.html.twig');
    }

    /**
     * @Route("/feedback", name="_default_feedback", requirements={"_scheme"="http"})
     */
    public function feedbackAction()
    {
        return $this->render('WenwenFrontendBundle:Help:feedback.html.twig');
    }

    /**
     * @Route("/feedback/finished", name="_default_feedback_finished", requirements={"_scheme"="http"}, options={"expose"=true})
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Help:finished.html.twig');
    }

    /**
	 * @Route("/contact", name="_default_contact", options={"expose"=true}, requirements={"_scheme"="http"})
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
        $message->setFrom(array($this->container->getParameter('qqmail_sender') => '91问问调查网'));
        $message->setTo('support@91wenwen.com');
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
        $flag = $this->get('swiftmailer.mailer.qq')->send($message);
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
