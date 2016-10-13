<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;

class HelpController extends BaseController
{
    /**
     * @Route("/help", name="help_help")
     */
    public function helpAction()
    {
        return $this->render('WenwenFrontendBundle:Help:index.html.twig');
    }

    /**
     * @Route("/help/issue", name="help_issue")
     */
    public function issueAction()
    {
        return $this->render('WenwenFrontendBundle:Help:issue.html.twig');
    }

    /**
     * @Route("/help/newGuide", name="help_newGuide")
     */
    public function guideAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuide.html.twig');
    }

    /**
     * @Route("/help/newGuide/detail", name="help_newGuide_detail")
     */
    public function guideDetailAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuideDetail.html.twig');
    }

    /**
     * @Route("/help/feedback", name="help_feedback")
     */
    public function feedbackAction()
    {
        return $this->render('WenwenFrontendBundle:Help:feedback.html.twig');
    }

    /**
     * @Route("/help/feedback/finished", name="help_feedback_finished", options={"expose"=true})
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Help:finished.html.twig');
    }
    
    /**
     * @Route("/help/company", name="help_company")
     */
    public function companyAction()
    {
        return $this->render('WenwenFrontendBundle:Help:company.html.twig');
    }

    /**
     * @Route("/help/ww", name="help_ww")
     */
    public function wwAction()
    {
        return $this->render('WenwenFrontendBundle:Help:91ww.html.twig');
    }

    /**
     * @Route("/help/regulations", name="help_regulations")
     */
    public function regulationsAction()
    {
        return $this->render('WenwenFrontendBundle:Help:regulations.html.twig');
    }

    /**
     * @Route("/help/map", name="help_map")
     */
    public function mapAction()
    {
        return $this->render('WenwenFrontendBundle:Help:map.html.twig');
    }

    /**
     * @Route("/help/cooperation", name="help_cooperation")
     */
    public function cooperationAction()
    {
        return $this->render('WenwenFrontendBundle:Help:cooperation.html.twig');
    }

    //从删除的Jili/ApiBundle/Controller/DefaultController还原的代码先贴在这里
    /**
     * @Route("/contact", name="_default_contact", options={"expose"=true})
     */
    public function contactAction()
    {
        try{
            $request = $this->get('request');
            $content = $request->query->get('content');
            $email = $request->query->get('email');
            $code = $this->checkContact($content, $email);
            return new Response($code);
        } catch(\Exception $e) {
            throw $e;
        }

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
        $subject = "来自非91wenwen会员的咨询";
        if ($nick) {
            $id = $session->get('uid');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $subject = "来自" . $nick . " [" . $user->getEmail() . "] 的咨询";
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($this->container->getParameter('webpower_from') => '91问问调查网'))
            ->setSender($this->container->getParameter('webpower_signup_sender'))
            ->setTo($this->container->getParameter('cs_mail'))
            ->setBody('<html>' .
                '<head></head>' .
                '<body>' .
                '咨询内容<br/>' .
                $content . '<br/><br/>' .
                '联系方式<br/>' .
                $email . '<br/><br/>' .
                '浏览器<br/>'.$_SERVER['HTTP_USER_AGENT'] . '<br/>' .
                '</body>' .
                '</html>', 'text/html');

        $mailer = $this->container->get('swiftmailer.mailer.webpower_signup_mailer');
        $flag = $mailer->send($message);
        if (!$flag) {
            $code = 4;
        }
        return $code;
    }
}
