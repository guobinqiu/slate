<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Form\Type\SearchGeneralType;

/**
 * @Route("/search", requirements={"_scheme" = "http"})
 * @Method({"GET", "POST"})
 * @Template()
 */
class SearchController extends Controller
{
    /**
     * @param  qs is the query string , $rt is the router flag
     * @Route("/form")
     * @Template()
     */
    public function formAction( $qs = array(), $rt = 0 )
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        if( $request->isMethod('POST')) {
            $form = $this->createForm(new SearchGeneralType());
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = ( isset( $query_params['q']) ) ?  $query_params['q'] : null ;

                $rt = $query_params['rt'];
                unset($query_params['rt']);
                $rt_config_router = $this->container->getParameter('emar_com.rt_of_search_form.router');
                $rt_key  = array_key_exists( $rt, $rt_config_router ) ? $rt:0 ;
                $router_ = $rt_config_router[ $rt_key];
                $url = $this->generateUrl( $router_ );
                return $this->redirect( $url .'?'.http_build_query( $query_params));
            }
        } else {
            //notice: the keyword must not be empty !
            $keyword = $request->query->get('q', '');
            if(empty($keyword)){
                extract($qs);
                if( isset($q) ) {
                    $keyword = $q;
                }
            }


            $form = $this->createForm(new SearchGeneralType(), array('q'=> $keyword, 'rt'=> (int) $rt ) );
        }
        return   array('search_form'=> $form->createView() ,'qs' => $qs, 'rt'=> $rt ) ;
    }
}
