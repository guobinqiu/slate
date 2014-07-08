<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Repository\AdPositionRepository;
use Jili\ApiBundle\Entity\RateAdResult;
use Jili\ApiBundle\Entity\LimitAdResult;
use Jili\ApiBundle\Form\EditBannerType;
use Jili\ApiBundle\Form\AddAdverType;
use Jili\ApiBundle\Form\AddBusinessActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\AdPosition;
use Jili\ApiBundle\Entity\AdBanner;
use Jili\ApiBundle\Entity\CallBoard;
use Jili\ApiBundle\Entity\CbCategory;
use Jili\ApiBundle\Entity\LimitAd;
use Jili\ApiBundle\Entity\RateAd;
use Jili\ApiBundle\Entity\TaskOrder;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\SendCallboard;
use Jili\ApiBundle\Entity\ActivityCategory;
use Jili\ApiBundle\Entity\MarketActivity;
use Jili\ApiBundle\Entity\ExchangeAmazonResult;
use Jili\ApiBundle\Entity\ExchangeFromWenwen;
use Jili\ApiBundle\Entity\CardRecordedMatch;
use Jili\ApiBundle\Entity\CardRecordedRemain;
use Jili\ApiBundle\Entity\CardRecordedReward;
use Jili\ApiBundle\Entity\IsReadFile;
use Jili\ApiBundle\Entity\CheckinAdverList;
use Jili\ApiBundle\Entity\CheckinUserList;
use Jili\ApiBundle\Entity\CheckinClickList;
use Jili\ApiBundle\Entity\CheckinPointTimes;
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
use Jili\ApiBundle\Entity\SendMessage00;
use Jili\ApiBundle\Entity\SendMessage01;
use Jili\ApiBundle\Entity\SendMessage02;
use Jili\ApiBundle\Entity\SendMessage03;
use Jili\ApiBundle\Entity\SendMessage04;
use Jili\ApiBundle\Entity\SendMessage05;
use Jili\ApiBundle\Entity\SendMessage06;
use Jili\ApiBundle\Entity\SendMessage07;
use Jili\ApiBundle\Entity\SendMessage08;
use Jili\ApiBundle\Entity\SendMessage09;

/**
 * @Route( requirements={"_scheme" = "https"})
 */
class AdminController extends Controller
{
    private function getAdminIp(){
        if($_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_ele_ip') || 
            $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_un_ip') ||
            $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||
           substr( $_SERVER['REMOTE_ADDR'],0,10)  == '192.168.1.' ||
            $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_vpn_ip'))
            return false;
        else
            return true;
          
    }
    /**
     * @Route("/login", name="_admin_login")
     */
    public function loginAction()
    {
        if($this->getAdminIp())
              return $this->redirect($this->generateUrl('_default_error'));
        $code = $this->container->getParameter('init');
        $request = $this->get('request');
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        if ($request->getMethod() == 'POST') {
            if($username=='admin' && $password=='admin'){
                // $session = new Session();
                // $session->start();
                // $session->set('admin_name', $username);
                $code = $this->container->getParameter('init');
                return $this->redirect($this->generateUrl('_admin_index' ));
            }else{
                $code = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:login.html.twig',array('code'=>$code));

      
    }

    /**
     * @Route("/gameAd", name="_admin_gameAd")
     */
    public function GameAdAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:gameAd.html.twig');
    }
    
    /**
     * @Route("/game", name="_admin_game")
     */
    public function GameAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:game.html.twig');
    }

    /**
     * @Route("/adwAd", name="_admin_adwAd")
     */
    public function AdwAdAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:adwAd.html.twig');
    }
    
