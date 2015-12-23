<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

use Jili\FrontendBundle\Entity\ExperienceAdvertisement;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Utility\WenwenToken;

/**
 * @Route("/home",requirements={"_scheme"="http"})
 */
class HomeController extends Controller
{
    /**
     * @Route("/index")
     * @Method({ "GET"})
     * @Template
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $logger = $this->get('logger');

        $cookies = $request->cookies;
        $session = $request->getSession();

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
            $this->get('session.points')->reset()->getConfirm();
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

        return $this->render('WenwenFrontendBundle:Home:home.html.twig', array ());
    }

    /**
     * @Route("/task")
     * @Template
     */
    public function taskAction()
    {
        //任务列表
        $arr = $this->getTaskList();
        $arr['wenwen_vote_url'] = $this->container->getParameter('wenwen_vote_url');
        return $this->render('JiliFrontendBundle:Home:task.html.twig', $arr);
    }

    public function getTaskList()
    {
        //可以做的任务，签到+游戏+91问问+购物 -cpa
        $taskList = $this->get('session.task_list');
        $taskList->setRequest($this->get('request'));
        $arr = $taskList->compose();

        return $arr;
    }

    /**
     * @Route("/adExperience")
     * @Template
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
            $adExperience = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement();
            $cache_proxy->set($cache_fn, $adExperience);
        }

        $arr['ad_experience'] = $adExperience;
        return $this->render('JiliFrontendBundle:Home:adExperience.html.twig', $arr);
    }

    /**
     * @Route("/vote")
     * @Template
     */
    public function voteAction()
    {
        //get vote mark
        $wenwen_vote_mark = $this->container->getParameter('wenwen_vote_mark');

        //快速问答:从文件中读取
        $filename = $this->container->getParameter('file_path_wenwen_vote');
        
        $votes = FileUtil :: readJosnFile($filename);
        if(! $votes || empty($votes)) {
            return new Response('<!-- 快速问答 -->');
        }
        $vote = array_pop($votes);
        $vote['vote_url'] = $vote['vote_url'] . "?" . $wenwen_vote_mark;
        return $this->render('JiliFrontendBundle:Home:vote.html.twig', $vote);
    }
}
