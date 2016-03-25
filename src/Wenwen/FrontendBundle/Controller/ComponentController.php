<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ComponentController extends Controller
{

    /**
     * @Route("/headerTopBar", name="_component_headertopbar")
     */
    public function headerTopBarAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $id = $request->getSession()->get('uid');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);

        return $this->render('WenwenFrontendBundle:Components:_headerTopBar.html.twig', array (
            'user' => $user
        ));
    }
}
