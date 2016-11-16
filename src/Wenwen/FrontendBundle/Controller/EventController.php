<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\ApiBundle\Utility\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserSignInSummary;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * @Route("/event")
 */
class EventController extends BaseController //implements UserAuthenticationController
{
    const ENCODE_TYPE = 'blowfish';
    const SALT = '羞答答的玫瑰静悄悄地开';

    /**
     * @Route("/", name="event", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Event:index.html.twig');
    }

    /**
     * @Route("/invite", name="event_invite", methods={"GET"})
     */
    public function inviteAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($request->getSession()->get('uid'));

        $invitees = $em->getRepository('WenwenFrontendBundle:User')->findBy(array('inviteId' => $user->getId()));

        //跳转到lp页，目的是给将来做运营留推广留一个口
        $inviteUrl = $this->generateUrl('event_invite_lp', array('userId' => $this->encode($user->getId())), true);

        return $this->render('WenwenFrontendBundle:Event:invite.html.twig', array(
            'inviteUrl' => $inviteUrl,
            'inviter' => $user,
            'invitees' => $invitees
        ));
    }

    /**
     * @Route("/invite/{userId}", name="event_invite_lp", methods={"GET"})
     */
    public function inviteLandingPageAction(Request $request, $userId)
    {
        if (isset($userId)) {
            $request->getSession()->set('inviteId', $this->decode($userId));
        }

        return $this->redirect($this->generateUrl('_user_reg'), 301);
    }

    private function encode($userId)
    {
        return urlencode(PasswordEncoder::encode(self::ENCODE_TYPE, (string)$userId, self::SALT));
    }

    private function decode($userId)
    {
        return PasswordEncoder::decode(self::ENCODE_TYPE, urldecode($userId), self::SALT);
    }

    /**
     * @Route("/prize", name="event_prize", methods={"GET"})
     */
    public function prizeAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $pageData = $this->getPageData($user);

        return $this->render('WenwenFrontendBundle:Event:prize.html.twig', $pageData);
    }

    /**
     * @Route("/prize/draw", name="event_prize_draw", methods={"POST"})
     */
    public function prizeDrawAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $points = $this->get('app.prize_service')->drawPrize($user);
        if ($points > 0 && $points < PrizeItem::FIRST_PRIZE_POINTS) {
            $message = '恭喜您，获得' . $points . '积分！';
        } elseif ($points == PrizeItem::FIRST_PRIZE_POINTS) {
            $message = '恭喜您，获得一等奖！请联系客服！';
        } else {
            $message = '感谢参与！';
        }

        if ($points > 0) {
            //中奖用户最新动态
            $news = date('Y-m-d') . ' 用户' . mb_substr($user->getNick(), 0, 3, 'utf8') . '** 抽奖获得' . $points . '积分';
            $this->get('app.latest_news_service')->insertLatestNews($news, CacheKeys::LATEST_PRIZE_NEWS_LIST);
        }

        $pageData = $this->getPageData($user);
        $pageData['message'] = $message;

        return $this->render('WenwenFrontendBundle:Event:prize.html.twig', $pageData);
    }

    /**
     * 获取抽奖页面参数.
     *
     * @param User $user
     * @return array
     */
    private function getPageData(User $user) {
        $prizeTickets = $this->get('app.prize_ticket_service')->getUnusedPrizeTickets($user);
        $prizeTicketCount = count($prizeTickets);

        $pointBalance = $this->get('app.prize_service')->getPointBalance();

        $drawable = $pointBalance > 0 && $prizeTicketCount > 0;

        $latestNewsList = $this->get('app.latest_news_service')->getLatestNews(CacheKeys::LATEST_PRIZE_NEWS_LIST);

        return array(
            'prizeTicketCount' => $prizeTicketCount,
            'pointBalance' => $pointBalance,
            'drawable' => $drawable,
            'latestNewsList' => $latestNewsList,
        );
    }

    /**
     * @Route("/checkin", name="event_checkin", methods={"GET"})
     */
    public function checkInAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $alreadySigned = $this->get('app.sign_in_service')->alreadySigned($user);

        $userSignInSummary = $user->getUserSignInSummary();
        if ($userSignInSummary != null) {
            $signedDays = $userSignInSummary->getConsecutiveDays();
            $unsignedDays = UserSignInSummary::MAX_CONSECUTIVE_DAYS - $signedDays;
            $progress = 100 / UserSignInSummary::MAX_CONSECUTIVE_DAYS * $signedDays;

            if ($signedDays == UserSignInSummary::MAX_CONSECUTIVE_DAYS && !$alreadySigned) {
                $signedDays = 0;
                $unsignedDays = UserSignInSummary::MAX_CONSECUTIVE_DAYS;
                $progress = 0;
            }
        } else {
            $signedDays = 0;
            $unsignedDays = UserSignInSummary::MAX_CONSECUTIVE_DAYS;
            $progress = 0;
        }

        return $this->render('WenwenFrontendBundle:Event:checkin.html.twig', array(
            'alreadySigned' => $alreadySigned,
            'signedDays' => $signedDays,
            'unsignedDays' => $unsignedDays,
            'progress' => $progress
        ));
    }

    /**
     * @Route("/checkin/update", name="event_checkin_update", methods={"POST"})
     */
    public function checkInUpdateAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $signInService = $this->get('app.sign_in_service');
        $alreadySigned = $signInService->alreadySigned($user);
        if (!$alreadySigned) {
            $signInService->signIn($user);
        }

        return $this->redirect($this->generateUrl('event_checkin'));
    }
}