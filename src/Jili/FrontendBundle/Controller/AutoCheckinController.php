<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;



/**
 * @Route("/auto-checkin")
 */
class AutoCheckinController extends Controller {
    /**
     * @Route("/redirectme")
     */
    public function redirectmeAction(){
        $logger = $this->get('logger');
        $request = $this->get('request');
        if($request->get('36kr')) {
            return $this->redirect('http://www.36kr.com/');
        } elseif ($request->get('z')) {
            return $this->redirect('http://www.amazon.cn/ref=z_cn?tag=zcn0e-23');
        }
        return $this->redirect('http://www.baidu.com');

    }

    /**
     * @Route("/process")
     * @Template()
     */
    public function processAction()
    {
        return array();
    }

}