    //没有通过认证
    private function noCertified($userId,$adid,$ocd){
        $em = $this->getDoctrine()->getManager();
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
        if($advertiserment->getIncentiveType()==1)
            $ocd = '';
        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid,$ocd);
        if(empty($adworder)){
            return false;
        }else{
            $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);
            $adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')));
            $adworder->setOrderStatus($this->container->getParameter('init_four'));
            $em->persist($adworder);
            $em->flush();
            $parms = array(
              'userid' => $userId,
              'orderId' => $adworder->getId(),
              'taskType' => $this->container->getParameter('init_one'),
              'reward_percent' => '',
              'point' => $adworder->getIncentive(),
              'date' => date('Y-m-d H:i:s'),
              'status' => $this->container->getParameter('init_four')
            );
            $return = $this->updateTaskHistory($parms);
            return $return;

        }
    }
    //已经认证
    private function hasCertified($userId,$adid,$ocd,$comm){
        $em = $this->getDoctrine()->getManager();
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
        if($advertiserment->getIncentiveType()==1){
            $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid);
        }
        if($advertiserment->getIncentiveType()==2){
            $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid,$ocd);
        }
        if(empty($adworder)){
            return false;
        }else{
            $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);
            $taskPercent = $this->selectTaskPercent($userId,$adworder->getId());
            if($adworder->getIncentiveType()==2){
                $adworder->setIncentive(intval($comm*$taskPercent['rewardPercent']));
            }
            $adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')));
            $adworder->setOrderStatus($this->container->getParameter('init_three'));
            $em->persist($adworder);
            $em->flush();
            if($adworder->getIncentiveType()==1){
                $parms = array(
                'userid' => $userId,
                'orderId' => $adworder->getId(),
                'taskType' => $this->container->getParameter('init_one'),
                'point' => $adworder->getIncentive(),
                'date' => date('Y-m-d H:i:s'),
                'status' => $this->container->getParameter('init_three')
              );
            }
            if($adworder->getIncentiveType()==2){
                $parms = array(
                'userid' => $userId,
                'orderId' => $adworder->getId(),
                'taskType' => $this->container->getParameter('init_one'),
                'point' => intval($comm*$taskPercent['rewardPercent']),
                'date' => date('Y-m-d H:i:s'),
                'status' => $this->container->getParameter('init_three')
              );
            }

            $return = $this->updateTaskHistory($parms);
            if(!$return){
                return false;
            }

            if($adworder->getIncentiveType()==1){
                $limitAd = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adid);
                $limitrs = new LimitAdResult();
                $limitrs->setAccessHistoryId($adworder->getId());
                $limitrs->setUserId($userId);
                $limitrs->setLimitAdId($limitAd[0]->getId());
                $limitrs->setResultIncentive($adworder->getIncentive());
                $em->persist($limitrs);
                $em->flush();
                $this->getPointHistory($userId,$adworder->getIncentive(),$adworder->getIncentiveType());
                $user = $em->getRepository('JiliApiBundle:User')->find($userId);
                $user->setPoints(intval($user->getPoints() + $adworder->getIncentive())); // point caculated when setInsentive()
                $em->persist($user);
                $em->flush();
        
            }else{
                $rateAd = $em->getRepository('JiliApiBundle:RateAd')->findByAdId($adid);
                //todo: deprecated
                $raters = new RateAdResult();
                $raters->setAccessHistoryId($adworder->getId());
                $raters->setUserId($userId);
                $raters->setRateAdId($rateAd[0]->getId());
                $raters->setResultPrice($adworder->getComm());
                $raters->setResultIncentive($adworder->getIncentive());
                $em->persist($raters);
                $em->flush();

                $this->getPointHistory($userId,$adworder->getIncentive(),$adworder->getIncentiveType());
                $user = $em->getRepository('JiliApiBundle:User')->find($userId);
                $user->setPoints(intval($user->getPoints() + $raters->getResultIncentive()));
                $em->persist($user);
                $em->flush();
                
            }
            return true;
        }
        
    }

    public function selectTaskPercent($userid,$orderId){
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory00'); 
                  break;
            case 1:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory01');  
                  break;
            case 2:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory02');  
                  break;
            case 3:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory03'); 
                  break;
            case 4:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory04'); 
                  break;
            case 5:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory05'); 
                  break;
            case 6:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory06'); 
                  break;
            case 7:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory07'); 
                  break;
            case 8:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory08'); 
                  break;
            case 9:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory09'); 
                  break;
      }
      $task_order = $task->getTaskPercent($orderId);
      return $task_order[0];
    }


    private function updateTaskHistory($parms=array()){
      extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory00'); 
                  break;
            case 1:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory01');  
                  break;
            case 2:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory02');  
                  break;
            case 3:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory03'); 
                  break;
            case 4:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory04'); 
                  break;
            case 5:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory05'); 
                  break;
            case 6:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory06'); 
                  break;
            case 7:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory07'); 
                  break;
            case 8:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory08'); 
                  break;
            case 9:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory09'); 
                  break;
      }
      $task_order = $task->getFindOrderId($orderId,$taskType);
      if(empty($task_order)){
          return false;
      }

      $po = $task->findById($task_order[0]['id']);
      if(empty($po)){
          return false;
      }

      $po[0]->setDate(date_create($date));
      $po[0]->setPoint($point);
      $po[0]->setStatus($status);
      $em->persist($po[0]);
      $em->flush();
      return true;
    }
    
    private function getPointHistory($userid,$point,$type){
        if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
        }else{
            $uid = $userid;
        }
        switch($uid){
            case 0:
                $po = new PointHistory00();
                break;
            case 1:
                $po = new PointHistory01();
                break;
            case 2:
                $po = new PointHistory02();
                break;
            case 3:
                $po = new PointHistory03();
                break;
            case 4:
                $po = new PointHistory04();
                break;
            case 5:
                $po = new PointHistory05();
                break;
            case 6:
                $po = new PointHistory06();
                break;
            case 7:
                $po = new PointHistory07();
                break;
            case 8:
                $po = new PointHistory08();
                break;
            case 9:
                $po = new PointHistory09();
                break;
        }
        $em = $this->getDoctrine()->getManager();
        $po->setUserId($userid);
        $po->setPointChangeNum($point);
        $po->setReason($type);
        $em->persist($po);
        $em->flush();
    }
    
    private function getStatus($uid,$aid,$ocd){
        $em = $this->getDoctrine()->getManager();
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
        if($advertiserment->getIncentiveType()==1){
            $adwStatus = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($uid,$aid);
        }
        if($advertiserment->getIncentiveType()==2){
            $adwStatus = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($uid,$aid,$ocd);
        }
        if(empty($adwStatus))
            return true;
        else
            return false;
    }


     /**
     * @Route("/importCpsAdver", name="_admin_importCpsAdver")
     */
    public function importCpsAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = array();
        $request = $this->get('request');
        $success = '';
        $userId = '';
        $adid = '';
        if ($request->getMethod('post') == 'POST') {
            $success = $this->container->getParameter('init_one');
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                $handle = fopen($file,'r'); 
                while ($data = fgetcsv($handle)){ 
                   $goods_list[] = $data;
                   unset($goods_list[0]);
                }
                foreach ($goods_list as $k=>$v){
                    $status = iconv('gb2312','UTF-8//IGNORE',$v[6]);
                    $name = iconv('gb2312','UTF-8//IGNORE',$v[0]);
                    $ocd = explode("'",$v[3]);
                    $adid = explode("'",$v[7]);
                    $userId = explode("'",$v[8]);
                    if($this->getStatus($userId[1],$adid[1],$ocd[1])){
                        if($status == $this->container->getParameter('nothrough')){
                            if(!$this->noCertified($userId[1],$adid[1],$ocd[1])){
                                $code[] = '[ '.$name.', '.$userId[1].', '.$adid[1].', '.$ocd[1].' ] 插入数据失败';
                            }
                        }
                        if($status == $this->container->getParameter('certified')){
                            if(!$this->hasCertified($userId[1],$adid[1],$ocd[1],$v[5])){
                                $code[] = '[ '.$name.', '.$userId[1].', '.$adid[1].', '.$ocd[1].' ] 插入数据失败';
                            }
                        }
                    }
                    
                }
                fclose($handle);
            }
        }
        $arr['success'] = $success;
        $arr['code'] = $code;
        return $this->render('JiliApiBundle:Admin:importCpsAdver.html.twig',$arr);

    }
    
    
    /**
     * @Route("/importAdver", name="_admin_importAdver")
     */
    public function importAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = array();
        $request = $this->get('request');
        $success = '';
        $userId = '';
        $adid = '';
        if ($request->getMethod('post') == 'POST') {
            $success = $this->container->getParameter('init_one');
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                $handle = fopen($file,'r'); 
                while ($data = fgetcsv($handle)){ 
                   $goods_list[] = $data;
                   unset($goods_list[0]);
                }
                foreach ($goods_list as $k=>$v){
                    $status = iconv('gb2312','UTF-8//IGNORE',$v[5]);
                    $name = iconv('gb2312','UTF-8//IGNORE',$v[0]);
                    $adid = explode("'",$v[7]);
                    $userId = explode("'",$v[8]);
                    $ocd = '';
                    if($this->getStatus($userId[1],$adid[1],$ocd)){
                        if($status == $this->container->getParameter('nothrough')){
                            if(!$this->noCertified($userId[1],$adid[1],$ocd)){
                                $code[] = '[ '.$name.', '.$userId[1].', '.$adid[1].' ] 插入数据失败';
                            }
                        }
                        if($status == $this->container->getParameter('certified')){
                            if(!$this->hasCertified($userId[1],$adid[1],$ocd,$v[4])){
                                $code[] =  '[ '.$name.', '.$userId[1].', '.$adid[1].' ] 插入数据失败';
                            }
                        }
                    }
                    
                }
                fclose($handle);
            }
        }
        $arr['success'] = $success;
        $arr['code'] = $code;
        return $this->render('JiliApiBundle:Admin:importAdver.html.twig',$arr);
    }
    
    /**
     * @Route("/delBanner/{id}", name="_admin_delBanner")
     */
    public function delBannerAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $banner = $em->getRepository('JiliApiBundle:AdBanner')->find($id);
        $em->remove($banner);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoBanner'));
    }
    

    /**
     * @Route("/addBanner", name="_admin_addBanner")
     */
    public function addBannerAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code ='';
        $codeflag = '';
        $banner = new adBanner();
        $form  = $this->createForm(new EditBannerType(),$banner);
        $request = $this->get('request');
        $bannerUrl = $request->request->get('url');
        $position = $request->request->get('position');
        $em = $this->getDoctrine()->getManager();
        $bannerNum = $em->getRepository('JiliApiBundle:AdBanner')->findAll();
        if ($request->getMethod() == 'POST') {
            if($bannerUrl && $position){
                $isPostion = $em->getRepository('JiliApiBundle:AdBanner')->findByPosition($position);
                if($isPostion){
                    $codeflag = $this->container->getParameter('init_two');
                }else{
                    $form->bind($request);
                    $path =  $this->container->getParameter('upload_banner_dir');
                    $banner->setAdUrl($bannerUrl);
                    $banner->setPosition($position);
                    $banner->setCreateTime(date_create(date('Y-m-d H:i:s')));
                    $em->persist($banner);
                    $code = $banner->upload($path);
                    if(!$code){
                        $em->flush();
                        return $this->redirect($this->generateUrl('_admin_infoBanner'));
                    }
                }
                
            }else{
                $codeflag = $this->container->getParameter('init_one');

            }
            
        }
        return $this->render('JiliApiBundle:Admin:addBanner.html.twig',
                array('url'=>$bannerUrl,'position'=>$position,'form' => $form->createView(),'code'=>$code,'codeflag'=>$codeflag));
    }
    
    /**
     * @Route("/infoBanner", name="_admin_infoBanner")
     */
    public function infoBannerAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $adbanner = $em->getRepository('JiliApiBundle:AdBanner')->getInfoAdminBanner();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($adbanner,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoBanner.html.twig',$arr);
    
    }
    
    /**
     * @Route("/editBanner/{id}", name="_admin_editBanner")
     */
    public function editBannerAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code ='';
        $codeflag = '';
        $request = $this->get('request');
        $url = $request->request->get('url');
        $position = $request->request->get('position');
        $em = $this->getDoctrine()->getManager();
        $adbanner = $em->getRepository('JiliApiBundle:AdBanner')->find($id);
        $form  = $this->createForm(new EditBannerType(),$adbanner);
        if ($request->getMethod() == 'POST') {
             if($url && $position){
                $form->bind($request);
                $path =  $this->container->getParameter('upload_banner_dir');
                $isPostion = $em->getRepository('JiliApiBundle:AdBanner')->getBannerPosition($position,$id);
                if($isPostion){
                    $findBanner = $em->getRepository('JiliApiBundle:AdBanner')->find($isPostion[0]['id']);
                    $findBanner->setPosition($adbanner->getPosition());
                    $findBanner->setCreateTime(date_create(date('Y-m-d H:i:s')));
                    $em->persist($adbanner);
                    $em->flush();
                }
                $adbanner->setAdUrl($url);
                $adbanner->setPosition($position);
                $adbanner->setCreateTime(date_create(date('Y-m-d H:i:s')));
                $em->persist($adbanner);
                $code = $adbanner->editupload($path);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('_admin_infoBanner'));
                }
             }else{
                $codeflag = $this->container->getParameter('init_one');
             }
            
        }
        return $this->render('JiliApiBundle:Admin:editBanner.html.twig',
                array('banner'=>$adbanner,'form' => $form->createView(),'code'=>$code,'codeflag'=>$codeflag));
    
    }
    
    
    /**
     * @Route("/addPostion", name="_admin_addPostion")
     */
    public function addPostionAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $advermentTitle = '';
        $adposition = '';
        $code = '';
        $ad_title = '';
        $postion = new AdPosition();
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $position = $request->request->get('position');
        $title = $request->request->get('title');
        $adid = $request->query->get('id');
        $number = $request->request->get('number');
        $em = $this->getDoctrine()->getManager();
        if($adid){
            $ad_adv = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
            $advermentTitle = $ad_adv->getTitle();
        }
        if ($request->getMethod() == 'POST') {
            if($request->request->get('search')){
                $adposition = $em->getRepository('JiliApiBundle:Advertiserment')->getSearchAd($title);
                if(!empty($adposition)){
                    $code = $this->container->getParameter('init_one');
                }else{
                    $codeflag = $this->container->getParameter('init_five');
                }
            }
            if($request->request->get('selected')){
                $ck = $request->request->get('ck');
                if($ck){
                    $ad_position = $em->getRepository('JiliApiBundle:Advertiserment')->find($ck);
                    $ad_title = $ad_position->getTitle();
                }else{
                    $adposition = $em->getRepository('JiliApiBundle:Advertiserment')->getSearchAd($title);
                    $codeflag = $this->container->getParameter('init_four');
                }
            }
            if($request->request->get('add')){
                if($title && $position && $number){
                    if($adid){
                        $exist = $em->getRepository('JiliApiBundle:AdPosition')->getAdPosition($position);
                        foreach($exist as $k=>$v){
                            $existNum[] = $v['position'];
                        }
                        if(in_array($number,$existNum)){
                            $codeflag = $this->container->getParameter('init_three');
                        }else{
                            $postion->setType($position);
                            $postion->setPosition($number);
                            $postion->setAdId($adid);
                            $em->persist($postion);
                            $em->flush();
                            return $this->redirect($this->generateUrl('_admin_infoPostion'));
                        }
                        
                    }else{
                        $adverment = $em->getRepository('JiliApiBundle:Advertiserment')->findByTitle($title);
                        if(empty($adverment)){
                            $codeflag = $this->container->getParameter('init_two');
                        }else{
                            $exist = $em->getRepository('JiliApiBundle:AdPosition')->getAdPosition($position);
                            foreach($exist as $k=>$v){
                                $existNum[] = $v['position'];
                            }
                            if(in_array($number,$existNum)){
                                $codeflag = $this->container->getParameter('init_three');
                            }else{
                                $postion->setType($position);
                                $postion->setPosition($number);
                                $postion->setAdId($adverment[0]->getId());
                                $em->persist($postion);
                                $em->flush();
                                return $this->redirect($this->generateUrl('_admin_infoPostion'));
                            }
                        }
                    }
                    
                }else{
                    $codeflag = $this->container->getParameter('init_one');
                }
            }
        }
        return $this->render('JiliApiBundle:Admin:addPostion.html.twig',
                array('codeflag'=>$codeflag,'adid'=>$adid,'code'=>$code,
                      'title'=>$title,'ad_title'=>$ad_title,'position'=>$position,'number'=>$number,
                        'adposition'=>$adposition,'advermentTitle'=>$advermentTitle));
    }
    
    /**
     * @Route("/searchPosition", name="_admin_searchPosition")
     */
    public function searchPositionAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $ad_code = '';
        $request = $this->get('request');
        $title = $request->request->get('title');
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $adposition = $em->getRepository('JiliApiBundle:Advertiserment')->getSearchAd($title);
            if(empty($adposition)){
                $ad_code = $this->container->getParameter('init_one');
            }else{
                $ad_code = $this->container->getParameter('init_two');
            }
        }
        return $this->render('JiliApiBundle:Admin:searchPosition.html.twig',array('adposition'=>$adposition,'ad_code'=>$ad_code));
        
    }
    
    
    /**
     * @Route("/delAdPosition/{id}", name="_admin_delAdPosition")
     */
    public function delAdPositionAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $adposition = $em->getRepository('JiliApiBundle:AdPosition')->find($id);
        $adposition->setPosition($this->container->getParameter('init'));
        $em->persist($adposition);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoPostion'));
    }
    
    /**
     * @Route("/editPostion/{id}", name="_admin_editPostion")
     */
    public function editPostionAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:AdPosition')->getInfoPosition($id);
        $position = $request->request->get('position');
        $number = $request->request->get('number');
        if ($request->getMethod() == 'POST') {
            if($position && $number){
                $exist = $em->getRepository('JiliApiBundle:AdPosition')->getAdPosition($position);
                foreach($exist as $k=>$v){
                    $existNum[] = $v['position'];
                }
                if(in_array($number,$existNum)){
                    $codeflag = $this->container->getParameter('init_two');
                }else{
                    $adposition = $em->getRepository('JiliApiBundle:AdPosition')->find($id);
                    $adposition->setType($position);
                    $adposition->setPosition($number);
                    $em->persist($adposition);
                    $em->flush();
                    return $this->redirect($this->generateUrl('_admin_infoPostion'));
                }
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editPostion.html.twig',array('adver'=>$adver[0],'codeflag'=>$codeflag));
    }
    
    /**
     * @Route("/infoPostion", name="_admin_infoPostion")
     */
    public function infoPostionAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAdvertiserment();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($adver,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoPostion.html.twig',$arr);
        
        
    }
    
    
    /**
     * @Route("/addAdver", name="_admin_addAdver")
     */
    public function addAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $codeflag = $this->container->getParameter('init');
        $adver = new Advertiserment();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $title = $request->request->get('title');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');
        $info = $request->request->get('info');
        $comment = $request->request->get('comment');
        $url = $request->request->get('url');
        $category = $request->request->get('category');
        $score = $request->request->get('score');
        $action_id = $request->request->get('actionId');
        $intReward = $request->request->get('intReward');
        $rewardRate = $request->request->get('rewardRate');
        $rule = $request->request->get('rule');
        $type = $request->request->get('type');
        $form  = $this->createForm(new AddAdverType(),$adver);
        $actCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
        if ($request->getMethod() == 'POST') {
            if($title && $start_time && $end_time && $info && $comment && $rule && $url && $category){ 
                if(($category == 1 && $score) || ($category == 2 && $intReward && $rewardRate) ||($category ==  $this->container->getParameter('emar_com.cps.category_type') && $action_id && $intReward && $rewardRate) ){
                    $form->bind($request);
                    $type = implode(",",$type);
                    $path =  $this->container->getParameter('upload_adver_dir');
                    $adver->setType($type);
                    $adver->setTitle($title);
                    $adver->setStartTime(date_create($start_time));
                    $adver->setEndTime(date_create($end_time));
                    $adver->setDecription($comment);
                    $adver->setImageurl($url);
                    $adver->setIncentiveType($category);
                    $adver->setCategory($category);
                    if($category==1){
                        $adver->setIncentive($score);
                    }else {
                        if( $category == $this->container->getParameter('emar_com.cps.category_type')){
                            $adver->setActionId($action_id);
                        }
                        $adver->setIncentiveRate($intReward);
                        $adver->setRewardRate($rewardRate);
                    }
                    $adver->setContent($info);
                    $adver->setInfo($rule);
                    $adver->setDeleteFlag($this->container->getParameter('init'));
                    $em->persist($adver);
                    $code = $adver->upload($path);
                    if(!$code){
                        $em->flush();
                        if($adver->getIncentiveType()==1){
                            $limit = new LimitAd();
                            $limit->setAdId($adver->getId());
                            $limit->setIncentive($adver->getIncentive());
                            $limit->setIncome(floor($adver->getIncentive()/30));
                            $em->persist($limit);
                            $em->flush();
                        }else{
                            $rate = new RateAd();
                            $rate->setAdId($adver->getId());
                            $rate->setIncentiveRate($adver->getIncentiveRate());
                            $em->persist($rate);
                            $em->flush();
                            
                        }
                        return $this->redirect($this->generateUrl('_admin_infoAdver'));
                    }

                }else{
                    $codeflag = $this->container->getParameter('init_one');
                }
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:addAdver.html.twig',
                            array(
                                'form' => $form->createView(),
                                'code'=>$code,
                                'codeflag'=>$codeflag,
                                'title'=>$title,
                                'start_time'=>$start_time,
                                'end_time'=>$end_time,
                                'info'=>$info,
                                'comment'=> $comment,
                                'url'=> $url,
                                'category'=>$category,
                                'score'=>$score,
                                'rule'=>$rule,
                                'actCategory'=>$actCategory
                                ));
    }
    
    /**
     * @Route("/infoAdver", name="_admin_infoAdver")
     */
    public function infoAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $arr['title'] = '';
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAllAdvertiserList();
//      $time =  $adver[0]['endTime']->format('Y-m-d H:i:s');
        if ($request->getMethod() == 'POST') {
           $title = $request->request->get('title');
           $adver = $em->getRepository('JiliApiBundle:Advertiserment')->searchTitle($title);
           $arr['title'] =  $title;
        }
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($adver,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoAdver.html.twig',$arr);
         
    }
    
    /**
     * @Route("/stopAdver/{id}", name="_admin_stopAdver")
     */
    public function stopAdverAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
        $stopTime = date("Y-m-d",strtotime("-1 day"));
        $adver->setEndTime(date_create($stopTime));
        $em->persist($adver);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoAdver'));
    }
    
    /**
     * @Route("/delAdver/{id}", name="_admin_delAdver")
     */
    public function delAdverAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
        $adver->setDeleteFlag($this->container->getParameter('init_one'));
        $em->persist($adver);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoAdver'));
    }
    
    /**
     * @Route("/editAdver/{id}", name="_admin_editAdver")
     */
    public function editAdverAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $codeflag = $this->container->getParameter('init');
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
        $actCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
        $newType = explode(",",$adver->getType());
        $request = $this->get('request');
        $title = $request->request->get('title');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');
        $info = $request->request->get('info');
        $comment = $request->request->get('comment');
        $url = $request->request->get('url');
        $category = $request->request->get('category');
        $score = $request->request->get('score');
        $action_id = $request->request->get('actionId');
        $intReward = $request->request->get('intReward');
        $rewardRate = $request->request->get('rewardRate');
        $rule = $request->request->get('rule');
        $type = $request->request->get('type');
        $form  = $this->createForm(new AddAdverType(),$adver);
        if ($request->getMethod() == 'POST') {
            if($title && $start_time && $end_time && $info && $comment && $rule && $url && $category){
                if(($category == 1 && $score) || ($category == 2 && $intReward && $rewardRate) ||($category ==  $this->container->getParameter('emar_com.cps.category_type') && $action_id && $intReward && $rewardRate) ){
                    $form->bind($request);
                    $path =  $this->container->getParameter('upload_adver_dir');
                    $type = implode(",",$type);
                    $adver->setType($type);
                    $adver->setTitle($title);
                    $adver->setStartTime(date_create($start_time));
                    $adver->setEndTime(date_create($end_time));
                    $adver->setDecription($comment);
                    $adver->setImageurl($url);
                    $adver->setIncentiveType($category);
                    $adver->setCategory($category);
                    if($category==1){
                        $adver->setIncentive($score);
                    }else{
                        if( $category == $this->container->getParameter('emar_com.cps.category_type')){
                            $adver->setActionId($action_id);
                        }
                        $adver->setIncentiveRate($intReward);
                        $adver->setRewardRate($rewardRate);
                    }
                    $adver->setContent($info);
                    $adver->setInfo($rule);
                    $adver->setDeleteFlag($this->container->getParameter('init'));
                    $em->persist($adver);
                    $code = $adver->upload($path);
                    if(!$code || $code=='图片为必填项'){
                        $em->flush();
                        if($adver->getIncentiveType()==1){
                            $limit = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adver->getId());
                            if(empty($limit)){
                                $del = $em->getRepository('JiliApiBundle:RateAd')->findByAdId($adver->getId());
                                $em->remove($del[0]);
                                $em->flush();
                                $new_limit = new LimitAd();
                                $new_limit->setAdId($adver->getId());
                                $new_limit->setIncentive($adver->getIncentive());
                                $new_limit->setIncome(floor($adver->getIncentive()/30));
                                $em->persist($new_limit);
                                $em->flush();
                            }else{
                                $limit[0]->setAdId($adver->getId());
                                $limit[0]->setIncentive($adver->getIncentive());
                                $limit[0]->setIncome(floor($adver->getIncentive()/30));
                                $em->persist($limit[0]);
                                $em->flush();
                            }
                        }else{
                            $rate = $em->getRepository('JiliApiBundle:RateAd')->findByAdId($adver->getId());
                            if(empty($rate)){
                                $del = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adver->getId());
                                $em->remove($del[0]);
                                $em->flush();
                                $new_rate = new RateAd();
                                $new_rate->setAdId($adver->getId());
                                $new_rate->setIncentiveRate($adver->getIncentiveRate());
                                $em->persist($new_rate);
                                $em->flush();
                            }else{
                                $rate[0]->setAdId($adver->getId());
                                $rate[0]->setIncentiveRate($adver->getIncentiveRate());
                                $em->persist($rate[0]);
                                $em->flush();
                            }
                            
                        }
                        return $this->redirect($this->generateUrl('_admin_infoAdver'));
                    }
                }else{
                    $codeflag = $this->container->getParameter('init_one');
                }
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editAdver.html.twig',
                array(
                    'adver'=>$adver,
                    'form' => $form->createView(),
                    'code'=>$code,
                    'codeflag'=>$codeflag,
                    'title'=>$title,
                    'start_time'=>$start_time,
                    'end_time'=>$end_time,
                    'info'=>$info,
                    'comment'=> $comment,
                    'url'=> $url,
                    'category'=>$category,
                    'score'=>$score,
                    'rule'=>$rule,
                    'actCategory'=>$actCategory,
                    'newType'=>$newType
                    ));
    
    }
    
    /**
     * @Route("/editCallboard/{id}", name="_admin_editCallboard")
     */
    public function editCallboardAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cb_category = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
        $callboard = $em->getRepository('JiliApiBundle:CallBoard')->find($id);
        $title = $request->request->get('title');
        $author = $request->request->get('author');
        $start_time = $request->request->get('start_time');
        $content = $request->request->get('content');
        $category = $request->request->get('category');
        if ($request->getMethod() == 'POST') {
            if($title && $start_time  && $author && $content){
                $callboard->setTitle($title);
                $callboard->setStartTime(date_create($start_time));
                $callboard->setUpdateTime(date_create(date('Y-m-d H:i:s')));
                $callboard->setAuthor($author);
                $callboard->setContent($content);
                $callboard->setCbType($category);
                $em->persist($callboard);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCallboard'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editCallboard.html.twig',array(
                    'callboard'=>$callboard,
                    'cb_category'=>$cb_category,
                    'codeflag'=>$codeflag,
                    'title'=>$title,
                    'start_time'=>$start_time,
                    'content'=> $content,
                    'author'=>  $author,
                    ));
    }
    
    /**
     * @Route("/delCallboard/{id}", name="_admin_delCallboard")
     */
    public function delCallboardAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:CallBoard')->find($id);
        $em->remove($callboard);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    }
    
    
    
    /**
     * @Route("/infoCallboard", name="_admin_infoCallboard")
     */
    public function infoCallboardAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboard();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($callboard,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoCallboard.html.twig',$arr);
    }
    
    
    /**
     * @Route("/addCallboard", name="_admin_addCallboard")
     */
    public function addCallboardAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $callboard = new CallBoard();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $title = $request->request->get('title');
        $start_time = $request->request->get('start_time');
        $content = $request->request->get('content');
        $author = $request->request->get('author');
        $category = $request->request->get('category');
        $cb_category = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
        if ($request->getMethod() == 'POST') {
            if($title && $start_time  && $author && $content){
                $callboard->setTitle($title);
                $callboard->setStartTime(date_create($start_time));
                $callboard->setAuthor($author);
                $callboard->setContent($content);
                $callboard->setCbType($category);
                $em->persist($callboard);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCallboard'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:addCallboard.html.twig',
                array(
                    'codeflag'=>$codeflag,
                    'title'=>$title,
                    'start_time'=>$start_time,
                    'content'=> $content,
                    'author'=>$author,
                    'cb_category'=> $cb_category
                    ));
        
    }

    /**
     * @Route("/delCbType/{id}", name="_admin_delCbType")
     */
    public function delCbTypeAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $cbcategory = $em->getRepository('JiliApiBundle:CbCategory')->find($id);
        $em->remove($cbcategory);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCbType'));
    }
    

     /**
     * @Route("/editCbType/{id}", name="_admin_editCbType")
     */
    public function editCbTypeAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cbcategory = $em->getRepository('JiliApiBundle:CbCategory')->find($id);
        $cbname = $request->request->get('categoryName');
        if ($request->getMethod() == 'POST') {
            if($cbname){
                $cbcategory->setCategoryName($cbname);
                $em->persist($cbcategory);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCbType'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editCbType.html.twig',array(
                    'cbcategory'=>$cbcategory,
                    'codeflag'=>$codeflag
                    ));
    
    }

    /**
     * @Route("/infoCbType", name="_admin_infoCbType")
     */
    public function infoCbTypeAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cbType = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($cbType,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoCbType.html.twig',$arr);              

    }

    /**
     * @Route("/addCbType", name="_admin_addCbType")
     */
    public function addCbTypeAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $cbcategory = new CbCategory();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $cbname = $request->request->get('categoryName');
        if ($request->getMethod() == 'POST') {
            if($cbname){
                $cbcategory->setCategoryName($cbname);
                $em->persist($cbcategory);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCbType'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:addCbType.html.twig',array('codeflag'=>$codeflag));
                    

    }
    
    /**
     * @Route("/exchangeCsv", name="_admin_exchangeCsv")
     */
    public function exchangeCsvAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $response = new Response();

        $request = $this->get('request');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');

        $em = $this->getDoctrine()->getManager();
        $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->exchangeInfo();
        $arr['exchange'] = $exchange;
        $response =  $this->render('JiliApiBundle:Admin:exchangeCsv.html.twig',$arr);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
