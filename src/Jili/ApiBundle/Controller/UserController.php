<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jili\ApiBundle\Form\ForgetPasswordType;
use Jili\ApiBundle\Form\RegType;
use Jili\ApiBundle\Form\Type\SignupActivateType;
use Jili\FrontendBundle\Form\Type\SignupType;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\TaskOrder;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Entity\LoginLog;
use Jili\ApiBundle\Entity\SetPasswordCode;
use Jili\ApiBundle\Entity\AmazonCoupon;
use Jili\ApiBundle\Entity\RegisterReward;
use Gregwar\Captcha\CaptchaBuilder;

use Jili\ApiBundle\Entity\SendCallboard;
use Jili\ApiBundle\Entity\IsReadCallboard;

use Jili\ApiBundle\Entity\PointHistory00;
use Jili\ApiBundle\Entity\PointHistory01;
use Jili\ApiBundle\Entity\PointHistory02;
use Jili\ApiBundle\Entity\PointHistory03;
use Jili\ApiBundle\Entity\PointHistory04;
use Jili\ApiBundle\Entity\PointHistory05;
use Jili\ApiBundle\Entity\PointHistory06;
use Jili\ApiBundle\Entity\PointHistory07;
use Jili\ApiBundle\Entity\PointHistory08;
use Jili\ApiBundle\Entity\PointHistory09;
use Jili\ApiBundle\Entity\TaskHistory00;
use Jili\ApiBundle\Entity\TaskHistory01;
use Jili\ApiBundle\Entity\TaskHistory02;
use Jili\ApiBundle\Entity\TaskHistory03;
use Jili\ApiBundle\Entity\TaskHistory04;
use Jili\ApiBundle\Entity\TaskHistory05;
use Jili\ApiBundle\Entity\TaskHistory06;
use Jili\ApiBundle\Entity\TaskHistory07;
use Jili\ApiBundle\Entity\TaskHistory08;
use Jili\ApiBundle\Entity\TaskHistory09;

use Jili\ApiBundle\Utility\ValidateUtil;
use Jili\FrontendBundle\Controller\CampaignTrackingController;
use JMS\JobQueueBundle\Entity\Job;

class UserController extends Controller implements CampaignTrackingController
{
    /**
	* @Route("/createFlag", name="_user_createFlag", options={"expose"=true})
    * @Method("POST")
	*/
    public function createFlagAction()
    {
        $code = '';
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if($user->getDeleteFlag() == 1){
                $this->removeSession();
                $code = $this->container->getParameter('init_one');
        }
        if(!$request->getSession()->get('flag')){
            $session = $this->getRequest()->getSession();
            $session->set('flag', 1);

            //update last logindate
            $user->setLastLoginDate(date_create(date('Y-m-d H:i:s')));
            $user->setLastLoginIp($this->get('request')->getClientIp());
            $em->flush();

            $loginlog = new Loginlog();
            $loginlog->setUserId($id);
            $loginlog->setLoginDate(date_create(date('Y-m-d H:i:s')));
            $loginlog->setLoginIp($this->get('request')->getClientIp());
            $em->persist($loginlog);
            $em->flush();
            if($user->getDeleteFlag() == 1){
                $this->removeSession();
                $code = $this->container->getParameter('init_one');
            }
        }
        return new Response($code);

    }

    public function removeSession()
    {
        $this->get('request')->getSession()->remove('uid');
        $this->get('request')->getSession()->remove('nick');
    }

