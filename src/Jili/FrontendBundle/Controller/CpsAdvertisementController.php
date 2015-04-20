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
     *
     * @Route("/detail/{wid}", requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     * Route("/detail/{wid}" requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     * todo: added pageno for recommend
     */
    public function detailAction(Request $request ,$wid)
    {
        $em = $this->getDoctrine()->getManager();

        $logger = $this->get('logger');

        if(empty($wid) ) {
            throw $this->createNotFoundException( '没找到此商家.1');
        } 

        $em = $this->getDoctrine()->getManager();

        $cps = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->findOneById($wid);

        if( empty($cps)) {
            throw $this->createNotFoundException('没找到此商家.');
        }

        $ad_category = $em->getRepository('JiliApiBundle:AdCategory')
            ->findOneById($cps->getAdCategoryId());
        $asp = $ad_category->getAsp();

        // check class exits:
        $commission_list  = $em->getRepository('JiliFrontendBundle:'.ucfirst($asp).'Commission')->findListByAdId($cps->getAdId());

        $same_cat_websites = array();

        $same_cat_websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')->findSameCatWebsitesByRandom( array( 'limit'=> 3, 'category'=> $cps->getWebsiteCategory() ) );
        var_dump($same_cat_websites);

        $data = array('website'=> $cps ,
            'commission_list'=>$commission_list,
            'same_cat_websites' => $same_cat_websites);
        // use template on asp
        // detail_emar.html.twig
        // detail_chanet.html.twig
        // detail_duomai.html.twig
        return $this->render('JiliFrontendBundle:CpsAdvertisement:detail.html.twig', $data);
    }

    /**
     * @abstract: 会将本地配置的商家排列在前面。
     * @Route("/list")
     * @Method( {"GET","POST"})
     */
    public function listAction(Request $request)
    {

        # filter by search keyword
        # filter by search category 
        # filter by index key 
        
        # the hot websites category 
        # pagination.
       
        $request = $this->get('request');
        $logger= $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $wcat = (int) $request->query->get('wcat', '' );
        
        $logger->debug('[jarod]'.implode(',',array(__LINE__,__FUNCTION__,'$wcat: ')). var_export($wcat, true));

        //todo: wcats with local file cache
        $wcats = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->fetchCategoryList();
        
        // webs
        $websites = array();
        $params =array();

        if( isset($wcat) && strlen( $wcat) > 0 ) {

        } else {

        }

        # ??
        $websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')->findAll();

        // page_size , page_no
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

        $logger->debug('[jarod]'.implode(',',array(__LINE__,__FUNCTION__)). var_export($websites_paged, true));

        # search form 
        $keyword = $request->query->get('q', '');
        $keyword = trim($keyword);
        if( strlen($keyword) > 0 ) {

        }

        $filter_form = $this->createForm(new WebsiteFilterType(), array('keyword'=>$keyword)  );


        return $this->render('JiliFrontendBundle:CpsAdvertisement:list.html.twig',
            array('categories'=> $wcats,
            'websites'=> $websites_paged,
            'total'=> $total,
            'filter_form'=>$filter_form->createView(),
            'letters'=>range('A','Z')));
    }

    /**
     * 
     * @Route("/list/search")
     * @Method({"GET","POST"})
     */
    public function listSearchAction(Request $request)
    {
        $logger= $this->get('logger');

        $wcat_id = $request->query->get('wcat' );

        $form = $this->createForm(new WebsiteFilterType() );
        if( $request->isMethod('post')) {
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
                $parameters = array('q'=> $keyword );
                if( isset($wcat_id ) && is_numeric($wcat_id) && $wcat_id > 0 ) {
                    $parameters ['wcat']= $wcat_id ;
                }
                $url = $this->generateUrl('jili_frontend_cpsadvertisement_list') .'?'. http_build_query( $parameters ) ;
                return $this->redirect( $url );
            }
        }

        return $this->render('JiliFrontendBundle:CpsAdvertisement:list_search_form.html.twig', array('form'=> $form->createView( )));
    }


    /**
     * @Route("/redirect")
     * @Method({"GET","POST"})
     */
    public function redirectAction(Request $request) 
    {

        return new Response(__FUNCTION__);
    }
}
