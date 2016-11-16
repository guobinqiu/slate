<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AdvertisermentController extends Controller
{

    /**
	 * @Route("/list", name="_advertiserment_list")
	 */
    public function listAction()
    {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('_advertiserment_list') );
            return  $this->redirect($this->generateUrl('_user_login'));
        }

        return $this->render('WenwenFrontendBundle:Advertisement:index.html.twig');
    }

    /**
     * @Route("/offer99", name="_advertiserment_offer99")
     */
    public function offer99Action()
    {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('_advertiserment_offer99') );
            return  $this->redirect($this->generateUrl('_user_login'));
        }
        return $this->render('WenwenFrontendBundle:Advertisement:offer99.html.twig');
    }

}