    /**
     * @Route("/checkFlag/{id}", name="_user_checkFlag")
     */
    public function checkFlagAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        return new Response($user->getIsInfoSet());
    }

    /**
     * @Route("/checkPwd", name="_user_checkPwd")
     */
    public function checkPwdAction()
    {
        $request = $this->get('request');
        $pwd = $request->query->get('pwd');
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if($user->pw_encode($pwd) == $user->getPwd())
            $code = $this->container->getParameter('init');
        else
            $code = $this->container->getParameter('init_one');
        return new Response($code);
    }

    /**
     * @Route("/updatePwd", name="_user_updatePwd")
     */
    public function updatePwdAction()
    {
        return $this->render('JiliApiBundle:User:changePwd.html.twig');
    }

    /**
     * @Route("/changePwd", name="_user_changePwd")
     */
    public function changePwdAction()
    {
        $arr['codeflag'] = '';
        $arr['code'] = '';
        $request = $this->get('request');
        $oldPwd =  $request->request->get('oldPwd');
        $pwd = $request->request->get('pwd');
        $newPwd = $request->request->get('newPwd');
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if ($request->getMethod() == 'POST') {
            if($oldPwd){
                if($user->pw_encode($oldPwd) == $user->getPwd()){
                    if($pwd){
                        if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
                            $arr['code'] = $this->container->getParameter('change_wr_pwd');
                            $arr['codeflag'] = $this->container->getParameter('init_three');
                        }else{
                            if($pwd == $newPwd){
                                $user->setPwd($pwd);
                                $em->flush();
                                $arr['codeflag'] = $this->container->getParameter('init_one');
                                $arr['code'] = $this->container->getParameter('forget_su_pwd');
                            }else{
                                $arr['codeflag'] = $this->container->getParameter('init_four');
                                $arr['code'] = $this->container->getParameter('change_unsame_pwd');
                            }
                        }
                    }else{
                        $arr['code'] = $this->container->getParameter('change_en_newpwd');
                        $arr['codeflag'] = $this->container->getParameter('init_five');
                    }
                }else{
                    $arr['code'] = $this->container->getParameter('change_wr_oldpwd');
                    $arr['codeflag'] = $this->container->getParameter('init_two');
                }
            }else{
                $arr['code'] = $this->container->getParameter('change_en_oldpwd');
                $arr['codeflag'] = $this->container->getParameter('init_six');
            }

        }
        return $this->render('JiliApiBundle:User:changePwd.html.twig',$arr);
    }


    /**
     * @Route("/resUp", name="_user_resUp")
     */
    public function resUp()
    {
        $request = $this->get('request');
        if($request->getSession()->get('uid')){
            $id = $request->getSession()->get('uid');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            if ($request->getMethod() == 'POST'){
                if($request->request->get('resize')){
                    $resizePath = $request->request->get('resizePath');
                    $x = $request->request->get('x');
                    $y = $request->request->get('y');
                    $x1 = $request->request->get('w');
                    $y1 = $request->request->get('h');
                    if(!$x)
                        $x=0;
                    if(!$y)
                        $y=0;
                    if(!$x1)
                        $x1=256;
                    if(!$y1)
                        $y1=256;
                    $user->resizeUpload($resizePath,$x,$y,$x1,$y1);
                    $user->setIconPath($resizePath);
                    $em->flush();

                }
                return $this->redirect($this->generateUrl('_profile_edit'));

            }
        }
    }



    /**
     * @Route("/getCity", name="_user_getCity", options={"expose"=true})
     * @Method("POST")
     */
    public function getCityAction()
    {
        $array = array();
        //$arr[] = array('id'=>0,'cityName'=>'请选择地区');
        $request = $this->get('request');
        $cid = $request->query->get('cid');
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository('JiliApiBundle:CityList')->findByProvinceId($cid);
        if($city){
            foreach ($city as $key => $value){
                $arr[] = array('id'=>$value->getId(),'cityName'=>$value->getCityName());
            }
            return new Response(json_encode($arr));
        }else{
            return new Response('');
        }

    }

    /**
     * @Route("/getCoupon", name="_user_getCoupon")
     */
    public function getCouponAction()
    {
        $code = '';
        $getCoupon = '';
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        if(!$id){
            return $this->redirect($this->generateUrl('_homepage'));
        }
        $em = $this->getDoctrine()->getManager();

        $userCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->isUserCoupon($id);
        if($userCount > $this->container->getParameter('init')){
            $code = $this->container->getParameter('init_one');
            $userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
            $getCoupon = $userCoupon[0]->getCoupon();
        }else{
            $amazonCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->countCoupon();
            if($amazonCount > $this->container->getParameter('init')){
                $amazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->getAmcoupon();
                $getCoupon = $amazon['0']['coupon'];
                $amazonCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->find($amazon['0']['id']);
                $amazonCoupon->setUserId($id);
                $em->flush();
            }else{
                $code = $this->container->getParameter('init_two');
            }
        }
        return $this->render('JiliApiBundle:User:getCoupon.html.twig',array(
            'coupon'=>$getCoupon,
            'code'=>$code
        ));

    }
    //领取亚马逊优惠券
    private function getCoupons($id)
    {
        $em = $this->getDoctrine()->getManager();
        $isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
        if(empty($isuserAmazon)){
            $amazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->getAmcoupon();
            // $getCoupon = $amazon['0']['coupon'];
            $amazonCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->find($amazon['0']['id']);
            $amazonCoupon->setUserId($id);
            $em->flush();
            $reward =  new RegisterReward();
            $reward->setUserId($id);
            $reward->setType($this->container->getParameter('init_one'));
            $em->persist($reward);
            $em->flush();
            return true;
        }else{
            return false;
        }

    }
    // 是否完善资料
    private function isExistInfo($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($userid);
        if($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
            return true;
        else
            return false;
    }
    //是否给过奖励
    private function isGetReward($userid,$componType)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
        if($user){
            if($componType == 'point'){
                if($user[0]->getType() == $this->container->getParameter('init_one'))
                    return $this->container->getParameter('init_four');//参加其他活动领取的
                if($user[0]->getType() == $this->container->getParameter('init_two'))
                    return $this->container->getParameter('init_one');
            }else{
                if($user[0]->getType() == $this->container->getParameter('init_one')){
                    $isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($userid);
                    if(empty($isuserAmazon)){
                        return $this->container->getParameter('init_three');//获得米粒的
                    }else{
                        return $this->container->getParameter('init_two');//获得优惠券的
                    }
                }else{
                    return $this->container->getParameter('init_four');//参加其他活动领取的
                }
            }
        }else{
            return false;
        }

    }

    //给米粒奖励
    private function getPointReward($componType,$userid)
    {
        $em = $this->getDoctrine()->getManager();
        $isuserPoint = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
        if(empty($isuserPoint)){
            $reward =  new RegisterReward();
            $reward->setUserId($userid);
            if($componType == 'point'){
                $reward->setType($this->container->getParameter('init_two'));
                $reward->setRewards($this->container->getParameter('init_fivty'));
                $em->persist($reward);
                $em->flush();

                // insert point_history
                $points_params = array (
                    'userid' => $userid,
                    'point' => $this->container->getParameter('init_fivty'),
                    'type' => AdCategory::ID_PROFILE_ACCOMPLISHMENT
                );

                $this->get('general_api.point_history')->get($points_params);

                $user = $em->getRepository('JiliApiBundle:User')->find($userid);
                $user->setPoints(intval($user->getPoints()+$this->container->getParameter('init_fivty')));
                $em->persist($user);
                $em->flush();
            }else{
                $reward->setType($this->container->getParameter('init_one'));
                $reward->setRewards($this->container->getParameter('init'));
                $em->persist($reward);
                $em->flush();
            }
            return true;
        }else{
            return false;
        }

    }

    //完善后领取(积分或优惠券）
    private function getReward($componType,$id)
    {
        $em = $this->getDoctrine()->getManager();
        if($componType == 'point'){
            $this->getPointReward($componType,$id);
            return $this->container->getParameter('init_one');
        }else{
            $amazonCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->countCoupon();
            if($amazonCount == $this->container->getParameter('init')){
                $isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
                if(empty($isuserAmazon)){
                    $this->getPointReward($componType,$id);
                    return $this->container->getParameter('init_three');
                }else{
                    return false;
                }
            }else{
                if($this->getCoupons($id)){
                    return $this->container->getParameter('init_two');
                }else{
                    return false;
                }

            }

        }

    }


    /**
     * @Route("/amazonResult", name="_user_amazonResult")
     */
    public function amazonResultAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $couponOd = '';
        $couponElec = '';
        $uid = '';
        $uid = $request->getSession()->get('uid');
        if($uid){
            $userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($uid);
            if(!empty($userCoupon)){
                $couponOd = $userCoupon[0]->getCouponOd();
                $couponElec = $userCoupon[0]->getCouponElec();
            }
        }else{
            return $this->redirect($this->generateUrl('_homepage'));
        }
        return $this->render('JiliApiBundle:User:amazonResult.html.twig',array(
            'couponOd'=>$couponOd,
            'couponElec'=>$couponElec,
            'uid'=>$uid
        ));
    }

    /**
     * @Route("/isExistInfo", name="_user_isExistInfo",options={"expose"=true})
     * @Method("POST")
     */
    public function isExistInfoAction()
    {
        $code = '';
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
            $code = $this->container->getParameter('init');
        else
            $code = $this->container->getParameter('init_one');
        return new Response($code);
    }

    /**
     * @Route("/activty", name="_user_activty")
     */
    public function activtyAction()
    {
        $componType = 'activity';
        return new Response($componType);
    }

    /**
     * @Route("/province", name="_user_province", options = {"expose"= true})
     * @Method("POST")
     */
    public function provinceAction()
    {
        $arr = array();
        $em = $this->getDoctrine()->getManager();
        $province = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
        foreach ($province as $key => $value) {
            $arr[] = array('id'=>$value->getId(),'provinceName'=>$value->getProvinceName());
        }
        return new Response(json_encode($arr));
    }

    /**
     * @Route("/hobby", name="_user_hobby", options={"expose"=true})
     * @Method("POST")
     */
    public function hobbyAction()
    {
        $arr = array();
        $em = $this->getDoctrine()->getManager();
        $hobby = $em->getRepository('JiliApiBundle:HobbyList')->findAll();
        foreach ($hobby as $key => $value) {
            $arr[] = array('id'=>$value->getId(),'hobby'=>$value->getHobbyName());
        }
        return new Response(json_encode($arr));
    }

    /**
     * @Route("/income", name="_user_income", options={"expose"=true})
     * @Method("POST")
     */
    public function incomeAction()
    {
        $arr = array();
        $em = $this->getDoctrine()->getManager();
        $income = $em->getRepository('JiliApiBundle:MonthIncome')->findAll();
        unset($income[0]);
        unset($income[1]);
        unset($income[2]);
        unset($income[3]);
        foreach ($income as $key => $value) {
            $arr[] = array('id'=>$value->getId(),'income'=>$value->getIncome());
        }
        return new Response(json_encode($arr));
    }


    /**
     * @Route("/userInfo", name="_user_userInfo", options={"expose"=true})
     * @Method("POST")
     */
    public function userInfoAction()
    {
        $arr = array();
        $mobile = '';
        $sex = '';
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $mobile = $user->getTel();
        $sex = $user->getSex();
        $arr[] = array(
            'id'=>$user->getId(),
            'email'=>$user->getEmail(),
            'nick'=>$user->getNick(),
            'sex'=>$sex,
            'mobile'=>$mobile,
        );
        return new Response(json_encode($arr));
    }

    /**
     * @Route("/isReward", name="_user_isReward")
     */
    public function isRewardAction()
    {
        $componType = 'activity';
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        if($this->isExistInfo($id)){
            if($this->isGetReward($id,$componType)){
                $isgetReward = $this->isGetReward($id,$componType);
                if($isgetReward ==  $this->container->getParameter('init_two')){
                    $userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
                    $getInfoReward = $userCoupon[0]->getCoupon();
                }
            }else{
                $rsReward = $this->getReward($componType,$id);
                if($rsReward == $this->container->getParameter('init_two')){
                    $userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
                    $getInfoReward = $userCoupon[0]->getCoupon();
                }
            }
        }else{


        }


    }




    /**
     * @Route("/registerReward", name="_user_registerReward")
     * @Method({"GET","POST"})
     */
    public function registerRewardAction()
    {
        $code = array();
        $codeflag = '';
        $birthday = '';
        $city = '';
        $month_income = '';
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $sex = $request->request->get('sex');
        $tel = $request->request->get('tel');
        $year = $request->request->get('year');
        $month = $request->request->get('month');
        $provinceId = $request->request->get('province');
        $city = $request->request->get('city');
        $month_income = $request->request->get('income');
        $hobby = $request->request->get('hobby');
        if($sex && $year && $city && $month_income && $hobby){
            if($year){
                $birthday = $year;
                if($month)
                    $birthday = $birthday.'-'.$month;
            }
            if($hobby){
                $hobbys  = substr($hobby,0,strlen($hobby)-1);
            }
            if($tel){
                if(!(ValidateUtil::validateMobile($tel))){
                    $codeflag = $this->container->getParameter('update_wr_mobile');
                    $code[] = array("code"=>$this->container->getParameter('init_five'),"msg"=>$codeflag);
                    return new Response(json_encode($code));
                }
            }

            $user->setSex($sex);
            $user->setBirthday($birthday);
            $user->setProvince($provinceId);
            $user->setCity($city);
            $user->setTel($tel);
            $user->setIncome($month_income);
            $user->setHobby($hobbys);
            $user->setIsInfoSet($this->container->getParameter('init_one'));
            $em->flush();

            //获得分数， user, point_history , task_history
            $point = $this->getPointsForReward();
            if($this->issetPoints($id)){
                $this->updatePoint($id,$point);
            }
            $code[] = array("code"=>$this->container->getParameter('init_one'));
        }else{
            $codeflag = $this->container->getParameter('reg_mobile');
            $code[] = array("code"=>$this->container->getParameter('init_four'),"msg"=>$codeflag);
        }

        return new Response(json_encode($code));
    }

    //获得分数
    private function getPointsForReward()
    {
        $em = $this->getDoctrine()->getManager();
        //默认值
        $maxPoint = 10;//永久为10个，默认情况
        //判断是否是当天注册
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        if($uid){
            $user = $em->getRepository('JiliApiBundle:User')->find($uid);
            $reg_date = $user->getRegisterDate()->format('Y-m-d');
            //注册当天填写属性
            if(date('Y-m-d') == $reg_date){
                // 4月份
                if(date('Ym') == '201404'){
                    $maxPoint = 30;//4月份当天注册，并填写属性
                }else{
                    $maxPoint = 20;//4月以后当天注册，并填写属性
                }
            }
        }
        return $maxPoint;
    }

    //判断是否获得过分数
    private function issetPoints($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('JiliApiBundle:PointHistory0'. ( $userid % 10));
        $task_order = $task->issetInsertReward($userid);
        if(empty($task_order)){
            return true;
        }else{
            return false;
        }
    }

    //更新point: user, point_history , task_history
    private function updatePoint($userId,$point)
    {
        $em = $this->getDoctrine()->getManager();

        //更新user表总分数
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);
        $oldPoint = $user->getPoints();
        $user->setPoints(intval($oldPoint+$point));
        $em->persist($user);
        $em->flush();

        //更新point_history表分数
        $params = array (
            'userid' => $userId,
            'point' => $point,
            'type' => 9,//9:完善资料
        );
        $pointLister = $this->get('general_api.point_history');
        $pointLister->get($params);

        //更新task_history表分数
        $params = array (
            'userid' => $userId,
            'orderId' => 0,
            'taskType' => 4,
            'categoryType' => 9,//9:完善资料
            'task_name' => '完善资料获取米粒',
            'point' => $point,
            'date' => date_create(date('Y-m-d H:i:s')),
            'status' => 1
        );
        $taskLister = $this->get('general_api.task_history');
        $taskLister->init($params);
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

    /**
     * @Route("/isNewMs/{id}", name="_user_isNewMs", options={"expose"=true})
     * @Method({ "GET", "POST"})
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
     * @Route("/info", name="_user_info",requirements={"_scheme"="https"})
     */
    public function infoAction()
    {
        // $existUserinfo = '';
        $existUserinfo = $this->container->getParameter('init_one');
        $countMessage = '';
        $code = '';
        $flag = '';
        $codeflag = '';
        $birthday = '';
        $city = '';
        $month_income = '';
        $newYear = $this->container->getParameter('init');
        $newMonth = $this->container->getParameter('init');
        $newHobby = '';
        $disarea = '';
        $usercomes = '';
        $userProHobby = '';
        $daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        if($user->getHobby()){
            $userProHobby = explode(",",$user->getHobby());
            foreach ($userProHobby as $key => $value) {
                $userHobby = $em->getRepository('JiliApiBundle:HobbyList')->find($value);
                $userHobbyList[] = $userHobby->getHobbyName();
            }
            $newHobby = implode(',',$userHobbyList);
        }

        if($user->getBirthday()){
            if(strlen($user->getBirthday())>5){
                $newBirthday = explode("-",$user->getBirthday());
                $newYear = $newBirthday[0];
                $newMonth = $newBirthday[1];
            }else{
                $newYear = $user->getBirthday();
            }
        }
        $hobbyList = $em->getRepository('JiliApiBundle:HobbyList')->findAll();
        $province = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
        $income = $em->getRepository('JiliApiBundle:MonthIncome')->findAll();
        unset($income[0]);
        unset($income[1]);
        unset($income[2]);
        unset($income[3]);
        $option = array('status' => 0 ,'offset'=>'1','limit'=>'10');
        $option_ex = array('daytype' => 0 ,'offset'=>'1','limit'=>'10');
#       $adtaste = $this->selTaskHistory($id,$option);
        $this->get('session.my_task_list')->remove(array('alive'));
        $adtaste = $this->get('session.my_task_list')->selTaskHistory($option);
        foreach ($adtaste as $key => $value) {
            if($value['orderStatus'] == 1 && $value['type'] ==1){
                unset($adtaste[$key]);
            }
        }
        $adtasteNum = count($adtaste);
        $exchange = $em->getRepository('JiliApiBundle:PointsExchange');
        $exchange = $exchange->getUserExchange($id,$option_ex);
        $exFrWen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->eFrWenByIdMaxTen($id);
        $sex = $request->request->get('sex');
        $tel = $request->request->get('tel');
        $year = $request->request->get('year');
        $month = $request->request->get('month');
        $day = $request->request->get('day');
        $provinceId = $request->request->get('province');
        $city = $request->request->get('city');
        $month_income = $request->request->get('income');
        $profession = $request->request->get('profession');
        $category = $request->request->get('category');
        $department = $request->request->get('department');
        $education = $request->request->get('education');
        $hobby = $request->request->get('hobby');
        $signature = $request->request->get('signature');
        $music = $request->request->get('music');
        $wish = $request->request->get('wish');
        $form  = $this->createForm(new RegType(), $user);
        if ($request->getMethod() == 'POST') {
            if($request->request->get('update')){
                if($sex && $year && $city && $month_income && $hobby){
                    if($year){
                        $birthday = $year;
                        if($month)
                            $birthday = $birthday.'-'.$month;
                    }
                    if($hobby)
                        $hobbys = implode(",",$hobby);
                    if($tel){
                        if(!(ValidateUtil::validateMobile($tel))){
                            $codeflag = $this->container->getParameter('update_wr_mobile');
                        }else{
                            $user->setSex($sex);
                            $user->setBirthday($birthday);
                            $user->setProvince($provinceId);
                            $user->setCity($city);
                            $user->setTel($tel);
                            $user->setIncome($month_income);
                            $user->setHobby($hobbys);
                            $user->setIsInfoSet($this->container->getParameter('init_one'));
                            $em->flush();

                            $this->get('login.listener')->updateInfoSession($user);
                            return $this->redirect($this->generateUrl('_user_info'));
                        }
                    }else{
                        $user->setSex($sex);
                        $user->setBirthday($birthday);
                        $user->setProvince($provinceId);
                        $user->setCity($city);
                        $user->setTel($tel);
                        $user->setIncome($month_income);
                        $user->setHobby($hobbys);
                        $user->setIsInfoSet($this->container->getParameter('init_one'));
                        $em->flush();

                        $this->get('login.listener')->updateInfoSession($user);
                        return $this->redirect($this->generateUrl('_user_info'));
                    }
                }else{
                    $codeflag = $codeflag = $this->container->getParameter('reg_mobile');
                }
            }else{
                if($request->request->get('reset')){
                    $this->get('login.listener')->updateInfoSession($user);
                    return $this->redirect($this->generateUrl('_user_info'));
                }else{
                    $form->bind($request);
                    $path =  $this->container->getParameter('upload_tmp_dir');
                    $code = $user->upload($path);
                    if($code == $this->container->getParameter('init_one')){
                        $code =  $this->container->getParameter('upload_img_type');
                    }
                    if($code == $this->container->getParameter('init_two')){
                        $code =  $this->container->getParameter('upload_img_size');
                    }

                    $this->get('login.listener')->updateInfoSession($user);
                    return new Response(json_encode($code));
                }
            }
        }
        //用户地区
        if($user->getProvince()){
            $userProvince = $em->getRepository('JiliApiBundle:ProvinceList')->find($user->getProvince());
            if($user->getCity()){
                $userCity = $em->getRepository('JiliApiBundle:CityList')->find($user->getCity());
                $disarea = $userProvince->getProvinceName()." ".$userCity->getCityName();
            }else{
                $disarea = $userProvince->getProvinceName();
            }
        }
        //月收入
        if($user->getIncome()){
            $userIncome = $em->getRepository('JiliApiBundle:MonthIncome')->find($user->getIncome());
            $usercomes = $userIncome->getIncome();
        }
        if($this->notReadCb() == 0 && $this->notReadMs($id)>0){
            $countMessage = $this->container->getParameter('init_one');
        }

        //确认中的米粒数
        $confirmPoints = $this->get('session.points')
            ->reset()
            ->getConfirm();

        $form_view = $form->createView();

        $this->get('login.listener')->updateInfoSession($user);
        return $this->render('JiliApiBundle:User:info.html.twig', array(
            'form' => $form_view,
            'form_upload' =>$form_view,
            'user' => $user,
            'adtaste' => $adtaste,
            'exchange' => $exchange,
            'code' => $code,
            'codeflag' => $codeflag,
            'existUserinfo' => $existUserinfo,
            'adtasteNum' => $adtasteNum,
            'hobbyList' => $hobbyList,
            'province' => $province,
            'income' => $income,
            'newHobby' => $userProHobby,
            'newYear' => $newYear,
            'newMonth' => $newMonth,
            'userHobby' =>    $newHobby,
            'disarea' => $disarea,
            'usercomes'=> $usercomes,
            'sex' => $sex,
            'tel' => $tel,
            'month_income' => $month_income,
            'hobby' => $hobby,
            'year' => $year,
            'month' => $month,
            'provinceId' => $provinceId,
            'city' => $city,
            'countMessage'=>$countMessage,
            'exFrWen'=> $exFrWen,
            'confirmPoints' => $confirmPoints
        ));
    }

    /**
     * @Route("/upload", name="_user_upload")
     */
    public function uploadAction()
    {
        return $this->redirect($this->generateUrl('_user_info',array('code'=>$code)));
    }

    /**
     * @Route("/update", name="_user_update")
     */
    public function updateAction()
    {
        $code = $this->container->getParameter('init');
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $request = $this->get('request');
        $ck = $request->query->get('ck');
        $tel = $request->query->get('tel');
        if ($request->getMethod() == 'POST') {
            if($ck){
                if(!(ValidateUtil::validateMobile($tel))){
                    $code = $this->container->getParameter('init_two');
                }else{
                    $user->setSex($ck);
                    $user->setTel($tel);
                    // 	    	$em->persist($user);
                    $user->setIsInfoSet($this->container->getParameter('init_one'));
                    $em->flush();
                }
            }else{
                $code = $this->container->getParameter('init_one');
            }
        }
        return $this->redirect($this->generateUrl('_user_info'));
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
            $url_homepage = $this->generateUrl('_user_login');
            $response = new RedirectResponse($url_homepage);
        }


        // set cookie based according the the remember_me.
        $response->headers->setCookie(new Cookie("jili_rememberme", '', time() - 3600 , '/') );

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
     * @Route("/login", name="_user_login",requirements={"_scheme"="https"}, options={"expose"=true})
     */
    public function loginAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();

        $goToUrl =  $session->get('referer');
        if(substr($goToUrl, -10) != 'user/login' && strlen($goToUrl)>0 ){
            $session->set('goToUrl', $goToUrl);
            $session->remove('referer');
        }

        if($session->has('uid')) {
            return $this->redirect($this->generateUrl('_homepage'));
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

            $response = new RedirectResponse($current_url, $code_redirect);

            // set cookie based according the the remember_me.
            if ($request->request->has('remember_me')  &&  $request->request->get('remember_me') === '1') {

                $token = $this->get('login.listener')->buildToken( array( 'email'=> $email, 'pwd'=> $pwd) );
                if( $token) {
                    $response->headers->setCookie(new Cookie("jili_rememberme", $token, time() + 3153600, '/'));
                } else {
                  $this->get('logger')->info($token);
                    // todo: set the error flash
                }

            }
            return $response;
        }
        return $this->render('WenwenFrontendBundle:Home:index.html.twig',array('code'=>$code,'email'=>$email));
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
        $arr['gotoEmial'] = $user->gotomail($info[0]['email']);
        $arr['user'] = $info[0];
        return $this->render('WenwenFrontendBundle:User:active.html.twig',$arr);
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
        if(!$user[0]->getPwd()){
            return new Response($this->container->getParameter('init_two'));
        }

        $nick = $user[0]->getNick();
        $id = $user[0]->getId();
        $passCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findByUserId($id);
        if(empty($passCode)){
            $str = 'jiliforgetpassword';
            $code = md5($id.str_shuffle($str));
            $url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
            if($this->sendMail_reset($url, $email,$nick)){
                $setPasswordCode = new SetPasswordCode();
                $setPasswordCode->setUserId($id);
                $setPasswordCode->setCode($code);
                $setPasswordCode->setCreateTime(new \DateTime());
                $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
                $em->persist($setPasswordCode);
                $em->flush();
                $code = $this->container->getParameter('init_one');
            }
        }else{
            $url = $this->generateUrl('_user_resetPass',array('code'=>$passCode[0]->getCode(),'id'=>$id),true);
            if($this->sendMail_reset($url, $email,$nick)){
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
                        if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
                            $arr['codeflag'] = $this->container->getParameter('init_three');
                            $arr['code'] = $this->container->getParameter('forget_wr_pwd');
                        }else{
                            if($pwd == $newPwd){
#								$this->get('request')->getSession()->set('uid',$id);
#								$this->get('request')->getSession()->set('nick',$user->getNick());
                                $this->get('login.listener')->initSession( $user );

                                $user->setPwd($pwd);
                                $setPasswordCode->setIsAvailable($this->container->getParameter('init'));
                                $em->persist($user);
                                $em->persist($setPasswordCode);
                                $em->flush();
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

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if($user[0]->getIsFromWenwen() == $this->container->getParameter('is_from_wenwen_register')){
        $email = $user[0]->getEmail();
        $url = $this->generateUrl('_user_setPassFromWenwen',array('code'=>$code,'id'=>$user[0]->getId()),true);
        $send_email = $this->get('send_mail')->sendMailForRegisterFromWenwen($email, $url);
        }else{
            $url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
            $send_email = $this->sendMail($url, $email,$nick);
        }

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

        if($user[0]->getIsFromWenwen() == $this->container->getParameter('is_from_wenwen_register')){
        $email = $user[0]->getEmail();
        $url = $this->generateUrl('_user_setPassFromWenwen',array('code'=>$code,'id'=>$user[0]->getId()),true);
        $send_email = $this->get('send_mail')->sendMailForRegisterFromWenwen($email, $url);
        }else{
            $url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$user[0]->getId()),true);
            $send_email = $this->sendMail($url,$email,$user[0]->getNick());
        }
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
	 * @Route("/reg", name="_user_reg",requirements={"_scheme"="https"})
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
              $args = array( '--campaign_id=1',
                '--group_id=81',
                '--mailing_id=9',
                '--email='. $user->getEmail(),
                '--title=先生/女士',
                '--name='. $user->getNick(),
                '--register_key='. $setPasswordCode->getCode() ); //check the verification

              $job = new Job('webpower-mailer:signup-confirm',$args,  true, '91wenwen_signup');
              $em->persist($job);
              $em->flush($job);

              $session=$this->get('session');

              // check the campaign
              if( $session->has('campaign_code') && $session->has('campaign_code_token') ) {

                $campagin_code = $session->get('campaign_code');
                $campagin_code_token  = $session->get('campaign_code_token');
                if(preg_match('/^offer99/',  $campagin_code) ) {

                  $job = new Job('recruit-notification:offer99',
                    array('--user_id='.$user->getId(), '--txid='.$campagin_code_token) ,
                    true, 'offer99-recruit');
                  $em->persist($job);
                  $em->flush($job);

                } elseif (preg_match('/^offerwow/',  $campagin_code )) {
                  $job = new Job('recruit-notification:offerwow',
                    array('--user_id='.$user->getId() ,'--tid='. $campagin_code_token ),
                    true, 'offerwow-recruit');
                  $em->persist($job);
                  $em->flush($job);
                }

              }



              // set sucessful message flash
              $this->get('session')->getFlashBag()->add(
                'notice',
                '恭喜，注册成功！'
              );
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
	 * @Route("/exchange/{type}/{exchangeType}", name="_user_exchange")
	 */
    public function exchangeAction($type=0,$exchangeType)
    {
        $id = $this->get('request')->getSession()->get('uid');
        if(!$id){
           return $this->redirect($this->generateUrl('_user_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        if($exchangeType==1){
            $repository = $em->getRepository('JiliApiBundle:PointsExchange');
            $option = array('daytype' => $type ,'offset'=>'','limit'=>'');
            $exchange = $repository->getUserExchange($id,$option);
            $arr['exchange'] = $exchange;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator
                    ->paginate($exchange,
                    $this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }else if($exchangeType==2){
            $exFrWen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->eFrWenById($id);
            $arr['exFrWen'] = $exFrWen;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator
                    ->paginate($exFrWen,
                    $this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');

        }else{
            return $this->redirect($this->generateUrl('_default_error'));

        }
        $arr['exchangeType'] = $exchangeType;
        $arr['type'] = $type;
        return $this->render('WenwenFrontendBundle:Personal:exchangeHistory.html.twig',$arr);
    }


    /**
	 * @Route("/adtaste/{type}", name="_user_adtaste")
	 */
    public function adtasteAction($type)
    {
        if(!$this->get('session')->has('uid')){
           return $this->redirect($this->generateUrl('_user_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $option = array('status' => $type ,'offset'=>'','limit'=>'');
#		$adtaste = $this->selTaskHistory($id,$option);
        $this->get('session.my_task_list')->remove(array('alive'));
        $adtaste = $this->get('session.my_task_list')->selTaskHistory($option);
        foreach ($adtaste as $key => $value) {
            if($value['orderStatus'] == 1 && $value['type'] ==1){
                unset($adtaste[$key]);
            }
        }
        $arr['adtaste'] = $adtaste;
        $user = $em->getRepository('JiliApiBundle:User')->find($this->get('session')->get('uid'));
        $arr['user'] = $user;
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($adtaste,
                $this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        return $this->render('WenwenFrontendBundle:Personal:taskHistory.html.twig',$arr);
    }

    /**
	 * @Route("/regSuccess", name="_user_regSuccess")
	 */
    public function regSuccessAction()
    {
        $session = $this->get('session');
        $session->set('campaign_code','');
        $session->set('campaign_code_token','');

        $email = $session->get('email');

        return $this->render('WenwenFrontendBundle:User:emailActive.html.twig', array(
           'gotoEmail'=> 'mail.'.substr( $email, strpos($email,'@') +1),
           'email' => $email
             ) );
    }


    /**
	 * @Route("/activate/{token}/{uid}", name="_user_signup_activate", requirements={"uid"="\d+"})
     * @Method({"GET", "POST"})
	 */
    public function signupActivateAction($token, $uid)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $vars = array('token'=> $token, 'uid'=> $uid);

        // check the uid
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        if( ! $user ) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }
        // check the token
        $passwordToken = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneValidateSignUpToken(array('user_id'=> $uid, 'token' => $token )  );
        if( !$passwordToken  ) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }

        $form  = $this->createForm(new SignupActivateType() );
        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                // the validation passed, do something with the $author object
                $this->get('signup_activate.form_handler')->setForm($form)->setParams(array( 'user'=>$user, 'passwordToken'=>  $passwordToken ) )->process( );
                // set sucessful message flash
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    '恭喜，密码设置成功！'
                );
                $this->get('session')->set('email', $user->getEmail() );
                return $this->redirect($this->generateUrl('_user_regSuccess'));
            }
        }

        $vars['form'] = $form->createView();
        $vars['pwdcode'] = $passwordToken;
        $vars['user'] = $user;

        return $this->render('WenwenFrontendBundle:User:emailActive.html.twig',$vars);
    }

    /**
     * To compatiable upwards, forward to _user_signup_activate .
     * @Route("forgetPass/{code}/{id}", name="_user_forgetPass")
     */
    public function forgetPassAction($code, $id)
    {
        return $this->redirect($this->generateUrl('_user_signup_activate', array('token'=>$code, 'uid'=>$id)));
    }

    /**
     * @Route("/setPassFromWenwen/{code}/{id}", name="_user_setPassFromWenwen",requirements={"_scheme"="https"})
     */
    public function setPassFromWenwenAction($code,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        $arr['nick'] = "";

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneByUserId($id);
        $arr['pwdcode'] = $setPasswordCode;

        $return = $this->checkCodeValid($setPasswordCode, $code);
        if(!$return){
            $arr['errorMessage'] = "该链接已经失效，您可能已经激活过。";
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', $arr);
        }

        $request = $this->get('request');
        if ($request->getMethod() == 'GET'){
            $arr['error_message'] = "";
            return $this->render('JiliApiBundle:User:setPassWen.html.twig',$arr);
        }

        $error_message = $this->checkInputForSetPassFromWenwen($request);
        if($error_message){
            $arr['error_message'] = $error_message;
            $arr['nick'] = $request->request->get('nick');
            return $this->render('JiliApiBundle:User:setPassWen.html.twig',$arr);
        }

        //设定密码，自动登录
        $this->get('login.listener')->initSession($user);

        $user->setPwd($request->request->get('pwd'));
        $user->setNick($request->request->get('nick'));
        $setPasswordCode->setIsAvailable($this->container->getParameter('init'));
        $em->persist($user);
        $em->persist($setPasswordCode);
        $em->flush();

        //设置密码之后，注册成功，发邮件2014-01-10
        $soapMailLister = $this->get('soap.mail.listener');
        $soapMailLister->setCampaignId($this->container->getParameter('register_success_campaign_id')); //活动id
        $soapMailLister->setMailingId($this->container->getParameter('register_success_mailing_id')); //邮件id
        $soapMailLister->setGroup(array ('name' => '积粒网','is_test' => 'false')); //group
        $recipient_arr = array (
                array (
                    'name' => 'email',
                    'value' => $user->getEmail()
                )
            );
        $soapMailLister->sendSingleMailing($recipient_arr);

        $this->get('login.listener')->checkNewbie($user);
        $this->get('login.listener')->log($user);

        return $this->render('JiliApiBundle:User:regSuccess.html.twig',$arr);
    }

    private function checkCodeValid($setPasswordCode, $code)
    {
        if($setPasswordCode->getIsAvailable()==0){
            return false;
        }
        $time = $setPasswordCode->getCreateTime();
        if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600*24*14){
            return false;
        }

        if($setPasswordCode->getCode() != $code){
            return false;
        }

        return true;
    }

    private function checkInputForSetPassFromWenwen($request)
    {
        $nick = $request->request->get('nick');
        $pwd = $request->request->get('pwd');
        $que_pwd = $request->request->get('que_pwd');
        $error_message = "";

        if($request->request->get('ck')!='1'){
            $error_message = 'choose agree';
            return $error_message;
        }

        if(!$nick){
            $error_message = $this->container->getParameter('reg_en_nick');
            return $error_message;
        }

        if (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u",$nick) || ((strlen($nick) + mb_strlen($nick,'UTF8')) / 2 > 20)){
            $error_message = $this->container->getParameter('reg_wr_nick');
            return $error_message;
        }

        $em = $this->getDoctrine()->getManager();
        $user_nick = $em->getRepository('JiliApiBundle:User')->findByNick($nick);
        if($user_nick){
            $error_message = $this->container->getParameter('reg_al_nick');
            return $error_message;
        }

        if(!$pwd){
            $error_message = $this->container->getParameter('forget_en_pwd');
            return $error_message;
        }

        if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
            $error_message = $this->container->getParameter('forget_wr_pwd');
            return $error_message;
        }

        if($pwd != $que_pwd){
            $error_message = $this->container->getParameter('forget_unsame_pwd');
            return $error_message;
        }

        return $error_message;
    }

    /**
	 * @Route("/updateIsRead", name="_user_updateIsRead", options={"expose"=true})
	 */
    public function updateIsReadAction()
    {
        $content = '';
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
        $code = array();
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $sendid = $request->query->get('sendid');
        $em = $this->getDoctrine()->getManager();
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
            $arr['pagination'] = $paginator
            ->paginate($sendCb,
                    $this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }
        if($sid == $this->container->getParameter('init_one')){//消息
            $showMs  = $this->selectSendMs($id);
            $arr['showMs'] = $showMs;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator
            ->paginate($showMs,
                    $this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }
        $arr['sid'] = $sid;
        return $this->render('WenwenFrontendBundle:Personal:message.html.twig',$arr);
    }


    /**
	* @Route("/countMs", name="_user_countMs", options={"expose"= true})
    * @Method("POST")
	*/
    public function countMsAction()
    {
        $notRead = $this->container->getParameter('init');
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
//         $id =1;
        $str = 'jiliforgetpassword';
        $code = md5($id.str_shuffle($str));
// 		$request = $this->get('request');
        $email = '278583642@qq.com';
        $nick = '';
        $url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
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
        ->setSubject('积粒网-帐号密码重置')
        ->setFrom(array('account@91jili.com'=>'积粒网'))
        ->setTo($email)
        ->setBody(
                '<html>' .
                ' <head></head>' .
                ' <body>' .
                '亲爱的'.$nick.'<br/>'.
                '<br/>'.
                '  我们收到您因为忘记密码，要求重置积粒网帐号密码的申请，请点击<a href='.$url.' target="_blank" style="color: #0000ee;">这里</a>重置您的密码。<br/><br/>' .
                '  如果您并未提交重置密码的申请，请忽略本邮件，并关注您的账号安全，因为可能有其他人试图登录您的账户。<br/><br/>积粒网运营中心' .
                ' </body>' .
                '</html>',
                'text/html'
        );
        $flag = $this->get('mailer')->send($message);
        if($flag===1){
            return true;
        }else{
            return false;
        }

    }



    public function sendMail($url,$email,$nick)
    {
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
                        '  感谢您注册成为“积粒网”会员！请点击<a href='.$url.' target="_blank" style="color: #0000ee;">这里</a>，立即激活您的帐户！<br/><br/><br/>' .
                        '  注：激活邮件有效期是14天，如果过期后不能激活，请到网站首页重新注册激活。<br/><br/>' .
                        '  ++++++++++++++++++++++++++++++++++<br/>' .
                        '  积粒网，轻松积米粒，快乐换奖励！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
                        ' </body>' .
                        '</html>',
                        'text/html'
        );
        $flag = $this->get('mailer')->send($message);
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


#	private function selTaskHistory($userid, $option) {
#      $em = $this->getDoctrine()->getManager();
#      $task = $em->getRepository('JiliApiBundle:TaskHistory0'. ( $userid % 10) );
#      $po = $task->getUseradtaste($userid, $option);
#
#      foreach ($po as $key => $value) {
#			if($value['type']==1 ) {
#				$adUrl = $task->getUserAdwId($value['orderId']);
#                if( is_array($adUrl) && count($adUrl) > 0) {
#                    $po[$key]['adid'] = $adUrl[0]['adid'];
#                } else {
#                    $po[$key]['adid'] = '';
#                }
#			}else{
#				$po[$key]['adid'] = '';
#			}
#		}
#		return $po;
#    }

}
