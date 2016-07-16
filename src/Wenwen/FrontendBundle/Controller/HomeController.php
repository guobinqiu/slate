<?php
namespace Wenwen\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Jili\FrontendBundle\Entity\ExperienceAdvertisement;

/**
 * 
 */
class HomeController extends Controller
{
    /**
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $logger = $this->get('logger');

        $logger->debug(__METHOD__ . ' START');

        $cookies = $request->cookies;
        $session = $request->getSession();

        if(!$session->has('uid')){
            // return $this->redirect($this->generateUrl('_user_login' ));
            return $this->render('WenwenFrontendBundle:Home:index.html.twig');;
        }

        //记住我
        if ($cookies->has('jili_rememberme') && !$session->has('uid')) {
            $token = $cookies->get('jili_rememberme');
            $result = $this->get('login.listener')->byToken($token);
            if ($result !== false && is_object($result) && $result instanceof \Jili\ApiBundle\Entity\User) {
                $session->set('uid', $result->getId());
                $session->set('nick', $result->getNick());
            }
        }

        //取得分数，以及更新登录状态
        if ($session->has('uid')) {
            // 20160716 二逼拉的屎，首页去访问task_history 无端增加开销，删掉
            // 但是很遗憾拉的屎太多太臭，这个branch不做过多清理，单纯为了增加速度，只做针对性处理
            $this->get('session.points')->reset();
            $this->get('login.listener')->updateSession();
        }

        //取得nick
        if ($cookies->has('jili_nick') && !$session->has('nick')) {
            $session->set('nick', $cookies->get('jili_nick'));
        }

        //newbie page
        if ($this->get('login.listener')->isNewbie()) {
            if ($session->get('is_newbie_passed', false) === false) {
                $arr['is_newbie_passed'] = false;
                $session->set('is_newbie_passed', true);
            }
        }

        // trace
        if( $request->query->has('spm') ) {
            $this->get('user_sign_up_route.listener')->refreshRouteSession( array('spm'=> $request->get('spm', null) ) );
        }
        $this->get('user_sign_up_route.listener')->log();

        $logger->debug(__METHOD__ . ' END');
        return $this->render('WenwenFrontendBundle:Home:home.html.twig');
    }

    /**
     * 
     */
    public function adExperienceAction()
    {
        $cache_fn = $this->container->getParameter('cache_config.api.top_adExperience.key');
        $cache_duration = $this->container->getParameter('cache_config.api.top_adExperience.duration');
        $cache_proxy = $this->get('cache.file_handler');

        if ($cache_proxy->isValid($cache_fn, $cache_duration)) {
            $adExperience = $cache_proxy->get($cache_fn);
        } else {
            $cache_proxy->remove($cache_fn);
            $em = $this->getDoctrine()->getManager();
            $adExperience = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement(2);
            $cache_proxy->set($cache_fn, $adExperience);
        }

        $arr['ad_experience'] = $adExperience;
        return $this->render('WenwenFrontendBundle:Advertisement:_hallHome.html.twig', $arr);
    }

}
