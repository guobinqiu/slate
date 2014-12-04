<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\FrontendBundle\Entity\UserTaobaoVisit;
use Jili\FrontendBundle\Entity\TaobaoComponent;

/**
 * @Route("/taobao",requirements={"_scheme"="http"})
 */
class TaobaoController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction() {
        // save user taobao visit
        $day = date('Ymd');

        $user_id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');
        if ($user_id) {
            $logger->debug('{jarod}'.__LINE__);
            $visit = $em->getRepository('JiliFrontendBundle:UserTaobaoVisit')->getTaobaoVisit($user_id, $day);
            if (empty ($visit)) {
                $visit = new UserTaobaoVisit();
                $visit->setUserId($user_id);
                $visit->setVisitDate($day);
                $em->persist($visit);
                $em->flush();
            }
        } else if ( $this->getRequest()->query->has('l') ) {
            $logger->debug('{jarod}'.__LINE__.$this->generateUrl('_user_login'));
            $this->get('session')->set('goToUrl', $this->get('router')->generate('jili_frontend_taobao_index', array('l'=> $this->getRequest()->query->get('l'))));

            return $this->redirect($this->generateUrl('_user_login'));
        } else {
            $logger->debug('{jarod}'.__LINE__);
        }

        // get taobao category
        $delete_flag = $this->container->getParameter('delete_flag_false');
        $categorys = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findCategorys($delete_flag);

        $arr['categorys'] = $categorys;
        $arr['current_id'] = 1;
        $arr['page'] = 2;

        return $this->render('JiliFrontendBundle:Taobao:index.html.twig', $arr);
    }

    /**
     * @Route("/searchBox")
     * @Template
     */
    public function searchBoxAction() {
        // get search box component code
        $em = $this->getDoctrine()->getManager();
        $search_box = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneByComponentId(TaobaoComponent :: TAOBAO_COMPONENT_SEARCH_BOX);
        $arr['search_box'] = $search_box;

        return $this->render('JiliFrontendBundle:Taobao:searchBox.html.twig', $arr);
    }

    /**
     * @Route("/categoryApi", options={"expose"=true})
     */
    public function categoryApiAction() {
        $request = $this->get('request');
        $id = $request->query->get('id', 1);
        $page = $request->query->get('page', 1);

        // get taobao component by category id and component id
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponents($id, TaobaoComponent :: TAOBAO_COMPONENT_KEYWORD, $page);

        $arr['current_id'] = $id;
        $arr['keywords'] = $keywords;
        $arr['page'] = $page +1;

        $response = new JsonResponse();
        $response->setData($arr);
        return $response;
    }

    /**
     * @Route("/item")
     * @Template
     */
    public function itemAction() {
        // get taobao item component
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId(TaobaoComponent :: TAOBAO_COMPONENT_ITEM);

        $arr['items'] = $items;
        return $this->render('JiliFrontendBundle:Taobao:item.html.twig', $arr);
    }

    /**
     * @Route("/shop")
     * @Template
     */
    public function shopAction() {
        // get taobao shop component
        $em = $this->getDoctrine()->getManager();
        $shops = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId(TaobaoComponent :: TAOBAO_COMPONENT_SHOP);

        $arr['shops'] = $shops;
        return $this->render('JiliFrontendBundle:Taobao:shop.html.twig', $arr);
    }

}