//      $response->headers->set("Content-type","application/vnd.ms-excel; charset=utf-8");
        $filename = "export".date("YmdHis").".csv";
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
//         $response->headers->set('Content-Transfer-Encoding', 'binary');
//         $response->headers->set("Expires","0");
//         $response->headers->set("Pragma","no-cache");
        return $response;
        
    }

    public function exchangeOK($exchange_id,$email,$status,$points,$finish_time,$type){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        if(!$exchanges->getStatus()){
            $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
            if(strlen($userInfo[0]->getId())>1){
                $uid = substr($userInfo[0]->getId(),-1,1);
            }else{
                $uid = $userInfo[0]->getId();
            }
            switch($uid){
                case 0:
                    $po = new PointHistory00();
                    break;
                case 1:
                    $po = new PointHistory01();
                    break;
                case 2:
                    $po = new PointHistory02();
                    break;
                case 3:
                    $po = new PointHistory03();
                    break;
                case 4:
                    $po = new PointHistory04();
                    break;
                case 5:
                    $po = new PointHistory05();
                    break;
                case 6:
                    $po = new PointHistory06();
                    break;
                case 7:
                    $po = new PointHistory07();
                    break;
                case 8:
                    $po = new PointHistory08();
                    break;
                case 9:
                    $po = new PointHistory09();
                    break;
            }
            $po->setUserId($userInfo[0]->getId());
            $po->setPointChangeNum('-'.$points);
            if($type == 1)
              $po->setReason($this->container->getParameter('init_eight'));
            if($type == 2)  
              $po->setReason($this->container->getParameter('page_num'));
            if($type == 3)  
              $po->setReason($this->container->getParameter('init_eleven'));
            if($type == 4)  
              $po->setReason($this->container->getParameter('init_twelve'));
            $em->persist($po);
            $em->flush();
            $exchanges->setStatus($this->container->getParameter('init_one'));
            $exchanges->setFinishDate(date_create($finish_time));
            $em->persist($exchanges);
            $em->flush();
        }
        return true;
    }

    public function exchangeNg($exchange_id,$email,$status,$points,$finish_time){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        if(!$exchanges->getStatus()){
            $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
            $user = $em->getRepository('JiliApiBundle:User')->find($userInfo[0]->getId());
            $user->setPoints(intval($user->getPoints() + $points));
            $em->persist($user);
            $em->flush();
            $exchanges->setStatus($this->container->getParameter('init_two'));
            $exchanges->setFinishDate(date_create($finish_time));
            $em->persist($exchanges);
            $em->flush();
        }   
        return true;
    }

    public function handleExchange($file,$type){
       $em = $this->getDoctrine()->getManager();
       if($type == 1 || $type == 3 || $type == 4){
          foreach ($file as $k=>$v){
              $exchange_id = $v[0];
              $email = iconv('gb2312','UTF-8//IGNORE',$v[1]);
              if($type == 1){
                  $status = $v[6];
                  $finish_time = $v[7];
                  $points = $v[4];
              } 
              if($type == 3){
                  $status = $v[8];
                  $finish_time = $v[9];
                  $points = $v[5];
              }
              if($type == 4){
                  $status = $v[7];
                  $finish_time = $v[8];
                  $points = $v[4];
              }
              $ear = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
              if($status == 'ok'){
                  $this->exchangeOK($exchange_id,$email,$status,$points,$finish_time,$type);
                  $this->exchangeSendMs($type,$ear->getUserId());
              }else{
                  $this->exchangeNg($exchange_id,$email,$status,$points,$finish_time); 
                  $this->exchangeSendMsFail($type,$ear->getUserId());           
              }
          }
       }
       if($type == 2){
          foreach ($file as $k=>$v){
                $exchange_id = $v[0];
                $email = iconv('gb2312','UTF-8//IGNORE',$v[1]);
                $status = $v[6];
                $finish_time = $v[7];
                $points = $v[3];
                $amazonCard1 = $v[8];
                $amazonCard2 = $v[9];
                $amazonCard3 = $v[10];
                $amazonCard4 = $v[11];
                $amazonCard5 = $v[12];
                $ear = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
                if($status == 'ok'){
                    $this->exchangeOK($exchange_id,$email,$status,$points,$finish_time,$type);
                    $ex = $em->getRepository('JiliApiBundle:ExchangeAmazonResult')->findByExchangeId($exchange_id);
                    if(empty($ex)){
                      $exchangeAmazon = new ExchangeAmazonResult();
                      $exchangeAmazon->setExchangeId($exchange_id);
                      $exchangeAmazon->setAmazonCardOne($amazonCard1);
                      $exchangeAmazon->setAmazonCardTwo($amazonCard2);
                      $exchangeAmazon->setAmazonCardThree($amazonCard3);
                      $exchangeAmazon->setAmazonCardFour($amazonCard4);
                      $exchangeAmazon->setAmazonCardFive($amazonCard5);
                      $em->persist($exchangeAmazon);
                      $em->flush();
                      $this->exchangeSendMs($type,$ear->getUserId());
                    } 
                }else{
                    $this->exchangeNg($exchange_id,$email,$status,$points,$finish_time);  
                    $this->exchangeSendMsFail($type,$ear->getUserId());     
                }
            }
       }
       return true;       

    }

    public function exchangeSendMsFail($type,$uid){
        $title = '';
        $content = '';
        switch ($type) {
            case '2':
                $title = $this->container->getParameter('exchange_fail_amazon_tilte');
                $content = $this->container->getParameter('exchange_fail_amazon_content');
                break;
            case '3':
                $title = $this->container->getParameter('exchange_fail_alipay_tilte');
                $content = $this->container->getParameter('exchange_fail_alipay_content');
                break;
            case '4':
                $title = $this->container->getParameter('exchange_fail_mobile_tilte');
                $content = $this->container->getParameter('exchange_fail_mobile_content');
                break;
            default:
                break;
        }
        if($title && $content){
          $parms = array(
                  'userid' => $uid,
                  'title' => $title,
                  'content' => $content
                );
          $this->insertSendMs($parms);
        }
    }

    public function exchangeSendMs($type,$uid){
        $title = '';
        $content = '';
        switch ($type) {
            case '2':
                $title = $this->container->getParameter('exchange_finish_amazon_tilte');
                $content = $this->container->getParameter('exchange_finish_amazon_content');
                break;
            case '3':
                $title = $this->container->getParameter('exchange_finish_alipay_tilte');
                $content = $this->container->getParameter('exchange_finish_alipay_content');
                break;
            case '4':
                $title = $this->container->getParameter('exchange_finish_mobile_tilte');
                $content = $this->container->getParameter('exchange_finish_mobile_content');
                break;
            default:
                break;
        }
        if($title && $content){
          $parms = array(
                  'userid' => $uid,
                  'title' => $title,
                  'content' => $content
                );
          $this->insertSendMs($parms);
        }
    }


    public function exchangeOKWen($email,$points){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if(strlen($userInfo[0]->getId())>1){
            $uid = substr($userInfo[0]->getId(),-1,1);
        }else{
            $uid = $userInfo[0]->getId();
        }
        switch($uid){
            case 0:
                $po = new PointHistory00();
                break;
            case 1:
                $po = new PointHistory01();
                break;
            case 2:
                $po = new PointHistory02();
                break;
            case 3:
                $po = new PointHistory03();
                break;
            case 4:
                $po = new PointHistory04();
                break;
            case 5:
                $po = new PointHistory05();
                break;
            case 6:
                $po = new PointHistory06();
                break;
            case 7:
                $po = new PointHistory07();
                break;
            case 8:
                $po = new PointHistory08();
                break;
            case 9:
                $po = new PointHistory09();
                break;
        }
        $po->setUserId($userInfo[0]->getId());
        $po->setPointChangeNum('+'.$points);
        $po->setReason($this->container->getParameter('init_thirteen'));
        $em->persist($po);
        $em->flush();
        $user = $em->getRepository('JiliApiBundle:User')->find($userInfo[0]->getId());
        $user->setPoints(intval($user->getPoints() + $points));
        return true;
    }

    //成功发放
    public function insertExWenwen($parms=array()){
      extract($parms);
      $em = $this->getDoctrine()->getManager();
      $exFromWen = new ExchangeFromWenwen();
      $exFromWen->setWenwenExchangeId($wenwenExId);
      $exFromWen->setPaymentPoint($points);
      $exFromWen->setUserId($userId);
      $exFromWen->setEmail($email);
      $exFromWen->setStatus($this->container->getParameter('init_one'));
      $em->persist($exFromWen);
      $em->flush();

    }

     //失败发放
    public function insertFailExWenwen($parms=array()){
      extract($parms);
      $em = $this->getDoctrine()->getManager();
      $exFromWen = new ExchangeFromWenwen();
      $exFromWen->setWenwenExchangeId($wenwenExId);
      $exFromWen->setPaymentPoint($points);
      $exFromWen->setEmail($email);
      $exFromWen->setReason($reason);
      $em->persist($exFromWen);
      $em->flush();

    }



    //91wenwen兑换列表
    public function handleExchangeWen($file){
      $code = array();
      $title = $this->container->getParameter('exchange_finish_wenwen_title');
      $content = $this->container->getParameter('ecchange_finish_wenwen_content');
      $em = $this->getDoctrine()->getManager();
      foreach ($file as $k=>$v){
          $email = iconv('gb2312','UTF-8//IGNORE',$v[1]);
          $wenwenExId = $v[0];
          $points = $v[3];
          $wenwenEx = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($wenwenExId);
          if(empty($wenwenEx)){
              $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
              if(empty($userInfo)){
                 $array = array(
                          'wenwenExId' => $wenwenExId,
                          'email' => $email,
                          'points' => $points,
                          'reason' => 'account not exists'
                      );
                 $this->insertFailExWenwen($array);
                 $code[] = $wenwenExId.'兑换失败';
              }elseif(!$userInfo[0]->getPwd()){
                $array = array(
                          'wenwenExId' => $wenwenExId,
                          'email' => $email,
                          'points' => $points,
                          'reason' => '账号没有激活'
                      );
                 $this->insertFailExWenwen($array);
                 $code[] = $wenwenExId.'兑换失败';
              }else{
                $array = array(
                          'wenwenExId' => $wenwenExId,
                          'userId' => $userInfo[0]->getId(),
                          'email' => $email,
                          'points' => $points,
                          'status' => $this->container->getParameter('init_one')
                      );
                 $this->insertExWenwen($array);
                 $this->exchangeOKWen($email,$points);
                 $parms = array(
                      'userid' => $userInfo[0]->getId(),
                      'title' => $title,
                      'content' => $content
                    );
                  $this->insertSendMs($parms);
              }
            }else{
              $code[] = $wenwenExId.'已发放';

          }
          
      }
      return $code;
       
    }


    /**
     * @Route("/exchangeInWen", name="_admin_exchangeInWen")
     */
    public function exchangeInWenAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $success = '';
        $code = array();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        if ($request->getMethod('post') == 'POST') {
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                if($file){
                  $handle = fopen($file,'r'); 
                  while ($data = fgetcsv($handle)){ 
                     $goods_list[] = $data;
                  }
                  unset($goods_list[0]);
                  $flag = $this->handleExchangeWen($goods_list);
                  $success = $this->container->getParameter('init_one');
                  if($flag){
                    $code = $flag;
                  }
                  fclose($handle);
                }else{
                  $success = $this->container->getParameter('init_two');
                }
            }
        }
        $arr['success'] = $success;
        $arr['code'] = $code;
        return $this->render('JiliApiBundle:Admin:exchangeInWen.html.twig',$arr);

    }


    /**
     * @Route("/exchangeCsvWen", name="_admin_exchangeCsvWen")
     */
    public function exchangeCsvWenAction()
    {
      if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
      $start_time = date('Y-m-d');
      $end_time = date('Y-m-d');
      $em = $this->getDoctrine()->getManager();
      $request = $this->get('request');
      if($request->query->get('start')){
          $start_time = $request->query->get('start');
      }
      if($request->query->get('end')){
          $end_time = $request->query->get('end');
      }
      $exFrWen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->exFromWen($start_time,$end_time);
      $paginator  = $this->get('knp_paginator');
      $arr['pagination'] = $paginator->paginate(
              $exFrWen,
              $this->get('request')->query->get('page', 1),
              20
      );
      $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig'); 
      if ($request->getMethod('post') == 'POST') {
          $start_time = $request->request->get('start_time');
          $end_time = $request->request->get('end_time');
          if($request->request->get('add')){
                $response = new Response();   
                $exchange = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->exFromWen($start_time,$end_time);
                $arr['exchange'] = $exchange;
                $response =  $this->render('JiliApiBundle:Admin:exchangeFromWenWenCsv.html.twig',$arr);
                $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                $filename = "export".date("YmdHis").".csv";
                $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
                return $response;     
          }
      }
      $arr['start'] = $start_time;
      $arr['end'] = $end_time;
      return $this->render('JiliApiBundle:Admin:exchangeCsvWen.html.twig',$arr);
    }


    /**
     * @Route("/exchangeIn", name="_admin_exchangeIn")
     */
    public function exchangeInAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $success = '';
        $exType = '';
        $em = $this->getDoctrine()->getManager();
        $exchangeType = $em->getRepository('JiliApiBundle:PointsExchangeType')->findAll();
        $arr['exchangeType'] = $exchangeType;
        $request = $this->get('request');
        if ($request->getMethod('post') == 'POST') {
            $exType = $request->request->get('exchangeType');
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                if($file){
                  $handle = fopen($file,'r'); 
                  while ($data = fgetcsv($handle)){ 
                     $goods_list[] = $data;
                  }
                   if($exType == 1){
                      if($goods_list[0][2] != '91wenwen_user'){
                        $success = $this->container->getParameter('init_three');
                      }else{
                        unset($goods_list[0]);
                        if($this->handleExchange($goods_list,$exType)){
                            $success = $this->container->getParameter('init_one');
                        }
                      }
                   }
                   if($exType == 2){
                        if($goods_list[0][8] != 'amazonCard1'){
                           $success = $this->container->getParameter('init_three');
                        }else{
                          unset($goods_list[0]);
                          if($this->handleExchange($goods_list,$exType)){
                              $success = $this->container->getParameter('init_one');
                          }
                        }
                   }
                   if($exType == 3){
                      if($goods_list[0][2] != 'alipay_user'){
                        $success = $this->container->getParameter('init_three');
                      }else{
                        unset($goods_list[0]);
                        if($this->handleExchange($goods_list,$exType)){
                            $success = $this->container->getParameter('init_one');
                        }
                      }
                   }
                  if($exType == 4){
                      if($goods_list[0][2] != 'mobile'){
                        $success = $this->container->getParameter('init_three');
                      }else{
                        unset($goods_list[0]);
                        if($this->handleExchange($goods_list,$exType)){
                            $success = $this->container->getParameter('init_one');
                        }
                      }
                   }
                  fclose($handle);
                }else{
                  $success = $this->container->getParameter('init_two');
                }
            }
        }
        $arr['success'] = $success;
        $arr['exType'] = $exType;
        return $this->render('JiliApiBundle:Admin:exchangeIn.html.twig',$arr);

    }

    /**
     * @Route("/exchange/list", name="_admin_exchange_list")
     * @Template
     */
    public function exchangeListAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $arr= array();
        #$logger= $this->get('logger');
        $request=$this->get('request');
        $em = $this->getDoctrine()->getManager();


        // get the exchange type 
        $exchangeType = $em->getRepository('JiliApiBundle:PointsExchangeType')->findAll();
        if ($request->getMethod() == 'GET') {
            $start_date = $request->query->get('start'); //date('Y-m-d', 0));
            $end_date = $request->query->get('end'); // date('Y-m-d'));
            $exType = $request->query->get('exType');

            $rows_per_page = 20;
            $page = $request->query->get('page',1);

            $wheres = array( 'start_date'=> $start_date, 'end_date'=> $end_date, 'type'=> (int) $exType );

        } else if ($request->getMethod() == 'POST') {

            $start_time = $request->request->get('start_time');
            $end_time = $request->request->get('end_time');
            $exType = $request->request->get('exchangeType');

            if($request->request->get('add')){
                $response = new Response();   
                $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->getExDateInfo($start_time,$end_time,$exType);

                foreach ($exchange as $key => $value) {
                    $exchangeDanger = $em->getRepository('JiliApiBundle:ExchangeDanger')->findByExchangeId($value['id']);
                    $exchange[$key]['mobile']='';
                    $exchange[$key]['ident'] = '';
                    $exchange[$key]['ip'] = '';
                    $exchange[$key]['pwd'] = '';
                    if(!empty($exchangeDanger)){	
                        foreach ($exchangeDanger as $key1 => $value1) {
                            if($value1->getDangerType() == $this->container->getParameter('init_one'))
                                $exchange[$key]['mobile'] = $this->container->getParameter('init_one');                 
                            if($value1->getDangerType() == $this->container->getParameter('init_two'))
                                $exchange[$key]['ip'] = $this->container->getParameter('init_one');                
                            if($value1->getDangerType() == $this->container->getParameter('init_three'))
                                $exchange[$key]['ident'] = $this->container->getParameter('init_one');
                            if($value1->getDangerType() == $this->container->getParameter('init_four'))
                                $exchange[$key]['pwd'] = $this->container->getParameter('init_one');
                        }
                    }
                }

                $arr['exchange'] = $exchange;
                if($exType == 1)
                    $response =  $this->render('JiliApiBundle:Admin:exchangeCsv.html.twig',$arr);
                if($exType == 2)
                    $response =  $this->render('JiliApiBundle:Admin:exchangeAmazonCsv.html.twig',$arr);
                if($exType == 3)
                    $response =  $this->render('JiliApiBundle:Admin:exchangeAlipayCsv.html.twig',$arr);
                if($exType == 4)
                    $response =  $this->render('JiliApiBundle:Admin:exchangeMobileCsv.html.twig',$arr);
                $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                $filename = "export".date("YmdHis").".csv";
                $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
                return $response;     
            } 


            //////////////////////////////////////
            //     $start_date = $request->request->get('start_time'); //date('Y-m-d', 0));
            //     $end_date = $request->request->get('end_time'); // date('Y-m-d'));
            //     $exType = $request->request->get('exchangeType');
            //     //update the conditions 
            //     $wheres = array();
        }


        $pointExchangeRepository = $em->getRepository('JiliApiBundle:PointsExchange');

        $count = $pointExchangeRepository->getCount($wheres );
        
        $params = array( 'page'=> $page, 
            'count'=>$rows_per_page );

        $exchange = $pointExchangeRepository->getCurrent($params , $wheres);

        $user_ids = array();
        $exchange_ids = array();
        foreach ($exchange as $key => $value) {
            $user_ids[] = $value['userId'];
            $exchange_ids[] = $value['id'];
        }

        $exchangeDangers = $em->getRepository('JiliApiBundle:ExchangeDanger')->findByExchangeIds($exchange_ids);

        $emails = $em->getRepository('JiliApiBundle:User')->findEmailById($user_ids );

        foreach($exchange as $key => $value) {
            $eid = $value['id']; 
            $uid = $value['userId']; 

           $exchange[$key]['email']= isset( $emails[$uid] ) ? $emails[ $uid ] : '';
           $exchange[$key]['mobile']='';
           $exchange[$key]['ident'] = '';
           $exchange[$key]['ip'] = '';
           $exchange[$key]['pwd'] = '';

           $exchangeDanger = isset( $exchangeDangers [$eid] )  ? $exchangeDangers [$eid]   : array() ;

           if(!empty($exchangeDanger)){
              foreach ($exchangeDanger as $key1 => $value1) {
                 if($value1->getDangerType() == $this->container->getParameter('init_one'))
                    $exchange[$key]['mobile'] = $this->container->getParameter('init_one');                 
                 if($value1->getDangerType() == $this->container->getParameter('init_two'))
                    $exchange[$key]['ip'] = $this->container->getParameter('init_one');                
                 if($value1->getDangerType() == $this->container->getParameter('init_three'))
                    $exchange[$key]['ident'] = $this->container->getParameter('init_one');
                 if($value1->getDangerType() == $this->container->getParameter('init_four'))
                    $exchange[$key]['pwd'] = $this->container->getParameter('init_one');
              }
           }
        }
