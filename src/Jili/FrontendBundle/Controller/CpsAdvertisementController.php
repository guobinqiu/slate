<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\EmarBundle\Form\Type\WebsiteFilterType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/shop", requirements={"_scheme" = "http"})
 */
class CpsAdvertisementController extends Controller
{
    /**
     * @Route("/detail/{wid}", requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     */
    public function detailAction(Request $request ,$wid)
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        if(empty($wid) ) {
            throw $this->createNotFoundException( '没找到此商家.');
        } 

        $cps = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->findOneById($wid);

        if( empty($cps)) {
            throw $this->createNotFoundException('没找到此商家.');
        }

        $ad_category = $em->getRepository('JiliApiBundle:AdCategory')
            ->findOneById($cps->getAdCategoryId());
        $asp = $ad_category->getAsp();
        $shop = $em->getRepository('JiliFrontendBundle:'.ucfirst($asp).'Advertisement')->findOneById($cps->getAdId());
        $commission_list  = $em->getRepository('JiliFrontendBundle:'.ucfirst($asp).'Commission')->findListByAdId($shop->getAdsId());

        $same_cat_websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->findSameCatWebsitesByRandom( array( 'limit'=> 3, 'category'=> $cps->getWebsiteCategory() ) );

        return $this->render('JiliFrontendBundle:CpsAdvertisement:detail.html.twig',array('website'=> $cps ,
            'is_emar_cps'=> $ad_category->getIsEmarCps()  ,
            'detail' => $shop, 
            'commission_list'=>$commission_list,
            'same_cat_websites' => $same_cat_websites));
    }

    /**
     * @abstract: 会将本地配置的商家排列在前面。
     * @Route("/list")
     * @Method( {"GET","POST"})
     */
    public function listAction(Request $request)
    {
       
        $request = $this->get('request');
        $logger= $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $keyword = $request->query->get('q', '');
        $keyword = trim($keyword);
        $filter_form = $this->createForm(new WebsiteFilterType(), array('keyword'=>$keyword)  );

        #TODO: wcats with local file cache
        $wcats = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->fetchCategoryList();
        
        $websites = array();
        $dic_key = $request->query->get('t','');
        if( ! empty($dic_key) && strlen($dic_key) ===  1  ){
            $websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')->fetchByWebsiteNameDictionaryKey($dic_key);
        } else {
            $wcat =  $request->query->get('wcat', '' );
            $params = array(/* 'dic_key'=> $dic_key , */ 'keyword' => $keyword, 'wcat'=> $wcat);
            $websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')->fetchByKeywordsAndCategory($params);
        }


        # page_size , page_no
        $page_no = $request->query->getInt('p',1);

        $total =  count($websites);
        $page_size = $this->container->getParameter('emar_com.page_size_of_shoplist');

        $i = 0;
        $start = ( $page_no -1 ) * $page_size ;
        $end =  $start + $page_size;
        $websites_paged = array();
        foreach($websites as $k => $v) {
            if(  $start <= $i ) {
                if( $i >= $end ) {
                    break;
                }
                $websites_paged[$k] =$v;
            }
            $i++;
        }

        return $this->render('JiliFrontendBundle:CpsAdvertisement:list.html.twig',
            array('categories'=> $wcats,
            'websites'=> $websites_paged,
            'total'=> $total,
            'filter_form'=>$filter_form->createView() ));
    }

    /**
     * @Route("/list/search")
     * @Method({"GET","POST"})
     */
    public function listSearchAction(Request $request)
    {
        $logger= $this->get('logger');
        $wcat = $request->query->get('wcat','' );
        $form = $this->createForm(new WebsiteFilterType() );
        if( $request->isMethod('post')) {
            $form->bind($request);
            if( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
                $url = $this->generateUrl('jili_frontend_cpsadvertisement_list',
                    array('q'=> $keyword, 'wcat'=> empty($wcat) ? -1: $wcat) ) ;
                return $this->redirect( $url );
            }
        }
        return $this->render('JiliFrontendBundle:CpsAdvertisement:list_search_form.html.twig', array('form'=> $form->createView( )));
    }


    /**
     * @Route("/redirect/{wid}", requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     * @Method({"GET"})
     */
    public function redirectAction(Request $request  , $wid) 
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');
        if(empty($wid) ) {
            throw $this->createNotFoundException( '没找到此商家.');
        } 
        $cps = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->findOneById($wid);

        if( empty($cps)) {
            throw $this->createNotFoundException( '没找到此商家.');
        }

        # force to login 
        if( false ===  $this->get('user_login')->checkLoginStatus()) {
            $url_current = $request->getRequestUri();
            $this->get('session')->set('referer', $url_current);
            return $this->redirect( $this->generateUrl('_login'));
        }

        $uid =  $this->get('user_login')->getLoginUserId();

        # goto the {asp}_advertisement table , fetch the redirect_url
        $ad_category = $em->getRepository('JiliApiBundle:AdCategory')
            ->findOneById($cps->getAdCategoryId());
        $asp = $ad_category->getAsp();
        $shop = $em->getRepository('JiliFrontendBundle:'.ucfirst($asp).'Advertisement')->findOneById($cps->getAdId());
        
        $uri_shop =$shop->getRedirectUrlWithUserId($uid );
        if(strlen($uri_shop) > 0 ) {
            return $this->redirect( $uri_shop);/// Response(__FUNCTION__);
        }
        return $this->forward('JiliFrontendBundle:CpsAdvertisement:list');
    }
}
