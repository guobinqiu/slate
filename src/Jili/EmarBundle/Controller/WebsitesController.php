<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Jili\EmarBundle\Form\Type\WebsiteFilterType;
use Jili\EmarBundle\Form\Type\SearchWebsiteType;

use Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

use Jili\EmarBundle\Entity\EmarActivityCommission;
use Jili\ApiBundle\Utility\RebateUtil;

/**
 * @Route("/websites", requirements={"_scheme" = "http"})
 */
class WebsitesController extends Controller
{


    /**
     * 返利商城: 从emar_websits中取数据，
     * @Route("/hot/{tmpl}/{max}", defaults={"tmpl"="top", "max" = 12 }, requirements={"tmpl"= "\w+",  "max" = "\d+"} )
     * @Method("GET")
     * @Template()
     */
    public function hotAction($tmpl, $max)
    {
        if( $tmpl != 'top' ) {
               throw $this->createNotFoundException('页面没找到');
        }
        //todo restrice the request ip ?
        $logger = $this->get('logger');

        $cache_key = $this->container->getParameter('cache_config.emar.websites_hot.key');
        $cache_fn = $cache_key. '_max.'. $max;
        $cache_duration = $this->container->getParameter('cache_config.emar.websites_hot.duration');

        $cache_proxy = $this->get('cache.file_handler');

        if($cache_proxy->isValid($cache_fn , $cache_duration) ) {
            $websites = $cache_proxy->get($cache_fn);
        }  else {
            $cache_proxy->remove( $cache_fn);

            $em = $this->getDoctrine()->getManager();
            $params = array();
            if(isset($max) && $max > 0 ) {
                $params['limit'] = $max;
            }

            // fetch the details ?
            $hot_webs_configed = $em->getRepository('JiliEmarBundle:EmarWebsites')->getHot( $params);
            $websites = array();

            if(! empty($hot_webs_configed)) {
                $webids= array();

                $commissions = array();
                foreach($hot_webs_configed as $row) {
                    $webids[] = $row[ 'webId'];
                    $commissions[ $row['webId'] ] = $row['commission'];
                }

                $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds( $webids );

                $websites = array_fill_keys( $webids, null);

                # input commissions_of_configed , $commission_of_api, $commission_of_default;
                foreach( $result as $row) {
                       $em->detach($row);
                    $web_id = $row->getWebId();
                    $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission($row->getCommission() );
                    if( array_key_exists( $web_id,  $commissions) ){
                        $multiple = $commissions[$web_id];
                    }
                    if( ! isset($multiple) ||   is_null($multiple) || $multiple == 0   ){
                        $multiple = $this->container->getParameter('emar_com.cps.action.default_rebate');
                    }
                    $row->setCommission( round($multiple * $comm /100, 2) );
                    $websites[$row->getWebId() ] = $row;
                }
            }


            $cache_proxy->set( $cache_fn, $websites );
        }



        $template ='JiliEmarBundle:Websites:'. 'hot_on_'. $tmpl. '.html.twig';

        return $this->render($template, compact('websites'));
    }



}
