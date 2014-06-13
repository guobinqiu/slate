<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Jili\EmarBundle\EventListener\ValidationException,
    Jili\EmarBundle\EventListener\ProcessException;

/**
 * @Route("/api",requirements={"_scheme":"http"})
 */
class ApiController extends Controller
{
    /**
     * @Route("/callback")
     */
    public function callbackAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');
        $config = $this->container->getParameter('emar');
        $config_of_return_codes = $config['callback_return_code']; 
        try {
            // logger  
            $this->get('emar_api.logger')->log( $request->getRequestUri()  );
            // validation
            $result_of_validation = $this->get('emar_api.callback_validation')->validate($request);
            if( $result_of_validation['value'] === true ) {
                // preProcess decoding the parameters 
                $request->query->set('create_date', urldecode($request->query->get('create_date')) );//'2011-09-19+18%3A21%3A18',
                $request->query->set('action_name', mb_convert_encoding(urldecode($request->query->get('action_name')), 'utf8',  'gb2312') );
                $request->query->set('order_time', urldecode($request->query->get('order_time')) );//'2011-09-19+18%3A21%3A18',
                $request->query->set('prod_name', mb_convert_encoding(urldecode($request->query->get('prod_name')), 'utf8', 'gb2312')) ;//'2011-09-19+18%3A21%3A18',
                // process
                $result_of_process =$this->get('emar_api.callback_processor')->process($request , $result_of_validation['data'] );
                if( isset($result_of_process['code']) ) {
                    $result_to_return =  $result_of_process['code'];
                }  else {
                    $result_to_return= $config_of_return_codes['exception'];
                }
            } else {
                $result_to_return =  $result_of_validation['code'];
            }
        } catch ( ValidationException $e) {
            $result_to_return= $config_of_return_codes['exception'];
        } catch ( ProccessException $e) {
            $result_to_return= $config_of_return_codes['exception'];
        } catch (\Exception $e) {
            $result_to_return= $config_of_return_codes['exception']; 
        }
        $response = new Response( $result_to_return );
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
