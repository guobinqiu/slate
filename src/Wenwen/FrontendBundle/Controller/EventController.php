<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\ApiBundle\Utility\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        if (!$request->getSession()->has('uid')) {
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

    /**
     * @Route("/lottery", name="event_lottery", methods={"GET"})
     */
    public function lotteryAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $pointBalance = $this->get('app.lottery_service')->getPointBalance();
        $ticketCount = count($user->getUnusedLotteryTickets());
        $drawable = $pointBalance > 0 && $ticketCount > 0;
        $latestNewsList = $this->get('app.latest_news_service')->getLatestNews(CacheKeys::LATEST_PRIZE_NEWS_LIST);

        return $this->render('WenwenFrontendBundle:Event:draw.html.twig', array(
            'pointBalance' => $pointBalance,
            'ticketCount' => $ticketCount,
            'drawable' => $drawable,
            'latestNewsList' => $latestNewsList,
        ));
    }

    /**
     * @Route("/lottery/draw", name="event_lottery_draw", methods={"GET"})
     */
    public function lotteryDrawAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $lotteryService = $this->get('app.lottery_service');
        $latestNewsService = $this->get('app.latest_news_service');

        $pointBalance = $this->get('app.lottery_service')->getPointBalance();
        $lotteryTickets = $user->getUnusedLotteryTickets();
        $ticketCount = count($lotteryTickets);

        $message = '感谢参与';
        if ($pointBalance > 0 && $ticketCount > 0) {
            //取得一张奖券
            $lotteryTicket = $lotteryTickets[0];

            //使用奖券来进行抽奖
            $points = $lotteryService->drawPrize($lotteryTicket);
            if ($points > 0) {
                $message = '恭喜您，获得' . $points . '积分！';
            }

            //使用过的奖券要作废
            $lotteryService->deleteLotteryTicket($lotteryTicket);

            //中奖用户最新动态
            $news = substr($user->getNick(), 0, 3) . '** 恭喜成为幸运用户，抽中获得' . $points . '积分';
            $latestNewsService->insertLatestNews($news, CacheKeys::LATEST_PRIZE_NEWS_LIST);
        }

        //抽过奖积分发生变化了需要重新检查一遍
        $pointBalance = $lotteryService->getPointBalance();
        $ticketCount = count($user->getUnusedLotteryTickets());
        $drawable = $pointBalance > 0 && $ticketCount > 0;
        $latestNewsList = $latestNewsService->getLatestNews(CacheKeys::LATEST_PRIZE_NEWS_LIST);

        return $this->render('WenwenFrontendBundle:Event:draw.html.twig', array(
            'pointBalance' => $pointBalance,
            'ticketCount' => $ticketCount,
            'message' => $message,
            'drawable' => $drawable,
            'latestNewsList' => $latestNewsList,
        ));
    }

    private function encode($userId)
    {
        return urlencode(PasswordEncoder::encode(self::ENCODE_TYPE, (string)$userId, self::SALT));
    }

    private function decode($userId)
    {
        return PasswordEncoder::decode(self::ENCODE_TYPE, urldecode($userId), self::SALT);
    }
}