#


        $arr['pages'] = ceil( $count /$rows_per_page );
        $arr['page'] = $page;

        $arr['pagination'] = $exchange;
        $arr['start'] = $start_date;
        $arr['end'] = $end_date;
        $arr['exType'] = $exType;
        $arr['exchangeType'] = $exchangeType;

        return $arr;
    }

    /**
     * @Route("/exchangeInfo", name="_admin_exchangeInfo")
     */
    public function exchangeInfoAction()
    {

        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $start_time = '';
        $end_time = '';
        $exType = $this->container->getParameter('init');
        $request = $this->get('request');

        $start_time = $request->query->get('start');
        $end_time = $request->query->get('end');
        $exType = $request->query->get('exType');
        $em = $this->getDoctrine()->getManager();
        $exchangeType = $em->getRepository('JiliApiBundle:PointsExchangeType')->findAll();

        $arr['exchangeType'] = $exchangeType;
        $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->getExDateInfo($start_time,$end_time,$exType);
        foreach ($exchange as $key => $value) {
           $exchangeDanger = $em->getRepository('JiliApiBundle:ExchangeDanger')->findByExchangeId($value['id']);
           $exchange[$key]['mobile']='';
           $exchange[$key]['ident'] = '';
           $exchange[$key]['ip'] = '';
           $exchange[$key]['pwd'] = '';
           if(!empty($exchangeDanger)){
              foreach ($exchangeDanger as $key1 => $value1) {
                 if($value1->getDangerType() == $this->container->getParameter('init_one'))
                    $exchange[$key]['mobile'] = $this->container->getParameter('init_one');                 
                 if($value1->getDangerType() == $this->container->getParameter('init_two'))
                    $exchange[$key]['ip'] = $this->container->getParameter('init_one');                
                 if($value1->getDangerType() == $this->container->getParameter('init_three'))
                    $exchange[$key]['ident'] = $this->container->getParameter('init_one');
                 if($value1->getDangerType() == $this->container->getParameter('init_four'))
                    $exchange[$key]['pwd'] = $this->container->getParameter('init_one');
              }
           }
        }
        $paginator  = $this->get('knp_paginator');
        $arr['pagination'] = $paginator->paginate(
                $exchange,
                $this->get('request')->query->get('page', 1),
                20
        );
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');              
        if ($request->getMethod() == 'POST'){
            $start_time = $request->request->get('start_time');
            $end_time = $request->request->get('end_time');
            $exType = $request->request->get('exchangeType');

            if($request->request->get('add')){
                $response = new Response();   
                $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->getExDateInfo($start_time,$end_time,$exType);

                foreach ($exchange as $key => $value) {
                    $exchangeDanger = $em->getRepository('JiliApiBundle:ExchangeDanger')->findByExchangeId($value['id']);
                    $exchange[$key]['mobile']='';
					$exchange[$key]['ident'] = '';
					$exchange[$key]['ip'] = '';
					$exchange[$key]['pwd'] = '';
                    if(!empty($exchangeDanger)){	
                        foreach ($exchangeDanger as $key1 => $value1) {
                            if($value1->getDangerType() == $this->container->getParameter('init_one'))
								$exchange[$key]['mobile'] = $this->container->getParameter('init_one');                 
							if($value1->getDangerType() == $this->container->getParameter('init_two'))
								$exchange[$key]['ip'] = $this->container->getParameter('init_one');                
							if($value1->getDangerType() == $this->container->getParameter('init_three'))
								$exchange[$key]['ident'] = $this->container->getParameter('init_one');
							if($value1->getDangerType() == $this->container->getParameter('init_four'))
								$exchange[$key]['pwd'] = $this->container->getParameter('init_one');
                        }
                    }
                }

                $arr['exchange'] = $exchange;
                if($exType == 1)
                  $response =  $this->render('JiliApiBundle:Admin:exchangeCsv.html.twig',$arr);
                if($exType == 2)
                   $response =  $this->render('JiliApiBundle:Admin:exchangeAmazonCsv.html.twig',$arr);
                if($exType == 3)
                   $response =  $this->render('JiliApiBundle:Admin:exchangeAlipayCsv.html.twig',$arr);
                if($exType == 4)
                   $response =  $this->render('JiliApiBundle:Admin:exchangeMobileCsv.html.twig',$arr);
                $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                $filename = "export".date("YmdHis").".csv";
                $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
                return $response;     
            } 
           
        }
        $arr['start'] = $start_time;
        $arr['end'] = $end_time;
        $arr['exType'] = $exType;
        
        return $this->render('JiliApiBundle:Admin:exchangeInfo.html.twig',$arr);
    }

    /**
     * @Route("/selectUser", name="_admin_selectUser")
     */
    public function selectUserAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $count_user = '';
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');

        //getUserCount($start = false, $end = false, $pwd = false, $is_from_wenwen= false, $delete_flag = false)

        $count1 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time,true,1);
        $count1 = $count1['num'];

        $count2 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time,true,2);
        $count2 = $count2['num'];

        $count3 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time,true);
        $count3 = $count3['num'];

        $count4 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time,false,1);
        $count4 = $count4['num'];

        $count5 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time,false,2);
        $count5 = $count5['num'];

        $count6 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time);
        $count6 = $count6['num'];

        $count7 = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time,false,false,true);
        $count7 = $count7['num'];

        $arr['code'] = $code;

        $arr['start_time'] = $start_time;
        $arr['end_time'] = $end_time;

        $arr['count1'] = $count1;
        $arr['count2'] = $count2;
        $arr['count3'] = $count3;
        $arr['count4'] = $count4;
        $arr['count5'] = $count5;
        $arr['count6'] = $count6;
        $arr['count7'] = $count7;

        return $this->render('JiliApiBundle:Admin:selectUser.html.twig',$arr);
    }


    /**
     * @Route("/rewardRate", name="_admin_rewardRate")
     */
     public function rewardRateAction(){
        if($this->getAdminIp())
              return $this->redirect($this->generateUrl('_default_error'));  
        $search = array();
        $user_multiple = '';
        $email = '';
        $edit = '';
        $code = '';
        $multiple = '';
        $timeCk = '';
        $times = '';
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $email = $request->request->get('email');
        if ($request->getMethod() == 'POST'){
            $timeCk = $request->request->get('timeCk');
            $times = $request->request->get('times');
            if($timeCk){
                $user_multiple = $em->getRepository('JiliApiBundle:User')->getMultiple($times);

            }else{
                $user = $em->getRepository('JiliApiBundle:User')->getSearch($email);
                if($user){
                    $search = $user[0];
                    $edit = $request->request->get('edit');
                    if($edit){
                        $multiple = $request->request->get('multiple');
                        $editUser = $em->getRepository('JiliApiBundle:User')->find($search['id']);
                        $editUser->setRewardMultiple($multiple);
                        $em->persist($editUser);
                        $em->flush();
                        $code  = $this->container->getParameter('init_one');
                    }
                }         
            }
             
        }
        return $this->render('JiliApiBundle:Admin:rewardRate.html.twig',array('search'=>$search,'email'=>$email,'code'=>$code,'multiple'=>$multiple,'user_multiple'=>$user_multiple));  
    }



    /**
     * @Route("/sendCb", name="_admin_sendCb")
     */
     public function sendCbAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        $sendCallboard = new SendCallboard();
        if ($request->getMethod() == 'POST') {
            if($title  && $content){
                $sendCallboard->setSendFrom($this->container->getParameter('init'));
                $sendCallboard->setSendTo($this->container->getParameter('init'));
                $sendCallboard->setTitle($title);
                $sendCallboard->setContent($content);
                $sendCallboard->setReadFlag($this->container->getParameter('init'));
                $sendCallboard->setDeleteFlag($this->container->getParameter('init'));
                $em->persist($sendCallboard);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCb'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:sendCb.html.twig',array(
                    'codeflag'=>$codeflag,
                    'title'=>$title,
                    'content'=>$content
                    ));


     }

     /**
     * @Route("/delCb/{id}", name="_admin_delCb")
     */
     public function delCbAction($id){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $sendCallboard = $em->getRepository('JiliApiBundle:SendCallboard')->find($id);
        $sendCallboard->setDeleteFlag($this->container->getParameter('init_one'));
        $em->persist($sendCallboard);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCb'));
      
     }

     /**
     * @Route("/editCb/{id}", name="_admin_editCb")
     */
     public function editCbAction($id){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $sendCbs = $em->getRepository('JiliApiBundle:SendCallboard')->find($id);
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        if ($request->getMethod() == 'POST') {
            if($title && $content){
                $sendCbs->setTitle($title);
                $sendCbs->setContent($content);
                $em->persist($sendCbs);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCb'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editCb.html.twig',array(
                    'sendCbs'=>$sendCbs,
                    'codeflag'=>$codeflag
                    ));

     }

     /**
     * @Route("/infoCb", name="_admin_infoCb")
     */
     public function infoCbAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->getSendCb();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($sendCb,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoCb.html.twig',$arr);
        
     }

     /**
     * @Route("/delMs/{id}/{sendid}", name="_admin_delMs")
     */
     public function delMsAction($id,$sendid){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $this->delSendMs($id,$sendid);
        return $this->redirect($this->generateUrl('_admin_infoMs',array('id'=>$id)));
      
     }


      /**
     * @Route("/editMs/{id}/{sendid}", name="_admin_editMs")
     */
     public function editMsAction($id,$sendid){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $sendMsById = $this->selectSendMsById($id,$sendid);
        $email = $request->request->get('email');
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        if ($request->getMethod() == 'POST') {
            if($title  && $content && $email){
                $isUserEmail = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
                if($isUserEmail){
                    $parms = array(
                      'sendid'=> $sendid,
                      'userid' => $isUserEmail[0]->getId(),
                      'title' => $title,
                      'content' => $content
                    );
                    $this->updateSendMs($parms);
                    return $this->redirect($this->generateUrl('_admin_infoMs',array('id'=>$id)));

                }else{
                    $codeflag = $this->container->getParameter('init_two');
                }
                
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }

        return $this->render('JiliApiBundle:Admin:editMs.html.twig',array(
                    'codeflag'=>$codeflag,
                    'id'=>$id,
                    'sendid'=>$sendid,
                    'sendMsById'=>$sendMsById,
                    'email'=>$email,
                    'title'=>$title,
                    'content'=>$content
                    ));

     }

      /**
     * @Route("/infoMs/{id}", name="_admin_infoMs")
     */
     public function infoMsAction($id){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $sendMs = $this->selectSendMs($id);
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($sendMs,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        $arr['id'] = $id;
        return $this->render('JiliApiBundle:Admin:infoMs.html.twig',$arr);
        
     }



      /**
     * @Route("/sendMs", name="_admin_sendMs")
     */
     public function sendMsAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        $email = $request->request->get('email');
        if ($request->getMethod() == 'POST') {
            if($title  && $content && $email){
                $isUserEmail = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
                if($isUserEmail){
                    $parms = array(
                      'userid' => $isUserEmail[0]->getId(),
                      'title' => $title,
                      'content' => $content
                    );
                    $this->insertSendMs($parms);
                    if(strlen($isUserEmail[0]->getId())>1){
                        $uid = substr($isUserEmail[0]->getId(),-1,1);
                    }else{
                        $uid = $isUserEmail[0]->getId();
                    }
                    return $this->redirect($this->generateUrl('_admin_infoMs',array('id'=>$uid)));

                }else{
                    $codeflag = $this->container->getParameter('init_two');
                }
                
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:sendMs.html.twig',array(
                    'codeflag'=>$codeflag,
                    'title'=>$title,
                    'content'=>$content,
                    'email'=>$email
                    ));
     }

    /**
     * @Route("/addActivityCategory", name="_admin_addActivityCategory")
     */
    public function addActivityCategoryAction()
    {
        //todo: add asp fields
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $actCategory = new ActivityCategory();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $categoryName = $request->request->get('category');
        if ($request->getMethod() == 'POST') {
            if($categoryName){
                $actCategory->setCategory($categoryName);
                $em->persist($actCategory);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoActivityCategory'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:addActivityCategory.html.twig',array('codeflag'=>$codeflag));           

    }

    /**
     * @Route("/editActivityCategory/{id}", name="_admin_editActivityCategory")
     */
    public function editActivityCategoryAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $actCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->find($id);
        $categoryName = $request->request->get('category');
        if ($request->getMethod() == 'POST') {
            if($categoryName){
                $actCategory->setCategory($categoryName);
                $em->persist($actCategory);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoActivityCategory'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editActivityCategory.html.twig',array(
                    'actCategory'=>$actCategory,
                    'codeflag'=>$codeflag
                    ));
    
    }

     /**
     * @Route("/infoActivityCategory", name="_admin_infoActivityCategory")
     */
    public function infoActivityCategoryAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $actCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($actCategory,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoActivityCategory.html.twig',$arr);              

    }

    /**
     * @Route("/delActivityCategory/{id}", name="_admin_delActivityCategory")
     */
    public function delActivityCategoryAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $activityCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->find($id);
        $em->remove($activityCategory);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoActivityCategory'));
    }

      /**
     * @Route("/getAdver",name="_businessActivity_getAdver")
     */
    public function getAdverAction()
    {
      $request = $this->get('request');
      $name = $request->query->get('name');
      $em = $this->getDoctrine()->getManager();
      $adverName = $em->getRepository('JiliApiBundle:Advertiserment')->getCpsSearchAd($name);
      if($adverName){
          foreach ($adverName as $key => $value){
            $arr[] = array('id'=>$value['id'],'name'=>$value['title']);
          }
      return new Response(json_encode($arr));
      }else{
          return new Response('');
      }
      
    }

    
    /**
    * @Route("/delCheckinShop/{id}", name="_admin_delCheckinShop")
    */
    public function delCheckinShopAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $checkinOne = $em->getRepository('JiliApiBundle:CheckinAdverList')->find($id);
        $em->remove($checkinOne);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCheckinShop'));
    }


   /**
     * @Route("/editCheckinShop/{id}", name="_admin_editCheckinShop")
     */
    public function editCheckinShopAction($id){
       if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $request = $this->get('request');
        $actId = $request->request->get('actId');
        $interSpace = $request->request->get('inter_space');
        $em = $this->getDoctrine()->getManager();
        $checkinOne = $em->getRepository('JiliApiBundle:CheckinAdverList')->find($id);
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAllCps(2);
        if ($request->getMethod() == 'POST') {
            if($interSpace){
                $checkinOne->setAdId($actId);
                $checkinOne->setInterSpace($interSpace);
                $em->persist($checkinOne);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCheckinShop'));
            }else{
                $code = $this->container->getParameter('init_one');
            }

        }
        return $this->render('JiliApiBundle:Admin:editCheckinShop.html.twig',
                              array('adver'=>$adver,
                                    'code'=>$code,
                                    'checkinOne'=>$checkinOne
                                    ));

    }

    /**
     * @Route("/addCheckinShop", name="_admin_addCheckinShop")
     */
    public function addCheckinShopAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $request = $this->get('request');
        $actId = $request->request->get('actId');
        $interSpace = $request->request->get('inter_space');
        $cal = new CheckinAdverList();
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAllCps(2);
        if ($request->getMethod() == 'POST') {
            if($interSpace){
                $cal->setAdId($actId);
                $cal->setInterSpace($interSpace);
                $em->persist($cal);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCheckinShop'));
            }else{
                $code = $this->container->getParameter('init_one');
            }

        }
        return $this->render('JiliApiBundle:Admin:addCheckinShop.html.twig',
                              array('adver'=>$adver,'code'=>$code));

    }

    /**
     * @Route("/infoCheckinShop", name="_admin_infoCheckinShop")
     */
    public function infoCheckinShopAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $business = $em->getRepository('JiliApiBundle:CheckinAdverList')->getAllCheckinList();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($business,
                $request->query->get('page', 1), 20);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoCheckinShop.html.twig',$arr);
         
    }

    /**
    * @Route("/delCheckinPointTimes/{id}", name="_admin_delCheckinPointTimes")
    */
    public function delCheckinPointTimesAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $checkinPoint = $em->getRepository('JiliApiBundle:CheckinPointTimes')->find($id);
        $em->remove($checkinPoint);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCheckinPointTimes'));
    }

    /**
     * @Route("/editCheckinPointTimes/{id}", name="_admin_editCheckinPointTimes")
     */
    public function editCheckinPointTimesAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $request = $this->get('request');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');
        $times = $request->request->get('times');
        $em = $this->getDoctrine()->getManager();
        $cpt = $em->getRepository('JiliApiBundle:CheckinPointTimes')->find($id);
        if ($request->getMethod() == 'POST') {
            if($start_time && $end_time && $times){
                if(empty($isDate)){
                  $cpt->setStartTime(date_create($start_time));
                  $cpt->setEndTime(date_create($end_time));
                  $cpt->setPointTimes($times);
                  $em->persist($cpt);
                  $em->flush();
                  return $this->redirect($this->generateUrl('_admin_infoCheckinPointTimes'));
                }else{
                  $code = $this->container->getParameter('init_two');
                }     
            }else{
                $code = $this->container->getParameter('init_one');
            }

        }
        return $this->render('JiliApiBundle:Admin:editCheckinPointTimes.html.twig',
                        array('code'=>$code,'cpt'=>$cpt));

    }

    /**
     * @Route("/addCheckinPointTimes", name="_admin_addCheckinPointTimes")
     */
    public function addCheckinPointTimesAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $request = $this->get('request');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');
        $times = $request->request->get('times');
        $checkInType = $request->request->get('checkInType');
        //type,1 普通， 2 注册当天签到活动
        $checkInTypes = array(1,2);
        $cpt = new CheckinPointTimes();
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            if($start_time && $end_time && $times){
                $isDate = $em->getRepository('JiliApiBundle:CheckinPointTimes')->checkDate($start_time,$end_time,$checkInType);
                if(empty($isDate)){
                  $cpt->setStartTime(date_create($start_time));
                  $cpt->setEndTime(date_create($end_time));
                  $cpt->setPointTimes($times);
                  $cpt->setCheckinType($checkInType);
                  $em->persist($cpt);
                  $em->flush();
                  return $this->redirect($this->generateUrl('_admin_infoCheckinPointTimes'));
                }else{
                  $code = $this->container->getParameter('init_two');
                }     
            }else{
                $code = $this->container->getParameter('init_one');
            }

        }
        return $this->render('JiliApiBundle:Admin:addCheckinPointTimes.html.twig',
                        array('code'=>$code,'start'=>$start_time,'end'=>$end_time,'checkInType'=>$checkInType,'checkInTypes'=>$checkInTypes ));

    }

    /**
     * @Route("/infoCheckinPointTimes", name="_admin_infoCheckinPointTimes")
     */
    public function infoCheckinPointTimesAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cpt = $em->getRepository('JiliApiBundle:CheckinPointTimes')->getAllCheckinPoint();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($cpt,
                $request->query->get('page', 1), 20);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoCheckinPointTimes.html.twig',$arr);

    }

    /**
     * @Route("/addBusinessActivity", name="_admin_addBusinessActivity")
     */
    public function addBusinessActivityAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code ='';
        $codeflag = $this->container->getParameter('init');
        $business = new MarketActivity();
        $form  = $this->createForm(new AddBusinessActivityType(),$business);
        $em = $this->getDoctrine()->getManager();
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAllCps(2);
        $actCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
        $request = $this->get('request');
        $actId = $request->request->get('actId');
        $category = $request->request->get('category');
        $businessName = $request->request->get('businessname');
        $startTime = $request->request->get('start_time');
        $endTime = $request->request->get('end_time');
        $url = $request->request->get('url');
        if ($request->getMethod() == 'POST') {
             if($actId && $startTime && $endTime && $businessName && $url && $category){ 
                $form->bind($request);
                $category = implode(",",$category);
                $path =  $this->container->getParameter('upload_activity_dir');
                $business->setAid($actId);
                $business->setBusinessName($businessName);
                $business->setCategoryId($category);
                $business->setActivityUrl($url);
                $business->setStartTime(date_create($startTime));
                $business->setEndTime(date_create($endTime));
                $em->persist($business);
                $code = $business->upload($path);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('_admin_infoBusinessActivity'));
                }  
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:addBusinessActivity.html.twig',
                    array('code'=>$code,
                          'codeflag'=>$codeflag,
                          'adver'=>$adver,
                          'actCategory'=>$actCategory,
                          'form' => $form->createView(),
                          'businessName'=>$businessName,
                          'url'=>$url,
                          'start_time'=>$startTime,
                          'end_time'=>$endTime));           

    }


    /**
     * @Route("/infoBusinessActivity", name="_admin_infoBusinessActivity")
     */
    public function infoBusinessActivityAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $business = $em->getRepository('JiliApiBundle:MarketActivity')->getAllBusinessList();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($business,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoBusinessActivity.html.twig',$arr);
         
    }

     /**
     * @Route("/editBusinessActivity/{id}", name="_admin_editBusinessActivity")
     */
    public function editBusinessActivityAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code ='';
        $codeflag = $this->container->getParameter('init');
        $em = $this->getDoctrine()->getManager();
        $business = $em->getRepository('JiliApiBundle:MarketActivity')->find($id);
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAllCps(2);
        $actCategory = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
        $newCategory = explode(",",$business->getCategoryId());
        $form  = $this->createForm(new AddBusinessActivityType(),$business);
        $request = $this->get('request');
        $actId = $request->request->get('actId');
        $category = $request->request->get('category');
        $businessName = $request->request->get('businessname');
        $startTime = $request->request->get('start_time');
        $endTime = $request->request->get('end_time');
        $url = $request->request->get('url');
        if ($request->getMethod() == 'POST') {
             if($actId && $startTime && $endTime && $businessName && $url && $category){ 
                $form->bind($request);
                $path =  $this->container->getParameter('upload_activity_dir');
                $category = implode(",",$category);
                $business->setAid($actId);
                $business->setBusinessName($businessName);
                $business->setCategoryId($category);
                $business->setActivityUrl($url);
                $business->setStartTime(date_create($startTime));
                $business->setEndTime(date_create($endTime));
                $em->persist($business);
                $code = $business->editupload($path);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('_admin_infoBusinessActivity'));
                }  
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editBusinessActivity.html.twig',
                    array(
                          'business'=>$business,
                          'code'=>$code,
                          'codeflag'=>$codeflag,
                          'actId'=>$actId,
                          'adver'=>$adver,
                          'category'=>$category,
                          'actCategory'=>$actCategory,
                          'newCategory'=>$newCategory,
                          'form' => $form->createView(),
                          'businessName'=>$businessName,
                          'url'=>$url,
                          'start_time'=>$startTime,
                          'end_time'=>$endTime));           

    }

    /**
    * @Route("/delBusinessActivity/{id}", name="_admin_delBusinessActivity")
    */
    public function delBusinessActivityAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $business = $em->getRepository('JiliApiBundle:MarketActivity')->find($id);
        $business->setDeleteFlag($this->container->getParameter('init_one'));
        $em->persist($business);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoBusinessActivity'));
    }

     /**
     * @Route("/cardInfo", name="_admin_cardInfo")
     */
    public function cardInfoAction(){
        if($this->getAdminIp())
          return $this->redirect($this->generateUrl('_default_error'));
        $arr['code'] = '';
        $arr['pagination'] = '';
        $arr['userFlag'] = '';
        $arr['card_remain'] = '';
        $request = $this->get('request');
        $userId = $request->query->get('userId');
        $arr['userFlag'] = $userId;
        if($request->getMethod() == 'POST' || $userId) {
           $em = $this->getDoctrine()->getManager();
           if($userId)
              $userFlag = $userId;
           else
              $userFlag = $request->request->get('user_flag');
           if($userFlag){
              $userCard = $em->getRepository('JiliApiBundle:CardRecordedMatch')->userCardInfo($userFlag);
              if(!empty($userCard)){
                  $paginator = $this->get('knp_paginator');
                  $arr['pagination'] = $paginator
                  ->paginate($userCard,
                  $request->query->get('page', 1), 50);
                  $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
                  $userCard_remain = $em->getRepository('JiliApiBundle:CardRecordedRemain')->userCardRemain($userFlag);
                  if(empty($userCard_remain)){
                      $arr['card_remain'] = $this->container->getParameter('init');
                  }else{
                      $arr['card_remain'] = $userCard_remain[0]['remainCount'];
                  }
              }
           }else{
              $arr['code'] = $this->container->getParameter('init_one');
           } 
           $arr['userFlag'] = $userFlag;  

        }
        
        return $this->render('JiliApiBundle:Admin:cardInfo.html.twig',$arr);
    }

    /**
     * @Route("/giveCardPoint", name="_admin_giveCardPoint")
     */
    public function giveCardPointAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
             $mc = $request->request->get('mc');
             if(empty($mc)){
                $code = $this->container->getParameter('init_one');
             }else{
                foreach ($mc as $key => $value){
                  $this->handleCReward($value);
                }
             }   
        }
        $crm = $em->getRepository('JiliApiBundle:CardRecordedMatch')->cardByTime();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($crm,
                $request->query->get('page', 1), 50);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        $arr['code'] = $code;
        return $this->render('JiliApiBundle:Admin:giveCardPoint.html.twig',$arr);

    }

     /**
     * @Route("/downCardList", name="_admin_downCardList")
     */
    public function downCardListAction(){
        // $baseUrl = $this->getRequest()->getBasePath();
        return $this->render('JiliApiBundle:Admin:downCardList.html.twig');

    }

    /**
     * @Route("/awsCsvList", name="_admin_awsCsvList")
     */
    public function awsCsvListAction(){
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $file_name = $em->getRepository('JiliApiBundle:IsReadFile')->fileByTime();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($file_name,
                $request->query->get('page', 1), 50);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:awsCsvList.html.twig',$arr);

    }

    
     /**
     * @Route("/awsDayCsc", name="_admin_awsDayCsc")
     */
    public function awsDayCscAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $success = '';
        $check_date = date("Y-m-d",strtotime('-2 day')); 
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        if ($request->getMethod('post') == 'POST') {
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                $now_file = $_FILES['csv']['name'];
                $now_date = explode('.',$now_file);
                if($file){
                  $isFile = $em->getRepository('JiliApiBundle:IsReadFile')->findByCsvFileName($now_file);
                  if(empty($isFile)){
                      if(strtotime($now_date[0])>strtotime($check_date)){
                          $success = $this->container->getParameter('init_five');
                      }else{
                          $handle = fopen($file,'r'); 
                          while ($data = fgetcsv($handle)){ 
                             $goods_list[] = $data;
                          }
                          $handlStr = explode("\t",$goods_list[0][0]);
                          if($handlStr[0] == 'user_id' && $handlStr[1] == 'match_count'){
                              unset($goods_list[0]);
                              $this->handleCard($goods_list);
                              $this->insertIsReadFile($now_file);
                              $success = $this->container->getParameter('init_one');
                          }else{
                              $success = $this->container->getParameter('init_three');
                          }
                          fclose($handle);
                      }
                  }else{
                      $success = $this->container->getParameter('init_four');
                  }
                }else{
                  $success = $this->container->getParameter('init_two');
                }
            }
        }
        $arr['success'] = $success;
        return $this->render('JiliApiBundle:Admin:awsDayCsc.html.twig',$arr);
        
    }

    private function handleCard($file){
        $em = $this->getDoctrine()->getManager();
        foreach ($file as $k=>$v){
            $handlStr = explode("\t",$v[0]);
            $uid = $handlStr[0];
            $points = $handlStr[1];
            $this->insertCardMatch($uid,$points);
        }

    }
    private function insertIsReadFile($filename){
        $em = $this->getDoctrine()->getManager();
        $irf = new IsReadFile();
        $irf->setCsvFileName($filename);
        $em->persist($irf);
        $em->flush();
    }

    private function insertCardMatch($uid,$count){
        $em = $this->getDoctrine()->getManager();
        $crm = new CardRecordedMatch();
        $crm->setUserId($uid);
        $crm->setMatchCount($count);
        $em->persist($crm);
        $em->flush();
    }

    private function insertCardRemain($uid,$count){
        $em = $this->getDoctrine()->getManager();
        $crr = new CardRecordedRemain();
        $crr->setUserId($uid);
        $crr->setRemainCount($count);
        $em->persist($crr);
        $em->flush();
    }

    private function insertCardReward($mid,$uid,$count,$point){
        $em = $this->getDoctrine()->getManager();
        $crr = new CardRecordedReward();
        $crr->setMatchId($mid);
        $crr->setUserId($uid);
        $crr->setRewardCount($count);
        $crr->setRewardPoint($point);
        $em->persist($crr);
        $em->flush();
    }

    private function insertCardTask($uid,$point){
        $parms = array(
          'orderId' => $this->container->getParameter('init'),
          'userid' => $uid,
          'task_type' => $this->container->getParameter('init_four'),
          'categoryId' => $this->container->getParameter('init_fourteen'),
          'taskName' => $this->container->getParameter('card_record'),
          'reward_percent' => $this->container->getParameter('init'),
          'point' => $point,
          'date' => date('Y-m-d H:i:s'),
          'status' => $this->container->getParameter('init_one')
        );
        $this->getTaskHistory($parms);
    }

    private function handleCReward($id){
        $is_crr_flag = '';
        $em = $this->getDoctrine()->getManager();
        $crm = $em->getRepository('JiliApiBundle:CardRecordedMatch')->find($id);
        $uid = $crm->getUserId();
        $match_count = $crm->getMatchCount();
        $crdr = $em->getRepository('JiliApiBundle:CardRecordedRemain')->findByUserId($uid);
        if(!empty($crdr)){
            $match_count = $match_count + $crdr[0]->getRemainCount();
            $is_crr_flag = $this->container->getParameter('init_one');
        }
        if($match_count%4 == 0){
            $point = $match_count/4;
            $rewardCount = $match_count;
            $remain_count = $this->container->getParameter('init');
        }else{
            $point = intval($match_count/4);
            $remain_count = $match_count%4;
            $rewardCount = $match_count-$remain_count;
        }
        $this->insertCardReward($id,$uid,$rewardCount,$point);
        if($is_crr_flag){
           $update_crdr = $em->getRepository('JiliApiBundle:CardRecordedRemain')->find($crdr['0']->getId());
           $update_crdr->setRemainCount($remain_count);
           $em->persist($update_crdr);
           $em->flush();
        }else{
          if($remain_count != $this->container->getParameter('init'))
              $this->insertCardRemain($uid,$remain_count);
        }
        $this->getPointHistory($uid,$point,$this->container->getParameter('init_fourteen'));
        $this->insertCardTask($uid,$point);
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $user->setPoints(intval($user->getPoints() + $point));
        $em->persist($user);
        $em->flush();
        $crm->setIsProvideFlag($this->container->getParameter('init_one'));
        $em->persist($crm);
        $em->flush();

    } 


    /**
     * @Route("/insertCardReward",name="_admin_insertCardReward")
     */
    public function insertCardRewardAction(){
        $request = $this->get('request');
        $id = $request->query->get('id');
        $this->handleCReward($id);
        return new Response($this->container->getParameter('init_one'));
    }


     /**
     * @Route("/",name="")
     */
    public function Action(){

    }



     private function insertSendMs($parms=array()){
      extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      switch($uid){
            case 0:
                  $sm = new SendMessage00();
                  break;
            case 1:
                  $sm = new SendMessage01();
                  break;
            case 2:
                  $sm = new SendMessage02();
                  break;
            case 3:
                  $sm = new SendMessage03();
                  break;
            case 4:
                  $sm = new SendMessage04();
                  break;
            case 5:
                  $sm = new SendMessage05();
                  break;
            case 6:
                  $sm = new SendMessage06();
                  break;
            case 7:
                  $sm = new SendMessage07();
                  break;
            case 8:
                  $sm = new SendMessage08();
                  break;
            case 9:
                  $sm = new SendMessage09();
                  break;
      }
      $em = $this->getDoctrine()->getManager();
      $sm->setSendFrom($this->container->getParameter('init'));
      $sm->setSendTo($userid);
      $sm->setTitle($title);
      $sm->setContent($content);
      $sm->setReadFlag($this->container->getParameter('init'));
      $sm->setDeleteFlag($this->container->getParameter('init'));
      $em->persist($sm);
      $em->flush();
    }


    private function delSendMs($userid,$sendid){
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
             case 0:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage00');
                  break;
            case 1:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage01');
                  break;
            case 2:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage02');
                  break;
            case 3:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage03');
                  break;
            case 4:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage04');
                  break;
            case 5:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage05');
                  break;
            case 6:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage06');
                  break;
            case 7:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage07');
                  break;
            case 8:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage08');
                  break;
            case 9:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage09');
                  break;
      }
      $delSm = $sm->find($sendid);
      $delSm->setDeleteFlag($this->container->getParameter('init_one'));
      $em->persist($delSm);
      $em->flush();
    }

    private function updateSendMs($parms=array()){
      extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
             case 0:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage00');
                  break;
            case 1:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage01');
                  break;
            case 2:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage02');
                  break;
            case 3:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage03');
                  break;
            case 4:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage04');
                  break;
            case 5:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage05');
                  break;
            case 6:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage06');
                  break;
            case 7:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage07');
                  break;
            case 8:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage08');
                  break;
            case 9:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage09');
                  break;
      }
      $updateSm = $sm->find($sendid);
      $updateSm->setSendTo($userid);
      $updateSm->setTitle($title);
      $updateSm->setContent($content);
      $em->persist($updateSm);
      $em->flush();
    }

    private function selectSendMsById($userid,$sendid){
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage00');
                  break;
            case 1:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage01');
                  break;
            case 2:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage02');
                  break;
            case 3:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage03');
                  break;
            case 4:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage04');
                  break;
            case 5:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage05');
                  break;
            case 6:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage06');
                  break;
            case 7:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage07');
                  break;
            case 8:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage08');
                  break;
            case 9:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage09');
                  break;
      }
      $showMsById = $sm->getUserSendMs($sendid);
      return $showMsById[0];

    }


    private function selectSendMs($id){
      $em = $this->getDoctrine()->getManager();
      switch($id){
            case 0:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage00');
                  break;
            case 1:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage01');
                  break;
            case 2:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage02');
                  break;
            case 3:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage03');
                  break;
            case 4:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage04');
                  break;
            case 5:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage05');
                  break;
            case 6:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage06');
                  break;
            case 7:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage07');
                  break;
            case 8:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage08');
                  break;
            case 9:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage09');
                  break;
      }
      $showMs = $sm->getSendMs();
      return $showMs;
     
    }


    public function getTaskHistory($parms=array()){
    extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      switch($uid){
            case 0:
                  $po = new TaskHistory00();
                  break;
            case 1:
                  $po = new TaskHistory01();
                  break;
            case 2:
                  $po = new TaskHistory02();
                  break;
            case 3:
                  $po = new TaskHistory03();
                  break;
            case 4:
                  $po = new TaskHistory04();
                  break;
            case 5:
                  $po = new TaskHistory05();
                  break;
            case 6:
                  $po = new TaskHistory06();
                  break;
            case 7:
                  $po = new TaskHistory07();
                  break;
            case 8:
                  $po = new TaskHistory08();
                  break;
            case 9:
                  $po = new TaskHistory09();
                  break;
      }
      $em = $this->getDoctrine()->getManager();
      $po->setOrderId($orderId);
      $po->setUserId($userid);
      $po->setTaskType($task_type);
      $po->setCategoryType($categoryId);
      $po->setTaskName($taskName);
      $po->setRewardPercent($reward_percent);

      $po->setPoint($point);

      $po->setDate(date_create($date));
      $po->setStatus($status);
      $em->persist($po);
      $em->flush();
    }
    
    /**
     * @Route("/index", name="_admin_index")
     */ 
    public function indexAction()
    {
        if($this->getAdminIp())
              return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:index.html.twig');
    }
    
    /**
     * @Route("/main", name="_admin_main")
     */
    public function mainAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
           return $this->render('JiliApiBundle:Admin:main.html.twig');
    }
    
    /**
     * @Route("/menu", name="_admin_menu")
     */
    public function menuAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:menu.html.twig');
    }
    
    /**
     * @Route("/header", name="_admin_header")
     */
    public function headerAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:header.html.twig');
    }


    /**
     * @Route("/advertisermentCheck", name="_admin_advertisermentCheck")
     */
    public function advertisermentCheckAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $filename = $this->container->getParameter('file_path_advertiserment_check');
        $arr['content'] = "";
        if (file_exists($filename)) {
            $file_handle = fopen($filename, "r");
            if ($file_handle) {
               if(filesize ($filename)){
                    $arr['content'] = fread($file_handle, filesize ($filename));
               }
            }
            fclose($file_handle);
        }
        return $this->render('JiliApiBundle:Admin:advertisermentCheck.html.twig', $arr);
    }


    /**
     * @Route("/saveAdCheck", name="_admin_saveAdCheck")
     */
    public function saveAdCheckAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $content = $request->query->get('content');
        $filename = $this->container->getParameter('file_path_advertiserment_check');
        //写文件
        $handle = fopen($filename, "w");
        if (!$handle) {
            //die("指定文件不能打开，操作中断!");
            return new Response(0);
        }
        if (fwrite($handle, $content) === FALSE) {
           return new Response(0);
        }
        fclose($handle);

        return new Response(1);
    }


    /**
     * @Route("/emergencyAnnouncement", name="_admin_emergencyAnnouncement")
     */
    public function emergencyAnnouncementAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $filename = $this->container->getParameter('file_path_emergency_announcement');
        $arr['content'] = "";
        if (file_exists($filename)) {
            $file_handle = fopen($filename, "r");
            if ($file_handle) {
               if(filesize ($filename)){
                    $arr['content'] = fread($file_handle, filesize ($filename));
               }
            }
            fclose($file_handle);
        }
        return $this->render('JiliApiBundle:Admin:emergencyAnnouncement.html.twig', $arr);
    }

    /**
     * @Route("/saveEmergencyAnnouncement", name="_admin_saveEmergencyAnnouncement")
     */
    public function saveEmergencyAnnouncementAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $content = $request->query->get('content');
        $filename = $this->container->getParameter('file_path_emergency_announcement');
        //写文件
        $handle = fopen($filename, "w");
        if (!$handle) {
            //die("指定文件不能打开，操作中断!");
            return new Response(0);
        }
        if (fwrite($handle, $content) === FALSE) {
           return new Response(0);
        }
        fclose($handle);

        return new Response(1);
    }

    /**
     * @Route("/pointManage", name="_admin_addPointManage")
     */
    public function addPointManageAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');

        //第一次进入这个页面，还没有提交
        if ($request->getMethod('post') != 'POST') {
            $arr['success'] = "";
            $arr['code'] = array();
            return $this->render('JiliApiBundle:Admin:pointManage.html.twig', $arr);
        }

        $file = $_FILES['csv'];

         //选择文件后，提交处理
        if (!$file['name']) {
            $arr['code'][] = "请选择文件";
            return $this->render('JiliApiBundle:Admin:pointManage.html.twig', $arr);
         }

        //判断是否是csv文件
        $format = explode(".", $file['name']);
        if($format[1] != "csv"){
            $arr['code'][] = "请上传csv格式，文件编码为utf-8的文件";
            return $this->render('JiliApiBundle:Admin:pointManage.html.twig', $arr);
        }

         //上传文件
        $log_dir = $this->container->getParameter('file_path_admin_point_manage');
        $fileName= date("YmdHis");
        $path = $log_dir."/"."point_import_".$fileName.".csv";
        $log_path = $log_dir."/"."point_import_".$fileName."_log.csv";
        if(!move_uploaded_file($file['tmp_name'],$path)){
            $arr['code'][] = "上传文件失败";
            return $this->render('JiliApiBundle:Admin:pointManage.html.twig', $arr);
        }

        $point_manage_service = $this->get('point_manage.processor');
        $arr = $point_manage_service->process( $path, $log_path);

        return $this->render('JiliApiBundle:Admin:pointManage.html.twig', $arr);
    }

    /**
     * @Route("/addPointSearch", name="_admin_addPointSearch")
     */
    public function addPointSearchAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $start_time = "";
        $end_time = "";
        $email = "";
        $user_id = "";
        $category_id = "";
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $start_time = $request->get('start');
        $end_time = $request->get('end');
        $email = $request->get('email');
        $user_id = $request->get('user_id');
        if($request->get('category_id')){
            $category_id = $request->get('category_id');
        };
        //所有类型
        $categoryList = $em->getRepository('JiliApiBundle:AdCategory')->getCategoryList();
        // 去除不需要的类型: 8:91问问积分 10:亚马逊礼品卡 11:支付宝 12:手机费  14:名片录力 15:积分失效
        $del = array(8,10,11,12,14,15);
        foreach($categoryList as $key=>$value){
            if(in_array($value['id'],$del)){
               unset($categoryList[$key]);
            }
        }

        $result = $em->getRepository('JiliApiBundle:User')->addPointHistorySearch($start_time,$end_time,$category_id,$email,$user_id);
        $paginator  = $this->get('knp_paginator');
        $arr['pagination'] = $paginator->paginate(
                  $result,
                  $this->get('request')->query->get('page', 1),
                  20
        );
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        $arr['start'] = $start_time;
        $arr['end'] = $end_time;
        $arr['email'] = $email;
        $arr['user_id'] = $user_id;
        $arr['category_id'] = $category_id;
        $arr['categoryList'] = $categoryList;
        return $this->render('JiliApiBundle:Admin:addPointSearch.html.twig',$arr);

    }

    /**
     * @Route("/kpiDailyRR", name="_admin_kpiDailyRR")
     */
    public function kpiDailyRRAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $kpiYMD = date("Y-m-d",strtotime("-1 day"));
        $request = $this->get('request');
        if($request->get('kpiYMD')){
            $kpiYMD = $request->get('kpiYMD');
        }

        $kpi = array();
        if($kpiYMD){
            $em = $this->getDoctrine()->getManager();
            $result = $em->getRepository('JiliApiBundle:KpiDailyRR')->findByKpiYMD($kpiYMD);
            foreach ($result as $value){
                $kpi['id'][] = $value->getId();
                $kpi['kpiYMD'][] = $value->getKpiYMD();
                $kpi['registerYMD'][] = $value->getRegisterYMD();
                $kpi['rrday'][] = $value->getRRday();
                $kpi['registerUser'][] = $value->getRegisterUser();
                $kpi['activeUser'][] = $value->getActiveUser();
                $kpi['rr'][] = $value->getRR();
            }
        }

        $arr['kpi'] = $kpi;
        $arr['kpiYMD'] = $kpiYMD;
        return $this->render('JiliApiBundle:Admin:kpiDailyRR.html.twig',$arr);
    }

    /**
     * @Route("/member", name="_admin_member")
     */
    public function memberAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        $user_id = $request->get('user_id');
        $email = $request->get('email');
        $nick = $request->get('nick');

        $member = array();
        if($user_id || $email || $nick){
            $member = $em->getRepository('JiliApiBundle:User')->memberSearch($user_id, $email, $nick);
        }else{
            $user_id = $this->get('request')->getSession()->get('member_id');
            if($user_id){
                $member = $em->getRepository('JiliApiBundle:User')->findOneById($user_id);
            }
        }

        if($member){
            $member->setDeleteFlag($member->getDeleteFlag()?"已注销":"会员");
        }

        $arr['user_id'] = $user_id;
        $arr['email'] = $email;
        $arr['nick'] = $nick;
        $arr['member'] = $member;
        return $this->render('JiliApiBundle:Admin:member.html.twig',$arr);
    }

    /**
     * @Route("/memberEdit", name="_admin_member_edit")
     */
    public function memberEditAction()
    {
        set_time_limit(1800);
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));

        $request = $this->get('request');
        $user_id = $request->get('user_id');
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('JiliApiBundle:User')->findOneById($user_id);
        $arr['member'] = $member;
        $this->get('request')->getSession()->set( 'member_id', $user_id);

        if ($request->getMethod() == 'POST') {

            $nick = $request->get('nick');
            $tel = $request->get('tel');
            $delete_flag = $request->get('delete_flag');

            $errorMessage = $this->memberCheck($member->getEmail(),$nick, $tel, $delete_flag);
            if(!$errorMessage){
                $member->setNick($nick);//验证是否存在 ，是否排除已删除的用户
                $member->setTel($tel);//用户自己也可以修改
                $member->setDeleteFlag($delete_flag);

                $em->persist($member);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_member'));
            }else{
                $arr['nick'] = $nick;
                $arr['tel'] = $tel;
                $arr['delete_flag'] = $delete_flag;
                $arr['errorMessage'] = $errorMessage;
                return $this->render('JiliApiBundle:Admin:memberEdit.html.twig',$arr);
            }

        }else{
            $arr['nick'] = $member->getNick();;
            $arr['tel'] = $member->getTel();
            $arr['delete_flag'] = $member->getDeleteFlag();
            $arr['errorMessage'] = array();
            return $this->render('JiliApiBundle:Admin:memberEdit.html.twig',$arr);
        }
    }

    private function memberCheck($email, $nick, $tel, $delete_flag){
        $errorMessage = array();
        if(!$nick){
            $errorMessage[] = "请输入昵称";
        }elseif (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u", $nick)) {
            $errorMessage[] = "昵称为2-20个字符";
        }elseif($delete_flag !=1 ){
            $em = $this->getDoctrine()->getManager();
            $user_nick = $em->getRepository('JiliApiBundle:User')->findNick($email, $nick);
            if ($user_nick){
                $errorMessage[] = "昵称已经注册";
            }
        }

        if($tel && !preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$tel)){
            $errorMessage[] = "输入的手机格式不正确";
        }
        return $errorMessage;
    }

}
