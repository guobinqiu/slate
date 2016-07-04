<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\PointsExchange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jili\ApiBundle\Mailer;
use Jili\ApiBundle\Entity\LoginLog;
use Jili\ApiBundle\Entity\WenwenUser;
use Jili\ApiBundle\Entity\Callboard;
use Jili\ApiBundle\Entity\UserGameVisit;
use Jili\ApiBundle\Entity\RegisterReward;
use Jili\ApiBundle\Entity\UserInfoVisit;
use Jili\ApiBundle\Entity\CheckinAdverList;
use Jili\ApiBundle\Entity\CheckinUserList;
use Jili\ApiBundle\Entity\CheckinClickList;
use Jili\ApiBundle\Entity\UserWenwenVisit;
use Jili\ApiBundle\Utility\FileUtil;

class DefaultController extends Controller
{
    // 是否完善资料
    private function isExistInfo($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($userid);
        if ($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
            return true;
        else
            return false;
    }

    //是否给过奖励
    private function isGetReward($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
        if ($user) {
            if ($user[0]->getType() == $this->container->getParameter('init_one')) {
                $isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($userid);
                if (empty ($isuserAmazon)) {
                    return $this->container->getParameter('init_three'); //获得米粒的
                } else {
                    return $this->container->getParameter('init_two'); //获得优惠券的
                }
            } else {
                return $this->container->getParameter('init_four'); //参加其他活动领取的
            }
        } else {
            return false;
        }

    }

    //给米粒奖励
    private function getPointReward($componType, $userid)
    {
        $em = $this->getDoctrine()->getManager();
        $isuserPoint = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
        if (empty ($isuserPoint)) {
            $reward = new RegisterReward();
            $reward->setUserId($userid);
            if ($componType == 'point') {
                $reward->setType($this->container->getParameter('init_two'));
                $reward->setRewards($this->container->getParameter('init_fivty'));
                $em->persist($reward);
                $em->flush();
                $this->getPointHistory($userid, $this->container->getParameter('init_fivty'));
                $user = $em->getRepository('JiliApiBundle:User')->find($userid);
                $user->setPoints(intval($user->getPoints() + $this->container->getParameter('init_fivty')));
                $em->persist($user);
                $em->flush();
            } else {
                $reward->setType($this->container->getParameter('init_one'));
                $reward->setRewards($this->container->getParameter('init'));
                $em->persist($reward);
                $em->flush();
            }
            return true;
        } else {
            return false;
        }

    }

    //完善后领取(积分或优惠券）
    private function getReward($componType, $id)
    {
        $em = $this->getDoctrine()->getManager();
        if ($componType == 'point') {
            $this->getPointReward($componType, $id);
            return $this->container->getParameter('init_one');
        } else {
            $amazonCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->countCoupon();
            if ($amazonCount == $this->container->getParameter('init')) {
                $isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
                if (empty ($isuserAmazon)) {
                    $this->getPointReward($componType, $id);
                    return $this->container->getParameter('init_three');
                } else {
                    return false;
                }
            } else {
                if ($this->getCoupons($id)) {
                    return $this->container->getParameter('init_two');
                } else {
                    return false;
                }

            }

        }

    }
    //领取亚马逊优惠券
    private function getCoupons($id)
    {
        $em = $this->getDoctrine()->getManager();
        $isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
        if (empty ($isuserAmazon)) {
            $amazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->getAmcoupon();
            // $getCoupon = $amazon['0']['coupon'];
            $amazonCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->find($amazon['0']['id']);
            $amazonCoupon->setUserId($id);
            $em->flush();
            $reward = new RegisterReward();
            $reward->setUserId($id);
            $reward->setType($this->container->getParameter('init_one'));
            $em->persist($reward);
            $em->flush();
            return true;
        } else {
            return false;
        }

    }

    //签到列表
    public function checkinList()
    {
        $arrList = array();
        $date = date('Y-m-d H:i:s');
        $cal_count = "";
        $campaign_multiple = $this->container->getParameter('campaign_multiple');
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $reward_multiple = $user->getRewardMultiple();
        $cal = $em->getRepository('JiliApiBundle:CheckinAdverList')->showCheckinList($uid);
        if (count($cal) > 6) {
            $cal_count = 6;
            $calNow = array_rand($cal, 6); //随机取数组中6个键值
        } else {
            $cal_count = count($cal);
            for ($i = 0; $i < count($cal); $i++) {
                $calNow[$i] = $i;
            }
        }
        for ($i = 0; $i < $cal_count; $i++) {
            $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
            $cal[$calNow[$i]]['reward_rate'] = $cal[$calNow[$i]]['incentive_rate'] * $cal[$calNow[$i]]['reward_rate'] * $cps_rate;
            $cal[$calNow[$i]]['reward_rate'] = round($cal[$calNow[$i]]['reward_rate'] / 10000, 2);
            $arrList[] = $cal[$calNow[$i]];
        }
        return $arrList;

    }


    public function clickDayCount()
    {
        $culTimes = $this->container->getParameter('init');
        $date = date('Y-m-d');
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $culTimes = $em->getRepository('JiliApiBundle:CheckinUserList')->countUserList($uid,$date);
        return $culTimes;

    }


    public function conUnion($str)
    {
        $pattern = '/[^\x00-\x80]/';
        if (preg_match($pattern, $str)) {
            return true; // "含有中文";
        } else {
            return false;
        }
    }

    public function isUnion($str)
    {
        if (!eregi("[^\x80-\xff]", "$str")) {
            return true; //全是中文
        } else {
            return false;
        }
    }

    public function my_substr($str, $start, $len)
    {
        $tmpstr = "";
        $strlen = $start + $len;
        for ($i = 0; $i < $strlen; $i++) {
            if (ord(substr($str, $i, 1)) > 0xa0) {
                $tmpstr .= substr($str, $i, 3);
                $i += 2;
            } else
                $tmpstr .= substr($str, $i, 1);
        }
        return $tmpstr;
    }

    public function countStrs($str)
    {
        $len = strlen($str);
        $i = 0;
        while ($i < $len) {
            if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", $str[$i])) {
                $i += 2;
            } else {
                $i += 1;
            }
        }
        return $i;
    }

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
	 * @Route("/isExistVist", name="_default_isExistVist", options={"expose"=true})
     * @Method("POST");
	 */
    public function isExistVistAction()
    {
        $day = date('Ymd');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        if ($id) {
            $visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
            if (empty ($visit)) {
                $code = $this->container->getParameter('init_one');
            } else {
                $code = $this->container->getParameter('init');
            }
        } else {
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

    }

    /**
	 * @Route("/infoVisit", name="_default_infoVisit", options={"expose"= true})
     * @Method("POST")
	 */
    public function infoVisitAction()
    {
        $day = date('Ymd');
        $logger = $this->get('logger');

        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        if ($id) {

            $visit = $em->getRepository('JiliApiBundle:UserInfoVisit')->getInfoVisit($id, $day);
            if (empty ($visit)) {
                $infoVisit = new UserInfoVisit();
                $infoVisit->setUserId($id);
                $infoVisit->setVisitDate($day);
                $em->persist($infoVisit);
                $em->flush();
                $code = $this->container->getParameter('init_one');
            } else {
                $code = $this->container->getParameter('init');
            }
        } else {
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

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

    public function checkContact($content, $email)
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

        $transport = \Swift_SmtpTransport :: newInstance('smtp.exmail.qq.com', 25)->setUsername('account@91jili.com')->setPassword('D8aspring');
        $mailer = \Swift_Mailer :: newInstance($transport);
        $message = \Swift_Message :: newInstance();
        $message->setSubject($subject);
        $message->setFrom(array (
            'account@91jili.com' => '91问问调查网'
        ));
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
        $flag = $mailer->send($message);
        if (!$flag) {
            $code = 4;
        }
        return $code;
    }

    /**
	* @Route("/wenwenVisit", name="_default_wenwenVisit")
	*/
    public function wenwenVisitAction()
    {
        $day = date('Ymd');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        if ($id) {
            $visit = $em->getRepository('JiliApiBundle:UserWenwenVisit')->getWenwenVisit($id, $day);
            if (empty ($visit)) {
                $wenVisit = new UserWenwenVisit();
                $wenVisit->setUserId($id);
                $wenVisit->setVisitDate($day);
                $em->persist($wenVisit);
                $em->flush();

                $taskList = $this->get('session.task_list');
                // remove from session cache.
                $taskList->remove(array( '91ww_visit'));
            }
            $code = $this->container->getParameter('init_one');
        } else {
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

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
