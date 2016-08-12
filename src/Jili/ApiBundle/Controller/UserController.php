<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jili\FrontendBundle\Form\Type\SignupType;
use Jili\ApiBundle\Entity\SetPasswordCode;
use Gregwar\Captcha\CaptchaBuilder;
use Jili\ApiBundle\Entity\IsReadCallboard;
use JMS\JobQueueBundle\Entity\Job;
use Jili\ApiBundle\Validator\Constraints\PasswordRegex;

class UserController extends Controller
{
    /**
     * @Route("/isNewMs/{id}", name="_user_isNewMs", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function isNewMsAction($id)
    {
        $count = $this->notReadMs($id);
        if(  $count>  0){
            return new Response($count);
        }

        $count = $this->notReadCb();
        if( $count > 0) {
            return new Response($count);
        }
        return new Response('');
    }

    /**
     * @Route("/logout", name="_user_logout")
     */
    public function logoutAction()
    {
        $session = $this->get('request')->getSession();
        if($session->has('uid')) {
            $uid = $session->get('uid');
            $this->getDoctrine()->getManager()->getRepository('JiliApiBundle:User')->cleanToken($uid);
        }

        $this->get('request')->getSession()->remove('uid');
        $this->get('request')->getSession()->remove('nick');

        if ($session->has('referer')) {
            $referer_url = $session->get('referer');
            $session->remove('referer');
            if(strlen($referer_url)>0) {
                $response =   new RedirectResponse($referer_url);
            }
        } else {
            $url_homepage = $this->generateUrl('_homepage');
            $response = new RedirectResponse($url_homepage);
        }

        return $response;
    }

    /**
     * @Route("/resetPwd", name="_user_resetPwd", options={"expose"=true})
     */
    public function resetPwdAction()
    {
        return $this->render('WenwenFrontendBundle:User:resetPwdEmail.html.twig');
    }

    /**
     * @Route("/pwdCheck", name="_user_pwdCheck")
     */
    public function pwdCheckAction()
    {
        $request = $this->get('request');
        $email = $request->query->get('email');
        $pwd = $request->query->get('pwd');
        $em_email = $this->getDoctrine()
            ->getRepository('JiliApiBundle:User')
            ->findByEmail($email);
        if(!$em_email){
            $code = $this->container->getParameter('init_one');
        }else{
            $id = $em_email[0]->getId();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            if($user->pw_encode($pwd) != $user->getPwd()){
                $code = $this->container->getParameter('init_one');
            }else{
                $code = $this->container->getParameter('init');
            }
        }
        return new Response($code);
    }

    /**
     * @Route("/login", name="_user_login", options={"expose"=true})
     */
    public function loginAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();

        if($session->has('uid')){
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $goToUrl =  $session->get('referer');
        if(substr($goToUrl, -10) != 'user/login' && strlen($goToUrl)>0 ){
            $session->set('goToUrl', $goToUrl);
            $session->remove('referer');
        }

        $code = '';
        $email = $request->request->get('email');
        $pwd = $request->request->get('pwd');

        //login
        $code = $this->get('login.listener')->login($request);
        if($code == 'ok') {
            $code_redirect = '301';
            $current_url = '';
            if( $request->request->has('referer') ) {
                $current_url = $request->request->get('referer');
            }

            if( strlen(trim($current_url)) == 0  && $session->has('goToUrl') ) {
                $current_url = $session->get('goToUrl');
                $session->remove('goToUrl');
                $session->save();
            }

            if( strlen(trim($current_url)) == 0) {
                $current_url = $this->generateUrl('_homepage');
            } else {
                if( strpos( $current_url,'APIMemberId') !== false) {
                    str_replace('APIMemberId', $session->get('uid'), $current_url);
                    $code_redirect = '302';
                }
            }

            return new RedirectResponse($current_url, $code_redirect);
        }
        return $this->render('WenwenFrontendBundle:User:login.html.twig',array('code'=>$code,'email'=>$email));
    }

