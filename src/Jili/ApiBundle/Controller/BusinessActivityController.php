<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessHistory;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\ActivityCategory;
use Jili\ApiBundle\Entity\BusinessActivity;

/**
 * @Route( requirements={"_scheme" = "http"})
 */
class BusinessActivityController extends Controller
{
    /**
	 * @Route("/index/{aid}/{cateId}",requirements={"aid" = "\d+","cateId" = "\d+"},name="_businessActivity_index")
	 */
    public function indexAction($aid = 0,$cateId = 0)
    {
        $arr = array();
        $newArr = array();
        $str = '';
        $em = $this->getDoctrine()->getManager();
        $actCate = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
        $busiAct = $em->getRepository('JiliApiBundle:BusinessActivity')->nowActivity($aid);
        $nowMall = $em->getRepository('JiliApiBundle:BusinessActivity')->nowMall();
        $nowCate = $em->getRepository('JiliApiBundle:BusinessActivity')->nowCate();
        foreach ($nowCate as $key => $value) {
            $arr[] = $value['categoryId'];
        }
        foreach ($arr as  $value) {
            $str .= $value.',';
        }
        $allCate = explode(",",$str);
        foreach ($actCate as $key => $value) {
            if(!in_array($value->getId(),$allCate)){
                unset($actCate[$key]);
            }
        }
        if($cateId){
            foreach ($busiAct as $key => $value) {
                $cate = explode(",",$value['categoryId']);
                if(!in_array($cateId,$cate)){
                    unset($busiAct[$key]);
                }
            }
        }
        return $this->render('JiliApiBundle:BusinessActivity:index.html.twig',
                    array('nowMall'=>$nowMall,
                          'cate'=>$actCate,
                          'busi'=>$busiAct,
                          'aid'=>$aid,
                          'cateId'=>$cateId
                          ));
    }

    /**
	 * @Route("/info/{id}",name="_businessActivity_info")
	 */
    public function infoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $uid = $this->get('request')->getSession()->get('uid');
        $busiAct = $em->getRepository('JiliApiBundle:BusinessActivity')->find($id);
        $adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($busiAct->getAid());
        $adw_info = explode("u=",$adver->getImageurl());
        $new_url = trim($adw_info[0])."u=".$uid.trim($adw_info[1]).$id;
        $yixun = $busiAct->getActivityUrl();
        return $this->render('JiliApiBundle:BusinessActivity:info.html.twig',
                array('url'=>$new_url,'yixun'=>$yixun));

    }



}
