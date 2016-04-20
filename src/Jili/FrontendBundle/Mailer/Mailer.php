<?php
namespace Jili\FrontendBundle\Mailer;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

Class Mailer 
{

    private $mailer;
    private $router;

    private $templating;
    private $translator;

    public function __construct($mailer , RouterInterface $router) {
        $this->mailer  = $mailer;
        $this->router = $router;
    }

    /**
     * @abstract send an email to the new reg user a forgetPassword link to set the email
     * @param string $email User.email
     * @param string $nick User.nick
     * @param integer $user_id User.id
     * @param string $code SetPasswordCode.code of $user_id
     * @return boolean true for email is sent, false for not sent.
     */
    public function sendSignupActivate($email,$nick, $user_id,$code  )
    {
        $url = $this->router->generate('_user_forgetPass',array('code'=>$code, 'id'=>$user_id),true);
        $message = \Swift_Message::newInstance()
            ->setSubject( $this->translator->trans('signup_title', array(), 'mailings') )
            ->setFrom(array('account@91jili.com'=>$this->translator->trans('signup_send_from', array() , 'mailings')))
            ->setTo($email)
            ->setBody($this->templating->render('JiliApiBundle::Mailings/_signup_body.html.twig' , array('nick'=>$nick,'url'=>$url) ), 'text/html');
        $flag = $this->mailer->send($message);
        return $flag === 1;
    }


    public function setTemlating(EngineInterface $templating) 
    {
        $this->templating = $templating;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}
