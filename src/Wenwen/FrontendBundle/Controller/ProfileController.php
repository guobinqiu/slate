<?php

namespace Wenwen\FrontendBundle\Controller;

use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserDeleted;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\AppBundle\Entity\UserWithdraw;
use Wenwen\FrontendBundle\Form\UserIconType;
use Wenwen\FrontendBundle\Form\UserType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/profile")
 */
class ProfileController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/index", name="_profile_index", methods={"GET"})
     */
    public function indexAction()
    {
        $cpForm = $this->createChangePasswordForm();
        $wdForm = $this->createWithdrawForm();

        return $this->render('WenwenFrontendBundle:Profile:account.html.twig', array(
            'cpForm' => $cpForm->createView(),
            'wdForm' => $wdForm->createView(),
        ));
    }

    /**
     * @Route("/changePwd", name="_profile_changepwd", methods={"POST"})
     */
    public function changePwdAction(Request $request)
    {
        $wdForm = $this->createWithdrawForm();
        $cpForm = $this->createChangePasswordForm();
        $cpForm->bind($request);

        if ($cpForm->isValid()) {
            $formData = $cpForm->getData();

            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('WenwenFrontendBundle:User')->find($session->get('uid'));

            if ($user == null || !$user->isPwdCorrect($formData['password'])) {
                $cpForm->addError(new FormError('当前密码不正确'));
                return $this->render('WenwenFrontendBundle:Profile:account.html.twig', array(
                    'cpForm' => $cpForm->createView(),
                    'wdForm' => $wdForm->createView(),
                ));
            }

            $user->setPwd($formData['new_password']);
            $em->flush();

            $session->getFlashBag()->add('success', '密码修改成功');
            return $this->redirect($this->generateUrl('_profile_index'));
        }

        return $this->render('WenwenFrontendBundle:Profile:account.html.twig', array(
            'cpForm' => $cpForm->createView(),
            'wdForm' => $wdForm->createView(),
        ));
    }

    /**
     * @Route("/withdraw", name="_profile_withdraw", methods={"POST"})
     */
    public function withdrawAction(Request $request)
    {
        $cpForm = $this->createChangePasswordForm();
        $wdForm = $this->createWithdrawForm();
        $wdForm->bind($request);

        if ($wdForm->isValid()) {
            $formData = $wdForm->getData();
            $email = $formData['email'];
            $password = $formData['password'];
            $reason = $formData['reason'];

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('email' => $email));

            if ($user == null || !$user->isPwdCorrect($password)) {
                $wdForm->addError(new FormError('邮箱或密码不正确'));
                return $this->render('WenwenFrontendBundle:Profile:account.html.twig', array(
                    'cpForm' => $cpForm->createView(),
                    'wdForm' => $wdForm->createView(),
                ));
            }

            $userDeleted = $this->copyUserToUserDeleted($user);
            $em->persist($userDeleted);
            $em->remove($user);

            $userWithdraw = new UserWithdraw();
            $userWithdraw->setUserId($user->getId());
            $userWithdraw->setReason($reason);
            $em->persist($userWithdraw);

            $em->flush();
            $request->getSession()->clear();
            $this->clearCookies();

            return $this->render('WenwenFrontendBundle:Profile:withdraw_finish.html.twig');
        }

        return $this->render('WenwenFrontendBundle:Profile:account.html.twig', array(
            'cpForm' => $cpForm->createView(),
            'wdForm' => $wdForm->createView(),
        ));
    }

    /**
     * @Route("/edit", name="_profile_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($session->get('uid'));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        //建立user和userProfile之间的双向关联
        if ($user->getUserProfile() == null) {
            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $user->setUserProfile($userProfile);
        }

        //一个页面有多个表单
        $uploadForm = $this->createForm(new UserIconType());
        $userType = new UserType();
        $userForm = $this->createForm($userType, $user);

        //由于也支持GET请求，所以先要判断一下是不是POST的
        if ($request->getMethod() == 'POST') {
            //仅对editForm进行处理
            if ($request->request->has($userType->getName())) {
                $userForm->bind($request);
                if ($userForm->isValid()) {
                    $em->flush();//保存user的同时级联保存userProfile
                    $session->getFlashBag()->add('success', '个人资料修改成功');
                    return $this->redirect($this->generateUrl('_profile_edit'));
                }
            }
        }

        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', array(
            'uploadForm' => $uploadForm->createView(),
            'userForm' => $userForm->createView(),
            'user' => $user,
            'userProfile' => $user->getUserProfile(),
            'provinces' => $provinces,
            'cities' => $cities,
        ));
    }

    /**
     * @Route("/upload", name="_profile_upload", methods={"POST"})
     * @link http://symfony.com/doc/current/controller/upload_file.html
     */
    public function uploadAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);

        //一个页面有多个表单
        $userIconType = new UserIconType();
        $uploadForm = $this->createForm($userIconType, $user);
        $userForm = $this->createForm(new UserType(), $user);

        //仅对uploadForm进行处理
        if ($request->request->has($userIconType->getName())) {
            $uploadForm->bind($request);
            if ($uploadForm->isValid()) {
                $file = $user->getIcon();
                if ($file != null) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $uploadDir = $this->container->getParameter('avatar_directory');
                    $file->move($uploadDir, $fileName);

                    $webRoot = $this->get('kernel')->getRootDir() . '/../web';
                    $newIconUrl = $webRoot . '/' . $uploadDir . '/' . $fileName;

                    if ($user->getIconPath() != null) {
                        $oldIconUrl = $webRoot . '/' . $user->getIconPath();
                    }

                    //先按指定宽度等比缩放
                    $this->zoomImage($newIconUrl, 512);

                    //再按坐标裁切
                    $x = $uploadForm->get('x')->getData();
                    $y = $uploadForm->get('y')->getData();
                    $w = $uploadForm->get('w')->getData();
                    $h = $uploadForm->get('h')->getData();
                    $this->cropImage($newIconUrl, $x, $y, $w, $h);

                    //把iconPath关联到新上传的文件
                    $newIconPath = $uploadDir . '/' . $fileName;
                    $user->setIconPath($newIconPath);
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    //删除上一次上传的文件
                    if (isset($oldIconUrl) && file_exists($oldIconUrl)) {
                        unlink($oldIconUrl);
                    }

                    return $this->redirect($this->generateUrl('_profile_edit'));
                }
            }
        }

        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', array(
            'uploadForm' => $uploadForm->createView(),
            'userForm' => $userForm->createView(),
            'user' => $user,
        ));
    }

    private function zoomImage($filename, $w) {

        $src_image = imagecreatefromstring(file_get_contents($filename));

        $src_x = 0;
        $src_y = 0;
        $src_w = imagesx($src_image);
        $src_h = imagesy($src_image);

        $dst_x = 0;
        $dst_y = 0;
        $dst_w = $w;
        $dst_h = $w * $src_h / $src_w;
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);

        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($dst_image, $filename);

        imagedestroy($src_image);
        imagedestroy($dst_image);
    }

    private function cropImage($filename, $x, $y, $w, $h) {
        $src_image = imagecreatefromstring(file_get_contents($filename));
        $src_x = $x;
        $src_y = $y;
        $src_w = $w;
        $src_h = $h;

        $dst_x = 0;
        $dst_y = 0;
        $dst_w = $src_w;
        $dst_h = $src_h;
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);

        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($dst_image, $filename);

        imagedestroy($src_image);
        imagedestroy($dst_image);
    }

    private function createChangePasswordForm() {
        $form = $this->createFormBuilder()
            ->add('password', 'password', array(
                'label' => '当前密码',
                'constraints' => new Assert\NotBlank(array('message' => '请输入当前密码')),
            ))
            ->add('new_password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => '两次输入的密码不一致',
                'first_options' => array('label' => '新密码'),
                'second_options' => array('label' => '确认密码'),
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '请输入新密码')),
                    new Assert\Length(array('min' => 5, 'max' => 100)),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                ),
            ))
            ->getForm();

        return $form;
    }

    private function createWithdrawForm() {
        $form = $this->createFormBuilder()
            ->add('email', 'email', array(
                'label' => '请输入您的邮箱',
                'constraints' => new Assert\NotBlank(array('message' => '请输入您的邮箱地址')),
            ))
            ->add('password', 'password', array(
                'label' => '请输入您的密码',
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '请输入您的密码')),
                    new Assert\Length(array('min' => 5, 'max' => 100)),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                )
            ))
            ->add('reason', 'choice', array(
                'label' => '注销用户的理由',
                'expanded' => true,
                'multiple' => true,
                'choices' => array(
                    '问卷调查活动的数量太少了' => '问卷调查活动的数量太少',
                    '每个问卷的奖励太少' => '每个问卷的奖励太少',
                    '问卷所要的时间太长' => '问卷所要的时间太长',
                    '相对于问题，积分报酬太少' => '相对于问题来说，积分报酬太少',
                    '问卷的内容太难' => '问卷的内容太难',
                    '工作生活太忙，没时间参加' => '工作生活太忙，没时间参加',
                    '失去兴趣了' => '失去兴趣了',
                )
            ))
            ->getForm();

        return $form;
    }

    private function copyUserToUserDeleted(User $user)
    {
        $userDeleted = new UserDeleted();
        $userReflection = new \ReflectionObject($user);
        $userDeletedReflection = new \ReflectionObject($userDeleted);

        foreach ($userReflection->getProperties() as $userProperty) {
            if ($userDeletedReflection->hasProperty($userProperty->getName())) {
                $userProperty->setAccessible(true);
                $userDeletedProperty = $userDeletedReflection->getProperty($userProperty->getName());
                $userDeletedProperty->setAccessible(true);
                $userDeletedProperty->setValue($userDeleted, $userProperty->getValue($user));
            }
        }
        return $userDeleted;
    }
}
