<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ComponentController extends Controller
{

    /**
     * @Route("/headerNav", name="_component_headernav")
     */
    public function headerNavAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $id = $request->getSession()->get('uid');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($id);

        return $this->render('WenwenFrontendBundle:Components:_headerNav.html.twig', array (
            'user' => $user
        ));
    }

    /**
     * @Route("/homeBar", name="_component_home_bar")
     */
    public function homeBarAction(Request $request)
    {
        $id = $request->getSession()->get('uid');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($id);

        return $this->render('WenwenFrontendBundle:Components:_homeBar.html.twig', array (
            'user' => $user
        ));
    }
}
