<?php

namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Entity\RateAdResult;
use Jili\ApiBundle\Entity\LimitAdResult;
use Jili\ApiBundle\Form\EditBannerType;
use Jili\ApiBundle\Form\AddAdverType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\AdPosition;
use Jili\ApiBundle\Entity\Callboard;
use Jili\ApiBundle\Entity\LimitAd;
use Jili\ApiBundle\Entity\RateAd;
use Jili\ApiBundle\Entity\ExchangeAmazonResult;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;
use Jili\BackendBundle\Controller\IpAuthenticatedController;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Utility\ValidateUtil;
use Jili\ApiBundle\Utility\String;
use Wenwen\FrontendBundle\Entity\CategoryType;

class AdminController extends Controller implements IpAuthenticatedController
{
    /**
     * @Route("/login", name="_admin_login")
     */
    public function loginAction()
    {
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

    public function getPointHistory($userid,$point,$type)
    {
        $em = $this->getDoctrine()->getManager();
        $po = SequenseEntityClassFactory :: createInstance('PointHistory', $userid);
        $po->setUserId($userid);
        $po->setPointChangeNum($point);
        $po->setReason($type);
        $em->persist($po);
        $em->flush();
    }

    private function getStatus($uid,$aid,$ocd = '')
    {
        $em = $this->getDoctrine()->getManager();
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
        if($advertiserment->getIncentiveType()==1){
            $adwStatus = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($uid,$aid);
        } else if($advertiserment->getIncentiveType()==2){
            $adwStatus = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($uid,$aid,$ocd);
        }
        if(empty($adwStatus))
            return true;
        else
            return false;
    }

    /**
     * @Route("/editCallboard/{id}", name="_admin_editCallboard")
     */
    public function editCallboardAction($id)
    {
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cb_category = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
        $callboard = $em->getRepository('JiliApiBundle:Callboard')->find($id);
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
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:Callboard')->find($id);
        $em->remove($callboard);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    }



    /**
     * @Route("/infoCallboard", name="_admin_infoCallboard")
     */
    public function infoCallboardAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:Callboard')->getCallboard();
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
        $codeflag = $this->container->getParameter('init');
        $callboard = new Callboard();
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
     * @Route("/exchangeCsv", name="_admin_exchangeCsv")
     */
    public function exchangeCsvAction()
    {
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

    public function exchangeOK($exchange,$points,$finish_time,$type)
    {
        $em = $this->getDoctrine()->getManager();
        if(!$exchange->getStatus()){
            $pointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ($exchange->getUserId() % 10);
            $po = new $pointHistory();
            $po->setUserId($exchange->getUserId());
            $po->setPointChangeNum('-'.$points);
            //扯蛋的编号！！！
            if($type == 1) {
                $po->setReason($this->container->getParameter('init_eight'));
                $comment = '91问问';
            }
            if($type == 2) {
                $po->setReason(10); //随便啦，amazon 根本用不到
                $comment = '亚马逊礼品卡';
            }
            if($type == 3) {
                $po->setReason($this->container->getParameter('init_eleven'));
                $comment = '支付宝';
            }
            if($type == 4) {
                $po->setReason(CategoryType::MOBILE);
                $comment = '手机充值';
            }
            $em->persist($po);
            $em->flush();
            $exchange->setStatus($this->container->getParameter('init_one'));
            $exchange->setFinishDate(date_create($finish_time));
            $em->persist($exchange);
            $em->flush();

            $user = $em->getRepository('WenwenFrontendBundle:User')->find($exchange->getUserId());
            $this->get('app.latest_news_service')->insertLatestNews(mb_substr($user->getNick(), 0, 3, 'utf8') . '**' . $comment .'兑换' . $points . '积分');
        }
        return true;
    }

    public function exchangeNg($exchange, $points, $finish_time)
    {
        $em = $this->getDoctrine()->getManager();
        if(!$exchange->getStatus()){
            $user = $em->getRepository('WenwenFrontendBundle:User')->find($exchange->getUserId());
            $user->setPoints(intval($user->getPoints() + $points));
            $em->persist($user);
            $em->flush();
            $exchange->setStatus($this->container->getParameter('init_two'));
            $exchange->setFinishDate(date_create($finish_time));
            $em->persist($exchange);
            $em->flush();
        }
        return true;
    }


    /**
    *  Check items
    *  0. number of items for each line should be 14
    *  1. data.status must be 'ok' or 'ng'
    *  2. data.exchange_id must exist in points_exchange.id
    *  3. points_exchange.status must be NULL
    *  4. related user_id must exist when data.status is 'ng'
    *  @return array $error_rtn
    */
    public function checkAlipayExchangeResult($data){
        $logger= $this->get('logger');
        $logger->info('checkAlipayExchangeResult START ');
        $em = $this->getDoctrine()->getManager();

        $error_rtn = array();

        foreach ($data as $k=>$v){

            if(sizeof($v) != 14){
                $logger->error('checkAlipayExchangeResult line=[' . $k . '] number of items=['. sizeof($v) .'] is not correct. should be 14 ');
                array_push($error_rtn, implode(",", $v) . '改行数据的实际个数为'. sizeof($v) . '个，正确个数为14个');
                continue;
            }

            $exchange_id = $v[0];
            $status = strtolower(trim($v[8]));
            $finish_time = $v[9];
            $points = $v[5];

            if($status == 'ok'){
                
            } elseif($status == 'ng'){
                
            } else {
                $logger->error('checkAlipayExchangeResult points_exchange.id=[' . $exchange_id . '] status=[' . $status . '] is not right');
                array_push($error_rtn, implode(",", $v) . '改行数据的状态，只允许是ok或者ng');
                continue;
            }

            $points_exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
            if(empty($points_exchange)){
                // exchange_id should exist in points_exchange.id
                // if exchange_id not found in points_exchange
                // end checkfunction and return
                $logger->error('checkAlipayExchangeResult points_exchange.id=[' . $exchange_id . '] not found in points_exchange');
                array_push($error_rtn, implode(",", $v) . '改行数据不存在，请检查是否有对应的用户兑换申请');
                continue;
            }
            
            $current_status = $points_exchange->getStatus();
            if(!empty($current_status)){
                // points_exchange.status should be NULL
                // if points_exchange.status is not NULL, points_exchange.status is already updated
                // stop and check the situation                    
                $logger->info('checkAlipayExchangeResult points_exchange.id=[' . $exchange_id . '] already updated. current_status=[' . $current_status . ']');
                array_push($error_rtn, implode(",", $v) . '改行数据已被使用，数据库中的相关记录已被更新');
                continue;
            }

            if($status == 'ng'){
                $user_id = $points_exchange->getUserId();
                $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
                if(empty($user)){
                    // if exchange_id not found in points_exchange
                    // end checkfunction and return
                    $logger->error('checkAlipayExchangeResult points_exchange.id=[' . $exchange_id . '] user_id=[' . $user_id . '] not found in user');
                    array_push($error_rtn, implode(",", $v).'兑换失败，但是该用户已经不存在，无法通知该用户兑换的结果');
                    continue;
                }
            }
        }

        $logger->info('checkAlipayExchangeResult END   ');

        return $error_rtn;
    }

    public function handleAlipayExchangeResult($data){
        $logger= $this->get('logger');
        $logger->info('START handleAlipayExchangeResult memory_get_usage()=' . round(memory_get_usage()/1024));

        $ok_count=0;
        $ng_count=0;
        foreach ($data as $k=>$v){
            $exchange_id = $v[0];
            $status = strtolower(trim($v[8]));
            $finish_time = $v[9];
            $points = $v[5];
            
            if($status == 'ok'){
                $this->alipayExchangeOK($exchange_id,$finish_time,$points);
                $ok_count++;
            } elseif($status == 'ng'){
                $this->alipayExchangeNG($exchange_id,$finish_time,$points);
                $ng_count++;
            } 
        }
        $logger->info('ok_count=[' . $ok_count .'] ng_count=[' . $ng_count . ']');
        $logger->info('END   handleAlipayExchangeResult memory_get_usage()=' . round(memory_get_usage()/1024));
        return true;
    }

    public function alipayExchangeOK($exchange_id, $finish_time, $points){
        $logger= $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $points_exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        $user_id = $points_exchange->getUserId();
        if($points_exchange != null){
            // Start to update the alipay exchange result if points_exchange.status is NULL(exchange result not updated yet)

            // Prepare the data to update points_exchange.status 
            $points_exchange->setStatus('1');
            $points_exchange->setFinishDate(date_create($finish_time));

            // Prepare the data to create a record in point_history0x
            $po = SequenseEntityClassFactory :: createInstance('PointHistory', $user_id);
            $po->setUserId($user_id);
            $po->setPointChangeNum('-'.$points);
            $po->setReason(CategoryType::ALIPAY);

            // Prepare the data for sendMs
            $title = $this->container->getParameter('exchange_finish_alipay_tilte');
            $content = $this->container->getParameter('exchange_finish_alipay_content');
            $sm = SequenseEntityClassFactory :: createInstance('SendMessage', $user_id);
            $sm->setSendFrom($this->container->getParameter('init'));
            $sm->setSendTo($user_id);
            $sm->setTitle($title);
            $sm->setContent($content);
            $sm->setReadFlag($this->container->getParameter('init'));
            $sm->setDeleteFlag($this->container->getParameter('init'));
            
            $em->getConnection()->beginTransaction();
            try { 
                // update
                $em->persist($points_exchange);
                // insert
                $em->persist($po);
                // insert
                $em->persist($sm);
                $em->flush();
                $em->getConnection()->commit();
                $em->clear();
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                $logger->error('alipayExchangeOK update failed: ' . $e->getMessage());
                throw $e;
            }

            $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
            $this->get('app.latest_news_service')->insertLatestNews(mb_substr($user->getNick(), 0, 3, 'utf8') . '**支付宝兑换' . $points . '积分');

        } else {
            // Do nothing if points_exchange.status is not NULL(exchange result already updated)
        }
    }

    public function alipayExchangeNG($exchange_id, $finish_time, $points){
        $logger= $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $points_exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        $user_id = $points_exchange->getUserId();
        if($points_exchange != null){
            // Start to update the alipay exchange result if points_exchange.status is NULL(exchange result not updated yet)

            // Prepare the data to update points_exchange.status to 2 (exchange failed)
            $points_exchange->setStatus('2');
            $points_exchange->setFinishDate(date_create($finish_time));

            // Return the point for exchange back to user
            $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
            $user->setPoints(intval($user->getPoints() + $points));

            // Prepare the data for sendMs
            $title = $this->container->getParameter('exchange_fail_alipay_tilte');
            $content = $this->container->getParameter('exchange_fail_alipay_content');
            $sm = SequenseEntityClassFactory :: createInstance('SendMessage', $user_id);
            $sm->setSendFrom($this->container->getParameter('init'));
            $sm->setSendTo($user_id);
            $sm->setTitle($title);
            $sm->setContent($content);
            $sm->setReadFlag($this->container->getParameter('init'));
            $sm->setDeleteFlag($this->container->getParameter('init'));
            
            $em->getConnection()->beginTransaction();
            try { 
                // update
                $em->persist($points_exchange);
                // update
                $em->persist($user);
                // insert
                $em->persist($sm);
                $em->flush();
                $em->getConnection()->commit();
                $em->clear();
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                $logger->error_log('alipayExchangeOK update failed: ' . $e->getMessage());
                throw $e;
            }

        } else {
            // Do nothing if points_exchange.status is not NULL(exchange result already updated)
        }
    }


    public function handleExchange($file,$type)
    {
        $logger= $this->get('logger');
        $logger->info('START handleExchange');
       $em = $this->getDoctrine()->getManager();
       if($type == 1 || $type == 3 || $type == 4){
          foreach ($file as $k=>$v){
              $exchange_id = $v[0];
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
              $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
              if(strtolower(trim($status)) == 'ok'){
                  $this->exchangeOK($exchange,$points,$finish_time,$type);
                  $this->exchangeSendMs($type,$exchange->getUserId());
              }elseif(strtolower(trim($status)) == 'ng'){
                  $this->exchangeNg($exchange,$points,$finish_time);
                  $this->exchangeSendMsFail($type,$exchange->getUserId());
              }
          }
       }
       if($type == 2){
          foreach ($file as $k=>$v){
                $exchange_id = $v[0];
                $status = $v[6];
                $finish_time = $v[7];
                $points = $v[3];
                $amazonCard1 = $v[8];
                $amazonCard2 = $v[9];
                $amazonCard3 = $v[10];
                $amazonCard4 = $v[11];
                $amazonCard5 = $v[12];
                $ear = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
                if(strtolower(trim($status)) == 'ok'){
                    $this->exchangeOK($ear,$points,$finish_time,$type);
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
                }elseif(strtolower(trim($status)) == 'ng'){
                    $this->exchangeNg($ear,$points,$finish_time);
                    $this->exchangeSendMsFail($type,$ear->getUserId());
                }
            }
       }
       $logger->info('END   handleExchange');
       return true;

    }

    public function exchangeSendMsFail($type,$uid)
    {
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

    public function exchangeSendMs($type,$uid)
    {
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

    /**
     * @Route("/exchangeIn", name="_admin_exchangeIn")
     */
    public function exchangeInAction()
    {
        set_time_limit(1800);
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
                      // 页面上选择了【支付宝】
                      if($goods_list[0][2] != 'alipay_user'){
                        // 导入文件的第一行的第三个字段不是　alipay_user 的时候，返回状态 3
                        $success = $this->container->getParameter('init_three');
                      }else{
                        // 去掉第一行
                        unset($goods_list[0]);
                        // 检查文件内容是否正确，是否和数据库中现存数据匹配
                        $check_result = $this->checkAlipayExchangeResult($goods_list);
                        if(sizeof($check_result) == 0){
                            // 开始处理
                            if($this->handleAlipayExchangeResult($goods_list)){
                                $success = $this->container->getParameter('init_one');
                            }
                        } else {
                            // 文件内容检查没有通过
                            $success = '4';
                            $arr['error_info'] = $check_result;
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

        } elseif ($request->getMethod() == 'POST') {

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

        $emails = $em->getRepository('WenwenFrontendBundle:User')->findEmailById($user_ids );

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
        $em = $this->getDoctrine()->getManager();
        $checkinOne = $em->getRepository('JiliApiBundle:CheckinAdverList')->find($id);
        $em->remove($checkinOne);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCheckinShop'));
    }


    public function insertSendMs($parms=array())
    {
        extract($parms);
        $em = $this->getDoctrine()->getManager();
        $sm = SequenseEntityClassFactory :: createInstance('SendMessage', $userid);
        $sm->setSendFrom($this->container->getParameter('init'));
        $sm->setSendTo($userid);
        $sm->setTitle($title);
        $sm->setContent($content);
        $sm->setReadFlag($this->container->getParameter('init'));
        $sm->setDeleteFlag($this->container->getParameter('init'));
        $em->persist($sm);
        $em->flush();
    }


    public function delSendMs($userid,$sendid)
    {
      $em = $this->getDoctrine()->getManager();
      $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ( $userid % 10));
      $delSm = $sm->find($sendid);
      $delSm->setDeleteFlag($this->container->getParameter('init_one'));
      $em->persist($delSm);
      $em->flush();
    }

    public function updateSendMs($parms=array())
    {
      extract($parms);
      $em = $this->getDoctrine()->getManager();
      $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ( $userid % 10));
      $updateSm = $sm->find($sendid);
      $updateSm->setSendTo($userid);
      $updateSm->setTitle($title);
      $updateSm->setContent($content);
      $em->persist($updateSm);
      $em->flush();
    }

    public function selectSendMsById($userid,$sendid)
    {
      $em = $this->getDoctrine()->getManager();
      $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ( $userid % 10));
      $showMsById = $sm->getUserSendMs($sendid);
      return $showMsById[0];
    }


    /**
     * @param integer $id the suffix of table
     */
    public function selectSendMs($id)
    {
      $em = $this->getDoctrine()->getManager();
      $sm = $em->getRepository('JiliApiBundle:SendMessage0'.$id );
      $showMs = $sm->getSendMs();
      return $showMs;

    }

    /**
     * @Route("/index", name="_admin_index")
     */
    public function indexAction()
    {
        return $this->render('JiliApiBundle:Admin:index.html.twig');
    }

    /**
     * @Route("/main", name="_admin_main")
     */
    public function mainAction()
    {
           return $this->render('JiliApiBundle:Admin:main.html.twig');
    }

    /**
     * @Route("/menu", name="_admin_menu")
     */
    public function menuAction()
    {
        return $this->render('JiliApiBundle:Admin:menu.html.twig');
    }

    /**
     * @Route("/header", name="_admin_header")
     */
    public function headerAction()
    {
        return $this->render('JiliApiBundle:Admin:header.html.twig');
    }

    /**
     * @Route("/saveAdCheck", name="_admin_saveAdCheck")
     */
    public function saveAdCheckAction()
    {
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
        $request = $this->get('request');
        $content = $request->query->get('content');
        $filename = $this->container->getParameter('file_path_emergency_announcement');
        //写文件
        $handle = fopen($filename, "w");
        if (!$handle) {
            //die("指定文件不能打开，操作中断!");
            return new Response(0);
        }
        if (fwrite($handle, trim($content)) === FALSE) {
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
            $arr['code'][] = "请上传csv格式，文件编码为utf-8(无签名)的文件";
            return $this->render('JiliApiBundle:Admin:pointManage.html.twig', $arr);
        }

        // 检查编码是否为utf-8
        $encoding = FileUtil::isUTF8($file['tmp_name']);
        if(!$encoding){
            $arr['code'][] = "请上传csv格式，文件编码为utf-8(无签名)的文件";
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

        $result = $em->getRepository('WenwenFrontendBundle:User')->addPointHistorySearch($start_time,$end_time,$category_id,$email,$user_id);
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

        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        $user_id = $request->get('user_id');
        $email = $request->get('email');
        $nick = $request->get('nick');

        $member = array();
        if($user_id || $email || $nick){
            $member = $em->getRepository('WenwenFrontendBundle:User')->memberSearch($user_id, $email, $nick);
        }else{
            $user_id = $this->get('request')->getSession()->get('member_id');
            if($user_id){
                $member = $em->getRepository('WenwenFrontendBundle:User')->findOneById($user_id);
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
                return $this->redirect($this->generateUrl('_admin_member'));
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

    private function memberCheck($email, $nick, $tel, $delete_flag)
    {
        $errorMessage = array();
        if(!$nick){
            $errorMessage[] = "请输入昵称";
        }elseif (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u", $nick)) {
            $errorMessage[] = "昵称为2-20个字符";
        }elseif($delete_flag !=1 ){
            $em = $this->getDoctrine()->getManager();
            $user_nick = $em->getRepository('WenwenFrontendBundle:User')->findNick($email, $nick);
            if ($user_nick){
                $errorMessage[] = "昵称已经注册";
            }
        }

        if($tel && !(ValidateUtil::validateMobile($tel))){
            $errorMessage[] = "输入的手机格式不正确";
        }
        return $errorMessage;
    }

}
