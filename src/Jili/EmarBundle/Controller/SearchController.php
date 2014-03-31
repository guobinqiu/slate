<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Form\Type\SearchGeneralType;

/**
 * @Route("/search")
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
    public function formAction( $qs = array(), $rt = 0 ) {

        $request = $this->get('request');
        $logger= $this->get('logger');

            $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $qs, true));
            $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $rt, true));

        if( $request->isMethod('POST')) {
            $form = $this->createForm(new SearchGeneralType());
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = ( isset( $query_params['q']) ) ?  $query_params['q'] : null ;
                $router=$query_params['rt']; 
                unset($query_params['rt']);
                //todo: move this to config
                if( $router == 0 ) {
                    $url = $this->generateUrl('jili_emar_product_search');
                } else if( $router == 1 ) {
                    $url = $this->generateUrl('jili_emar_websites_shopsearch');
                }

                $query = array_merge( $request->query->all(), $query_params );
                return $this->redirect( $url .'?'.http_build_query( $query));
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

            $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $keyword, true));
            $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $request->query->all()  , true));

            $form = $this->createForm(new SearchGeneralType(), array('q'=> $keyword, 'rt'=> (int) $rt ) ); 
        }
        return   array('form'=> $form->createView() ,'qs' => $qs, 'rt'=> $rt ) ;
    }
}
