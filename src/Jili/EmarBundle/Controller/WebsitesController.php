<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Form\Type\SearchType;

use Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;
/**
 * @Route("/websites")
 */
class WebsitesController extends Controller
{
    /**
     * @Route("/search")
     * @Template();
     */
    public function searchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        $form = $this->createForm(new SearchType() );
        if( $request->isMethod('post')) {

            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();

                $keyword = $query_params['keyword'];

                #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $query_params ,true) );
                $url = $this->generateUrl('jili_emar_websites_result') .'?'. http_build_query( array('q'=> $keyword ) ) ;
                #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export($url, true) );
                return $this->redirect( $url );
            }
        }
        return  array('form'=> $form->createView());
    }


    /**
     * @Route("/result")
     * @Template();
     */
    public function resultAction()
    {

        $request = $this->get('request');
        $logger= $this->get('logger');

        $keyword = $request->query->get('q', '');
        $page_no = $request->query->get('p',1);

        $websites = array();

        $web_raw  = $this->get('website.list_get')->fetch( );
        if( strlen(trim($keyword)) > 0) {
            $websites = $this->get('website.search')->find( $web_raw, $keyword );
        } else {
            $websites = $web_raw;
        }

        $total =  count($websites);

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $websites, true)  );

        $form = $this->createForm(new SearchType(), array('keyword'=>$keyword)  );
        $page_size = $this->container->getParameter('emar_com.page_size');
        $websites_paged = array_slice($websites, ( $page_no -1 ) * $page_size + 1 , $page_size      );
        return  array('form'=> $form->createView(), 'websites'=> $websites_paged, 'total'=> $total);
    }
}