    /**
	 * @Route("/checkReg/{id}",requirements={"id" = "\d+"}, name="_user_checkReg")
	 */
    public function checkRegAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if($user)
            $info = $em->getRepository('JiliApiBundle:User')->getUserList($id);
        else
            return $this->redirect($this->generateUrl('_default_error'));
        $arr['user'] = $info[0];
        $arr['email'] = $info[0]['email'];
        return $this->render('WenwenFrontendBundle:User:emailActive.html.twig',$arr);
    }

    /**
	 * @Route("/checkCaptcha", name="_user_checkCaptcha")
	 */
    public function checkCaptchaAction()
    {
        $request = $this->get('request');
        if($this->get('request')->getSession()->get('phrase') != $request->query->get('captcha'))
            $code = $this->container->getParameter('init_one');
        else{
            $this->get('request')->getSession()->remove('phrase');
            $code = $this->container->getParameter('init');
        }
        return new Response($code);
    }

    /**
	 * @Route("/checkEmail", name="_user_checkEmail")
	 */
    public function checkEmailAction()
    {
        $request = $this->get('request');
        $email = $request->query->get('email');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if(empty($user))
            $code = $this->container->getParameter('init_one');
        else
            $code = $this->container->getParameter('init');
        return new Response($code);
    }

    /**
	 * @Route("/checkNick", name="_user_checkNick")
	 */
    public function checkNickAction()
    {
        $request = $this->get('request');
        $nick = $request->query->get('nick');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->findByNick($nick);
        if(empty($user))
            $code = $this->container->getParameter('init_one');
        else
            $code = $this->container->getParameter('init');
        return new Response($code);
    }

    /**
	 * @Route("/reset", name="_user_reset", options={"expose"=true})
	 */
    public function resetAction()
    {
        $code = '';
        $request = $this->get('request');
        $email = $request->query->get('email');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if(empty($user)){
            $code = $this->container->getParameter('chnage_no_email');
            return new Response($code);
        }
        if(!$user[0]->getIsEmailConfirmed()){
            return new Response($this->container->getParameter('init_two'));
        }

        $nick = $user[0]->getNick();
        $id = $user[0]->getId();
        $passCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findByUserId($id);
        $str = 'jiliforgetpassword';
        $password_code = md5($id.str_shuffle($str));
        if(empty($passCode)){
            $url = $this->generateUrl('_user_resetPass',array('code'=>$password_code,'id'=>$id),true);
            if($this->sendMail_reset($url, $email,$nick)){
                $setPasswordCode = new SetPasswordCode();
                $setPasswordCode->setUserId($id);
                $setPasswordCode->setCode($password_code);
                $setPasswordCode->setCreateTime(new \DateTime());
                $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
                $em->persist($setPasswordCode);
                $em->flush();
                $code = $this->container->getParameter('init_one');
            }
        }else{
            $url = $this->generateUrl('_user_resetPass',array('code'=>$password_code,'id'=>$id),true);
            if($this->sendMail_reset($url, $email,$nick)){
                $passCode[0]->setCode($password_code);
                $passCode[0]->setIsAvailable($this->container->getParameter('init_one'));
                $passCode[0]->setCreateTime(new \DateTime());
                $em->flush();
                $code = $this->container->getParameter('init_one');
            }
        }
        return new Response($code);
    }

    /**
	 * @Route("/resetPass/{code}/{id}", name="_user_resetPass")
	 */
    public function resetPassAction($code,$id)
    {
        $arr['codeflag'] = $this->container->getParameter('init');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneByUserId($id);
        if($setPasswordCode->getIsAvailable()==0){
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }
        $arr['pwdcode'] = $setPasswordCode;
        $time = $setPasswordCode->getCreateTime();
        if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600*24){
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }else{
            if($setPasswordCode->getCode() == $code){
                $request = $this->get('request');
                $pwd = $request->request->get('pwd');
                $newPwd = $request->request->get('pwdRepeat');
                if ($request->getMethod() == 'POST'){
                    if($pwd){
                            //用户密码为5-100个字符，密码至少包含1位字母和1位数字
                        $passwordConstraint = new PasswordRegex();
                        $errorList = $this->get('validator')->validateValue($pwd, $passwordConstraint);
                        if (count($errorList) > 0) {
                            $arr['codeflag'] = $this->container->getParameter('init_three');
                            $arr['code'] = $this->container->getParameter('forget_wr_pwd');
                        }else{
                            if($pwd == $newPwd){
#								$this->get('request')->getSession()->set('uid',$id);
#								$this->get('request')->getSession()->set('nick',$user->getNick());
                                $this->get('login.listener')->initSession( $user );

                                $user->setPasswordChoice(\Jili\ApiBundle\Entity\User::PWD_WENWEN);
                                $em->persist($user);

                                $setPasswordCode->setIsAvailable($this->container->getParameter('init'));
                                $em->persist($setPasswordCode);
                                $em->flush();

                                $password_crypt_type = $this->container->getParameter('signup.crypt_method');
                                $password_salt = $this->container->getParameter('signup.salt');
                                $password = \Jili\ApiBundle\Utility\PasswordEncoder::encode($password_crypt_type, $pwd, $password_salt);
                                $em->getRepository('JiliApiBundle:UserWenwenLogin')->createOne(array (
                                    'user_id' => $id,
                                    'password' => $password,
                                    'crypt_type' => $password_crypt_type,
                                    'salt' => $password_salt
                                ));

                                $arr['codeflag'] = $this->container->getParameter('init_one');
                                $arr['code'] = $this->container->getParameter('forget_su_pwd');
                            }else{
                                $arr['codeflag'] = $this->container->getParameter('init_four');
                                $arr['code'] = $this->container->getParameter('forget_unsame_pwd');
                            }
                        }
                    }else{
                        $arr['codeflag'] = $this->container->getParameter('init_two');
                        $arr['code'] = $this->container->getParameter('forget_en_pwd');
                    }

                }
                return $this->render('WenwenFrontendBundle:User:resetPwd.html.twig',$arr);
            }
        }
    }

    /**
	 * @Route("/reSend", name="_user_reSend")
	 */
    public function reSend()
    {
        $request = $this->get('request');
        $id = $request->query->get('id');
        $code = $request->query->get('code');
        $nick = $request->query->get('nick');
        $email = $request->query->get('email');

        $send_email = false;
        $url = $this->generateUrl('_signup_confirm_register', array('register_key'=>$code),true);
        $send_email = $this->sendMail($url, $email,$nick);

        if($send_email){
            $code = $this->container->getParameter('init_one');
        }else{
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

    }

    /**
	 * @Route("/activeEmail/{email}", name="_user_activeEmail", options={"expose"=true})
	 */
    public function activeEmail($email)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        $str = 'jiliactiveregister';
        $code = md5($user[0]->getId().str_shuffle($str));

        $send_email = false;

        //$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$user[0]->getId()),true);
        $url = $this->generateUrl('_signup_confirm_register', array('register_key'=>$code),true);
        $send_email = $this->sendMail($url,$email,$user[0]->getNick());

        if($send_email){
            $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findByUserId($user[0]->getId());
            $setPasswordCode[0]->setCode($code);
            $setPasswordCode[0]->setCreateTime(date_create(date('Y-m-d H:i:s')));
            $setPasswordCode[0]->setIsAvailable($this->container->getParameter('init_one'));
            $em->persist($setPasswordCode[0]);
            $em->flush();

            return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user[0]->getId()),true));
        }else{
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }
    }

    /**
     * todo: refactor the issetReg()
     */
    public function issetReg($email)
    {
        $em = $this->getDoctrine()->getManager();
        $is_pwd = $em->getRepository('JiliApiBundle:User')->isPwd($email);
        if($is_pwd){
            $code = $this->container->getParameter('init_one');//用户已注册
        }else{
            $code = $this->container->getParameter('init_two');//用户未注册
        }
        return $code;
    }

    /**
	 * @Route("/reg", name="_user_reg")
	 */
    public function regAction()
    {
        if($this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_homepage'));
        }




        $request = $this->get('request');
        $form = $this->createForm(new SignupType(), array());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            $logger = $this->get('logger');

            if($form->isValid()) {
              $user_data_inserted = $this->get('signup.form_handler')
                ->setForm($form)
                ->setClientInfo(array(
                  'user_agent'=>$request->headers->get('USER_AGENT'),
                  'remote_address'=>$request->getClientIp()
                ))
                ->process();


              $em = $this->getDoctrine()->getManager();

              $user = $user_data_inserted['user'];
              $setPasswordCode = $user_data_inserted['setPasswordCode'];
              // send signup confirm email
              $args = array(
                  '--subject=[91问问调查网] 请点击链接完成注册，开始有奖问卷调查',
                  '--email='.$user->getEmail(),
                  '--name='.$user->getNick(),
                  '--register_key='. $setPasswordCode->getCode(),
              );
              $job = new Job('mail:signup_confirmation', $args, true, '91wenwen_signup', Job::PRIORITY_HIGH);
              $em->persist($job);
              $em->flush($job);

              $session=$this->get('session');
              // set sucessful message flash
              $session->getFlashBag()->add('notice', '恭喜，注册成功！');
              $session->set('email',$user->getEmail()  );
              return $this->redirect($this->generateUrl('_user_regSuccess'));
            } else {
                $logger->debug('reg error messages'.$form->getErrorsAsString() );
            }
        } //eof POST

        return $this->render('WenwenFrontendBundle:User:register.html.twig',array(
                'form' => $form->createView(),
                ));
    }

    /**
	 * @Route("/agreement", name="_user_agreement")
	 */
    public function agreementAction()
    {
        return $this->render('WenwenFrontendBundle:About:regulations.html.twig');
    }

    /**
	 * @Route("/captcha", name="_user_captcha", options={"expose"=true})
	 */
    public function captchaAction()
    {
        $builder = new CaptchaBuilder;
        $builder->setBackgroundColor(255,255,255);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build();
        header('Content-type: image/jpeg');
        $builder->output();
        $session = new Session();
        $session->start();
        $session->set('phrase', $builder->getPhrase());
        exit;
    }

    /**
	 * @Route("/exchangeLook", name="_user_exchangeLook")
	 */
    public function exchangeLook()
    {
        $code = array();
        $request = $this->get('request');
        $exid = $request->query->get('exid');
        $em = $this->getDoctrine()->getManager();
        $ear = $em->getRepository('JiliApiBundle:ExchangeAmazonResult')->findByExchangeId($exid);

        $code[] = array("a"=>$ear[0]->getAmazonCardOne());
        $code[] = array("a"=>$ear[0]->getAmazonCardTwo());
        $code[] = array("a"=>$ear[0]->getAmazonCardThree());
        $code[] = array("a"=>$ear[0]->getAmazonCardFour());
        $code[] = array("a"=>$ear[0]->getAmazonCardFive());
        return new Response(json_encode($code));
    }

    /**
	 * @Route("/exchange", name="_user_exchange")
	 */
    public function exchangeAction(Request $request)
    {
        $id = $this->get('request')->getSession()->get('uid');
        if(!$id){
           return $this->redirect($this->generateUrl('_user_login'));
        }

        $type = $request->query->get('type', 0);
        $exchangeType = $request->query->get('exchangeType', 1);
        $page = $request->query->get('p', 1);
        $page_size = $this->container->getParameter('page_num');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        if($exchangeType==1){

            // get total count
            $total_count = $em->getRepository('JiliApiBundle:PointsExchange')->getUserExchangeCount($id, $type);
            $page = $page > (int) ceil($total_count / $page_size) ? (int) ceil($total_count / $page_size) : $page;

            //get list
            $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->getUserExchange($id, $type, $page);
            $arr['exchange'] = $exchange;
            $arr['p'] = $page;
            $arr['total'] = $total_count;

        }else if($exchangeType==2){

            // get total count
            $total_count = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->eFrWenByIdCount($id);
            $page = $page > (int) ceil($total_count / $page_size) ? (int) ceil($total_count / $page_size) : $page;

            //get list
            $exchange = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->eFrWenById($id,$page);
            $arr['exchange'] = $exchange;
            $arr['p'] = $page;
            $arr['total'] = $total_count;

        }else{
            return $this->redirect($this->generateUrl('_default_error'));

        }
        $arr['exchangeType'] = $exchangeType;
        $arr['type'] = $type;
        $arr['page_size'] = $page_size;
        return $this->render('WenwenFrontendBundle:Personal:exchangeHistory.html.twig',$arr);
    }

    /**
	 * @Route("/adtaste", name="_user_adtaste")
	 */
    public function adtasteAction(Request $request)
    {
        if(!$this->get('session')->has('uid')){
           return $this->redirect($this->generateUrl('_user_login'));
        }
        $page = $request->query->get('p', 1);
        $type = $request->query->get('type', 0);
        $page_size = $this->container->getParameter('page_num');

        $user_id = $this->get('session')->get('uid');
        $em = $this->getDoctrine()->getManager();
        $total_count = $em->getRepository('JiliApiBundle:TaskHistory0'.($user_id%10))->getTaskHistoryCount($user_id, $type);
        $page = $page > (int) ceil($total_count / $page_size) ? (int) ceil($total_count / $page_size) : $page;

        $this->get('session.my_task_list')->remove(array('alive'));
        $adtaste = $this->get('session.my_task_list')->selTaskHistory($type, $page);

        $arr['p'] = $page;
        $arr['page_size'] = $page_size;
        $arr['total'] = $total_count;
        $arr['type'] = $type;

        $arr['adtaste'] = $adtaste;
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $arr['user'] = $user;
        return $this->render('WenwenFrontendBundle:Personal:taskHistory.html.twig',$arr);
    }

    /**
	 * @Route("/regSuccess", name="_user_regSuccess")
	 */
    public function regSuccessAction()
    {
        $session = $this->get('session');
        $email = $session->get('email');
        return $this->render('WenwenFrontendBundle:User:emailActive.html.twig', array('email' => $email));
    }

    /**
	 * @Route("/updateIsRead", name="_user_updateIsRead", options={"expose"=true})
	 */
    public function updateIsReadAction()
    {
        $isRead = '';
        $code = array();
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $sendid = $request->query->get('sendid');
        $em = $this->getDoctrine()->getManager();
        $isreadInfo = $em->getRepository('JiliApiBundle:IsReadCallboard')->isreadInfo($sendid,$id);
        if(empty($isreadInfo)){
            $isRead = new IsReadCallboard();
            $isRead->setSendCbId($sendid);
            $isRead->setUserId($id);
            $em->persist($isRead);
            $em->flush();
            $isRead = $this->container->getParameter('init_one');
        }
        $sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->find($sendid);
        $content = $sendCb->getContent();
        $code[] = array('content'=>$content,'isRead'=>$isRead);
        return new Response(json_encode($code));
    }

    /**
	 * @Route("/updateSendMs", name="_user_updateSendMs", options={"expose"=true})
	 */
    public function updateSendMsAction()
    {
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $sendid = $request->query->get('sendid');
        $showMs = $this->updateSendMs($id,$sendid);
        return new Response(json_encode($showMs));
    }

    /**
	 * @Route("/message/{sid}",requirements={"sid" = "\d+"}, name="_user_message", options={"expose"=true})
	 */
    public function messageAction($sid)
    {
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        if($sid == $this->container->getParameter('init_two')){//公告
            $sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->getSendCb();
            $userCb = $em->getRepository('JiliApiBundle:IsReadCallboard')->getUserIsRead($id);
            $userIsRead = array();
            foreach ($userCb as $keyCb => $valueCb) {
                $userIsRead[$valueCb['sendCbId']] = $valueCb['sendCbId'];
            }
            $reg_date = $user->getRegisterDate()->format('Y-m-d H:i:s');
            foreach ($sendCb as $key => $value) {
                if($value['createtime']->format('Y-m-d H:i:s') > $reg_date){
                    if(array_key_exists($value['id'],$userIsRead))
                        $sendCb[$key]['isRead'] = $this->container->getParameter('init_one');
                    else
                        $sendCb[$key]['isRead'] = '';
                }else{
                    unset($sendCb[$key]);
                }

            }
            $arr['sendCb'] = $sendCb;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator->paginate(
                $sendCb,
                $this->get('request')->query->get('page', 1),
                $this->container->getParameter('page_num')
            );
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }
        if($sid == $this->container->getParameter('init_one')){//消息
            $showMs  = $this->selectSendMs($id);
            $arr['showMs'] = $showMs;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator->paginate(
                $showMs,
                $this->get('request')->query->get('page', 1),
                $this->container->getParameter('page_num')
            );
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }
        $arr['sid'] = $sid;
        return $this->render('WenwenFrontendBundle:Personal:message.html.twig',$arr);
    }

    /**
     * @Route("/countMs", name="_user_countMs", options={"expose"= true}, methods={"POST"})
     */
    public function countMsAction()
    {
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $countCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountAllCallboard($user->getRegisterDate()->format('Y-m-d H:i:s'));
        $countIsCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountIsReadCallboard($id);
        $countMs = $this->countSendMs($id);
        $notRead = intval($countMs[0]['num']) + intval($countCb[0]['num']) - intval($countIsCb[0]['num']);
        return new Response($notRead);
    }

    /**
	* @Route("/mission/{id}", name="_user_mission")
	*/
    public function missionAction($id)
    {
        $str = 'jiliforgetpassword';
        $code = md5($id.str_shuffle($str));
        $email = '278583642@qq.com';
        $nick = '';
        $url = $this->generateUrl('_signup_confirm_register', array('register_key'=>$code),true);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if($this->sendMail($url, $email,$nick)){
            $setPasswordCode = new SetPasswordCode();
            $setPasswordCode->setUserId($user->getId());
            $setPasswordCode->setCode($code);
            $setPasswordCode->setCreateTime(date_create(date('Y-m-d H:i:s')));
            $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
            $em->persist($setPasswordCode);
            $em->flush();
            echo 'success';
        }

        return $this->render('JiliApiBundle:User:mission.html.twig');
    }

    //reset pwd send mail
    public function sendMail_reset($url,$email,$nick)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('91问问-帐号密码重置')
        ->setFrom(array($this->container->getParameter('webpower_from') => '91问问调查网'))
        ->setSender($this->container->getParameter('webpower_signup_sender'))
        ->setTo($email)
        ->setBody(
                '<html>' .
                ' <head></head>' .
                ' <body>' .
                '亲爱的'.$nick.'<br/>'.
                '<br/>'.
                '  我们收到您因为忘记密码，要求重置91问问帐号密码的申请，请点击<a href='.$url.' target="_blank" style="color: #0000ee;">这里</a>重置您的密码。<br/><br/>' .
                '  如果您并未提交重置密码的申请，请忽略本邮件，并关注您的账号安全，因为可能有其他人试图登录您的账户。<br/><br/>91问问运营中心' .
                ' </body>' .
                '</html>',
                'text/html'
        );
        $flag = $this->get('swiftmailer.mailer.webpower_signup_mailer')->send($message);
        if($flag===1){
            return true;
        }else{
            return false;
        }
    }

    public function sendMail($url,$email,$nick)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('91问问-注册激活邮件')
        ->setFrom(array($this->container->getParameter('webpower_from') => '91问问调查网'))
        ->setSender($this->container->getParameter('webpower_signup_sender'))
        ->setTo($email)
        ->setBody(
                        '<html>' .
                        ' <head></head>' .
                        ' <body>' .
                        '亲爱的'.$nick.'<br/>'.
                        '<br/>'.
                        '  感谢您注册成为“91问问”会员！请点击<a href='.$url.' target="_blank" style="color: #0000ee;">这里</a>，立即激活您的帐户！<br/><br/><br/>' .
                        '  注：激活邮件有效期是14天，如果过期后不能激活，请到网站首页重新注册激活。<br/><br/>' .
                        ' </body>' .
                        '</html>',
                        'text/html'
        );
        $flag = $this->get('swiftmailer.mailer.webpower_signup_mailer')->send($message);
        if($flag===1){
            return true;
        }else{
            return false;
        }
    }

    private function updateSendMs($userid,$sendid)
    {
        $isRead = '';
        $code = array();
        $em = $this->getDoctrine()->getManager();
        $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ($userid % 10) );
        $updateSm = $sm->find($sendid);
        if($updateSm->getReadFlag() == 0){
            $updateSm->setReadFlag($this->container->getParameter('init_one'));
            $em->persist($updateSm);
            $em->flush();
            $isRead = $this->container->getParameter('init_one');
        }
        $code[] = array('content'=>$updateSm->getContent(),'isRead'=>$isRead);
        return $code;
    }

    private function countSendMs($userid)
    {
      $em = $this->getDoctrine()->getManager();
      $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ($userid % 10));
      $countMs = $sm->CountSendMs($userid);
      return $countMs;
    }

    private function selectSendMs($userid)
    {
      return  $this->getDoctrine()->getManager()->getRepository('JiliApiBundle:SendMessage0'. ($userid % 10) )->getSendMsById($userid);
    }

    private function notReadCb()
    {
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $countCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountAllCallboard($user->getRegisterDate()->format('Y-m-d H:i:s'));
        $countIsCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountIsReadCallboard($id);
        $countUserCb = intval($countCb[0]['num']) - intval($countIsCb[0]['num']);
        return $countUserCb;
    }

    private function notReadMs($id)
    {
        $countUserMs = $this->countSendMs($id);
        return $countUserMs[0]['num'];
    }
}