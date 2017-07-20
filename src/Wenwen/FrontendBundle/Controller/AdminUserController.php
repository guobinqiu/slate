<?php

namespace Wenwen\FrontendBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Entity\User;

class AdminUserController extends BaseController #implements IpAuthenticatedController
{
    /**
     * @Route("/admin/user", name="admin_user", options={"expose"=true})
     */
    public function memberAction()
    {
        set_time_limit(1800);

        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        $userId = $request->get('user_id');
        $email = $request->get('email');
        $nick = $request->get('nick');

        $arr['user_id'] = $userId;
        $arr['email'] = $email;
        $arr['nick'] = $nick;


        $user = null;
        if($userId){
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
        } elseif($email){
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('email' => $email));
        } elseif($nick){
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('nick' => $nick));
        } else {

        }


        if(!empty($user)){
            $arr['user'] = $user;
            $userProfile = $em->getRepository('WenwenFrontendBundle:UserProfile')->findOneBy(array('user' => $user));
            $this->get('logger')->debug(__METHOD__ . ' userProfile=' . json_encode($userProfile));
            if(!empty($userProfile)){
                $profile = array();
                if($userProfile->getSex() == 1){
                    $profile['gender'] = '男性';
                } elseif($userProfile->getSex() == 2){
                    $profile['gender'] = '女性';
                } else {
                    $profile['gender'] = '未知';
                }
                $birthday = $userProfile->getBirthday();
                if(!empty($birthday)){
                    $profile['birthday'] = $birthday;
                } else {
                    $profile['birthday'] = '未知';
                }
                $province = $userProfile->getProvince();
                if(!empty($province)){
                    $provinceName = $em->getRepository('WenwenFrontendBundle:ProvinceList')->findOneById($province);
                    $this->get('logger')->debug(__METHOD__ . ' provinceName=' . $provinceName->getProvinceName());
                    if(!empty($provinceName)){
                        $profile['provinceName'] = $provinceName->getProvinceName();
                    } else {
                        $profile['provinceName'] = '未知';
                    }
                }
                $city = $userProfile->getCity();
                if(!empty($city)){
                    $cityName = $em->getRepository('WenwenFrontendBundle:CityList')->findOneById($city);
                    if(!empty($cityName)){
                        $this->get('logger')->debug(__METHOD__ . ' cityName=' . $cityName->getCityName());
                        $profile['cityName'] = $cityName->getCityName();
                    } else {
                        $profile['cityName'] = '未知';
                    }
                }

                $arr['userProfile'] = $profile;
            }

            $currentPage = $request->query->get('page', 1);

            $adminUserService = $this->get('app.admin_user_service');
            $taskHistories = $adminUserService->findUserTaskHistories($user->getId(), $currentPage, 50);
            if($taskHistories){
                $arr['taskHistories'] = $taskHistories;
            }

            // 找到该用户，去找用户的历史记录，问卷参与记录
            $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
            $surveyPartnerParticipationHistorys = $adminSurveyPartnerService->getSurveyPartnerParticipationDetailByUser($user);
            if($surveyPartnerParticipationHistorys){
                $arr['surveyPartnerParticipationHistorys'] = $surveyPartnerParticipationHistorys;
            }

            $adminSurveySopService = $this->get('app.admin_survey_sop_service');
            $surveySopParticipationHistories = $adminSurveySopService->getParticipationHistoriesByUserId($user->getId());
            if($surveySopParticipationHistories){
                $arr['surveySopParticipationHistories'] = $surveySopParticipationHistories;
            }

        }

        return $this->render('WenwenFrontendBundle:admin:user.html.twig',$arr);
    }

    /**
     * @Route("/admin/memberEdit", name="_admin_member_edit", options={"expose"=true})
     */
    public function memberEditAction()
    {
        set_time_limit(1800);

        $request = $this->get('request');
        $user_id = $request->get('user_id');
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('WenwenFrontendBundle:User')->findOneById($user_id);
        $arr['member'] = $member;
        $this->get('request')->getSession()->set( 'member_id', $user_id);

        if ($request->getMethod() == 'POST') {

            $nick = $request->get('nick');
            $tel = $request->get('tel');
            $delete_flag = $request->get('delete_flag');
            $datetime = new \DateTime();

            $errorMessage = $this->memberCheck($member->getEmail(),$nick, $tel, $delete_flag);
            if(!$errorMessage){
                $member->setNick($nick);//验证是否存在 ，是否排除已删除的用户
                $member->setTel($tel);//用户自己也可以修改
                $member->setDeleteFlag($delete_flag);
                // Todo need constant for the value of delete_flag
                if($delete_flag == 1){
                    $member->setDeleteDate($datetime);
                } else {
                    $member->setDeleteDate(NULL);
                }
                $em->persist($member);
                $em->flush();
                return $this->redirect($this->generateUrl('admin_user'));
            }else{
                $arr['nick'] = $nick;
                $arr['tel'] = $tel;
                $arr['delete_flag'] = $delete_flag;
                $arr['errorMessage'] = $errorMessage;
                return $this->render('JiliApiBundle:Admin:memberEdit.html.twig',$arr);
            }

        }else{
            $arr['nick'] = $member->getNick();
            $arr['tel'] = $member->getTel();
            $arr['delete_flag'] = $member->getDeleteFlag();
            $arr['errorMessage'] = array();
            return $this->render('JiliApiBundle:Admin:memberEdit.html.twig',$arr);
        }
    }

    /**
     * @Route("/admin/user/send_confirmation_email", name="admin_user_send_confirmation_email", methods={"GET"}, options={"expose"=true})
     */
    public function sendConfirmationEmailAction(Request $request)
    {
        $email = $request->query->get('email');
        if (!$email) {
            return new JsonResponse(array(
                'error' => true,
                'message' => '缺少email参数',
            ), 400);
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('email' => $email));
        if (empty($user)) {
            return new JsonResponse(array(
                'error' => true,
                'message' => 'email用户不存在',
            ), 404);
        }

        $token = md5(uniqid(rand(), true));
        $authService = $this->get('app.auth_service');
        $rtn = $authService->sendConfirmationEmail($user->getId(), $user->getEmail(), $token);

        return new JsonResponse(array(
            'error' => false,
            'message' => '邮件已发送',
        ), 200);
    }
}
