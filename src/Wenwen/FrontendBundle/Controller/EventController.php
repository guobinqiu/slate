<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\ApiBundle\Utility\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\Entity\User;
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
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $pageData = $this->getPageData($user);

        return $this->render('WenwenFrontendBundle:Event:prize.html.twig', $pageData);
    }

    /**
     * @Route("/prize/draw", name="event_prize_draw", methods={"GET"})
     */
    public function prizeDrawAction()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $prizeService = $this->get('app.prize_service');

        $prizeTickets = $prizeService->getUnusedPrizeTickets($user);
        $prizeTicketCount = count($prizeTickets);

        $pointBalance = $prizeService->getPointBalance();

        $message = '感谢参与！';
        if ($pointBalance > 0 && $prizeTicketCount > 0) {
            //取得一张奖券
            $prizeTicket = $prizeTickets[0];

            //使用奖券来进行抽奖
            $points = $prizeService->drawPrize($prizeTicket);
            if ($points > 0) {
                $message = '恭喜您，获得' . $points . '积分！';
            }

            //使用过的奖券要作废
            $prizeService->deletePrizeTicket($prizeTicket);

            //中奖用户最新动态
            $news = substr($user->getNick(), 0, 3) . '** 恭喜成为幸运用户，抽中获得' . $points . '积分';
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
        $prizeService = $this->get('app.prize_service');
        $latestNewsService = $this->get('app.latest_news_service');

        $prizeTickets = $prizeService->getUnusedPrizeTickets($user);
        $prizeTicketCount = count($prizeTickets);

        $pointBalance = $prizeService->getPointBalance();

        $drawable = $pointBalance > 0 && $prizeTicketCount > 0;

        $latestNewsList = $latestNewsService->getLatestNews(CacheKeys::LATEST_PRIZE_NEWS_LIST);

        return array(
            'prizeTicketCount' => $prizeTicketCount,
            'pointBalance' => $pointBalance,
            'drawable' => $drawable,
            'latestNewsList' => $latestNewsList,
        );
    }
}