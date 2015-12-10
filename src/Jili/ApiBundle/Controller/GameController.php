<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route( requirements={"_scheme" = "http"})
 */
class GameController extends Controller
{
    /**
     * @Route("/chick", name="_game_chick")
     */
    public function chickAction()
    {
        $code = '';

        $arr['code'] = $code;

        if($this->checkMobile()) {
            $arr['code'] = $this->container->getParameter('init_one');
        }
        $uid = '';
        $uid = $this->get('request')->getSession()->get('uid');
        if(!$uid){
           $this->getRequest()->getSession()->set('referer', $this->generateUrl('_game_chick') );
           return $this->redirect($this->generateUrl('_user_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);

        $key = sha1(date("Ymd")."ADF93768CF".$uid);
        $url = 'http://sugoroku01.cn.pag-asia.com/index.php?point_uid='.$uid.'&nickname='.urlencode($user->getNick()).'&key='.$key;
        $arr['url'] = $url;

        return $this->render('JiliApiBundle:Game:chick.html.twig',$arr);
    }


    private function checkMobile()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        return  ( (false !== strpos($agent, 'iphone'))  ||
            (false !== strpos($agent, 'ipad')) ||
            (false !== strpos($agent, 'android'))) ; 
    }


}
