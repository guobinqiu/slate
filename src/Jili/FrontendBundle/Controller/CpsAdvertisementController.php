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

        $shop = $em->getRepository('JiliFrontendBundle:'.ucfirst($asp).'Advertisement')->findOneById($cps->getAdId());

        $commission_list  = $em->getRepository('JiliFrontendBundle:'.ucfirst($asp).'Commission')->findListByAdId($shop->getAdsId());

        $logger->debug('[jarod]'.implode(',',array(__LINE__,__FUNCTION__,'getWebsiteCategory: ')). var_export($cps->getWebsiteCategory() , true));
        $same_cat_websites = array();

        $same_cat_websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->findSameCatWebsitesByRandom( array( 'limit'=> 3, 'category'=> $cps->getWebsiteCategory() ) );

        # TODO: include asp  partial  to render the differece details.
        
        $data = array('website'=> $cps ,
            'detail' => $shop, 
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

        $keyword = $request->query->get('q', '');
        $keyword = trim($keyword);
        $filter_form = $this->createForm(new WebsiteFilterType(), array('keyword'=>$keyword)  );

        #TODO: wcats with local file cache
        $wcats = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->fetchCategoryList();
        
        // webs
        $websites = array();

        $dic_key = $request->query->get('t','');
        if( ! empty($dic_key) && strlen($dic_key) ===  1  ){
            $websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')->fetchByWebsiteNameDictionaryKey($dic_key);
        } else {
            $wcat =  $request->query->get('wcat', '' );
            $params = array(/* 'dic_key'=> $dic_key , */ 'keyword' => $keyword, 'wcat'=> $wcat);
            $websites = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')->fetchByKeywordsAndCategory($params);
        }

        $logger->debug('[jarod]'.implode(',',array(__LINE__,__FUNCTION__,'$websites: ')). var_export($websites, true));

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
        $wcat = $request->query->get('wcat','' );
        $form = $this->createForm(new WebsiteFilterType() );
        if( $request->isMethod('post')) {
            $form->bind($request);
            if( $form->isValid()) {

                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
                $parameters = array('q'=> $keyword );

                if( empty($wcat) ) {
                    $parameters ['wcat']=  -1;
                } else {
                    $parameters ['wcat']=  $wcat;
                }

                $url = $this->generateUrl('jili_frontend_cpsadvertisement_list', $parameters ) ;
                $logger->debug('[jarod]'.implode(',',array(__LINE__,__FUNCTION__,'$wcat: ')). var_export($url, true));

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

        $logger= $this->get('logger');

        $logger->debug('[jarod]'.implode(',',array(__LINE__,__FUNCTION__)). var_export($request, true));
        return new Response(__FUNCTION__);
    }
}
