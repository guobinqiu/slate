<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route( requirements={"_scheme" = "http"})
 */
class GameController extends Controller
{
    
    /**
     * @Route("/index", name="_game_chick")
     */
    //public function indexAction(){  
//        //return $this->render('JiliApiBundle:Game:chick.html.twig');
//		return $this->redirect($this->generateUrl('_game_chick'));
//    }

    /**
     * @Route("/chick", name="_game_chick")
     */
    public function chickAction(){
        // return $this->render('JiliApiBundle:Game:server.html.twig');
        $code = '';
        $arr['heightFlag'] = '';
        $arr['code'] = $code;
        if($this->checkMobile()=='noaceess'){
            $arr['code'] = $this->container->getParameter('init_one');
        }
        $em = $this->getDoctrine()->getManager();
        $uid = '';
        $uid = $this->get('request')->getSession()->get('uid');
        if(!$uid){
		   $this->getRequest()->getSession()->set('referer', $this->generateUrl('_game_chick') );
           return $this->redirect($this->generateUrl('_user_login'));
        }
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);

        $key = sha1(date("Ymd")."ADF93768CF".$uid);
        $url = "http://sugoroku01.ap.point-ad-game.com/index.php?point_uid=".$uid."&nickname=".$user->getNick()."&key=".$key;
        $arr['url'] = $url;
        if($this->getInfo($url)){
           $arr['heightFlag'] = $this->container->getParameter('init_one');
        }

        return $this->render('JiliApiBundle:Game:chick.html.twig',$arr);
    }

      
    private function checkMobile(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);   
        $is_iphone = (strpos($agent, 'iphone')) ? true : false;
        $is_ipad = (strpos($agent, 'ipad')) ? true : false;   
        $is_android = (strpos($agent, 'android')) ? true : false; 
        if($is_iphone || $is_ipad || $is_android)
            return 'noaceess';
    }

    public function getInfo($url){
        $contents = file_get_contents($url,'r');//得到文件的内容赋给字符串的变量
        $str = strstr($contents,"今日游戏数据已保存");
        if($str){
            return true;
        }else{
            return false;
        }
        // // 初始化一个 cURL 对象
        // $curl = curl_init();

        // // 设置你需要抓取的URL
        // curl_setopt($curl, CURLOPT_URL, $url);

        // // 设置header
        // curl_setopt($curl, CURLOPT_HEADER, 1);

        // // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // // 运行cURL，请求网页
        // $data = curl_exec($curl);

        // // 关闭URL请求
        // curl_close($curl);

        // // 显示获得的数据
        // return $data; 
    }
    
}
