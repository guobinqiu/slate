<?php
namespace Jili\FrontendBundle\Mailer;

use Symfony\Component\Routing\RouterInterface;

class Mailer 
{

    private $mailer;
    private $router;

    public function __construct($mailer , RouterInterface $router) {
        $this->mailer  = $mailer;
        $this->router = $router;
    }

    public function sendSignupActivate($email,$nick, $user_id,$code  )
    {
        $url = $this->router->generate('_user_forgetPass',array('code'=>$code, 'id'=>$user_id),true);
        $message = \Swift_Message::newInstance()
            ->setSubject('积粒网-注册激活邮件')
            ->setFrom(array('account@91jili.com'=>'积粒网'))
            ->setTo($email)
            ->setBody(
                '<html>' .
                ' <head></head>' .
                ' <body>' .
                '亲爱的'.$nick.'<br/>'.
                '<br/>'.
                '  感谢您注册成为“积粒网”会员！请点击<a href='.$url.' target="_blank">这里</a>，立即激活您的帐户！<br/><br/><br/>' .
                '  注：激活邮件有效期是14天，如果过期后不能激活，请到网站首页重新注册激活。<br/><br/>' .
                '  ++++++++++++++++++++++++++++++++++<br/>' .
                '  积粒网，轻松积米粒，快乐换奖励！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
                ' </body>' .
                '</html>',
                'text/html'
            );
        $flag = $this->mailer->send($message);
        if($flag===1){
            return true;
        }else{
            return false;
        }

    }
}
