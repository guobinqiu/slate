<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Jili\EmarBundle\Form\Type\SearchType;
use Jili\EmarBundle\Api2\Utils\YiqifaOpen;
use Jili\EmarBundle\Api2\Request\ProductSearchGetRequest;

// todo: login required

/**
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * @Route("/index")
     * @Template();
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        $form = $this->createForm(new SearchType() );
        if( $request->isMethod('POST')){
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
            
//  
//                 $emar_config = $this->container->getParameter('emar');
//                 $app_key  = $emar_config['AppKey'];
//                 $app_secret  = $emar_config['AppSecret'];
//                 $c = new YiqifaOpen($app_key, $app_secret) ;
//                 $c->format="json";
// 
//                 $req = new ProductSearchGetRequest();
//                 $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
//                 $req->setKeyword(mb_convert_encoding($keyword,'gb2312', 'utf8'));
//                 $req->setPage_no(1);
//                 $req->setPage_size(30);
//                 $req->setCatid('');
//                 $req->setWebid('');
//                 #$req->setPrice_range('50,300');
//                 $req->setOrderby(1);
// 
//                 try {
//                   $resp = $c->execute($req);
//                   //todo:  added $resp  to cache 
//                   
//                 } catch( \Exception $e ) {
//                   $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).$e->getMessage() );
//                 }
// 
// //
                $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $query_params ,true) );

                $url = $this->generateUrl('jili_emar_search_result') .'?'. http_build_query( array('q'=> $keyword ) ) ;

                $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export($url, true) );
                return $this->redirect( $url );
            }
        } else {

        }

        return  array('form'=> $form->createView());
    }


    /**
     * Route("/result/q/{keyword}/p/{page}/o/{order}/pr/{range_of_price}", defaults={ "page": 1, "order": 1, "range_of_price": "" })
     * @Route("/result")
     * @Template();
     */
    public function resultAction( )
    {
      if(!  $this->get('request')->getSession()->get('uid') ) {
        return  $this->redirect($this->generateUrl('_user_login'));
      }

        $request = $this->get('request');
        $logger= $this->get('logger');

        #$session = $this->get('session');
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $session->all() , true)  );

        $keyword = $request->query->get('q', '');
        $page = $request->query->get('p',1);
        $order = $request->query->get('o',1);
        $range_of_price = $request->query->get('pr', '');

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $keyword , true)  );
        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export(  $page, true)  );
        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export(  $order, true)  );
        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export(  $range_of_price, true)  );

        #$cn= get_class($session);
        #$cm = get_class_methods($cn);
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $cn , true)  );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $cm , true)  );
        #return new Response('a');

        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $request->request , true)  );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $request->query , true)  );

        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $keyword , true)  );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $page , true)  );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $order , true)  );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $range_of_price , true)  );


        $q = $keyword;
        $page_no = $page;

        if(strlen($q) == 0) {
          $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ) );
          return $this->redirect($this->generateUrl('jili_emar_search_index') );
        }

        $form = $this->createForm(new SearchType(), array('keyword'=>$q)  );

        $page_no = $request->query->get('p', 1);

        $page_size = 40;
        // 
        $emar_config = $this->container->getParameter('emar');
        $app_key  = $emar_config['AppKey'];
        $app_secret  = $emar_config['AppSecret'];


        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( $emar_config , true)  );
        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( $q , true)  );
        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( $page_no, true)  );
        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( $page_size, true)  );

        $c = new YiqifaOpen($app_key, $app_secret) ;
        // $c->consumerKey = "1326528120671";
        // $c->consumerSecret = "4090a94e6c98c2a3c3cf1634cdf29730";
        $c->format="json";
        $req = new ProductSearchGetRequest();
        $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
        $req->setKeyword(mb_convert_encoding($q,'gb2312', 'utf8'));
        $req->setPage_no($page_no);
        $req->setPage_size($page_size);

        $req->setCatid('');
        $req->setWebid('');

        // todo:
        // price condition
        //     * 商品的价格区间:“Price_range=最低价格,最高价格”，之间用“,”，举例:Price_range=50,300，价格不能为负数。其中“,”前的值“,”后的值，默认为空，返回所有价格的商品
        
        // order by fields 1?
        //      搜索排序: 排序标识1为按相关度排序，2为价格从低到高排序，3为价格从高到低排序,默认值为1
        #$req->setPrice_range('50,300');

        $req->setOrderby(1);
        $result_raw = $c->execute($req);
        $result_escaped = trim(str_replace("\\" ,'\\\\' ,trim($result_raw)));
        $result  = json_decode( trim($result_escaped), true);  
        if( null ===  $result  ) {
          $logger->crit('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). $result_raw );
        };

       # $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($result, true) );
        $products = array();
        $total = 0;

        if( isset( $result['response'] )) {
          $response = $result['response'];
          if( isset( $response['pdt_list'] )) {

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ) );
            $products = $response['pdt_list']['pdt'];
          }
          if( isset( $response['total'] )) {

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ) );
            $total = $response['total'];
          }
        }

        
        return array('products'=> $products,'total'=> $total, 'form'=> $form->createView() );
    }

}
