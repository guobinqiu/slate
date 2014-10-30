<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\FrontendBundle\Entity\UserTaobaoVisit;
//use Jili\FrontendBundle\Entity\TaobaoCategory;
//use Jili\FrontendBundle\Entity\TaobaoComponent;
//use Jili\FrontendBundle\Entity\TaobaoRecommend;

/**
 * @Route("/taobao",requirements={"_scheme"="http"})
 */
class TaobaoController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction() {
        //check login, if user don't login, redirect login page
        if (!$this->get('request')->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('jili_frontend_taobao_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // save user taobao visit
        $day = date('Ymd');
        $user_id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        if ($user_id) {
            $visit = $em->getRepository('JiliFrontendBundle:UserTaobaoVisit')->getTaobaoVisit($user_id, $day);
            if (empty ($visit)) {
                $visit = new UserTaobaoVisit();
                $visit->setUserId($user_id);
                $visit->setVisitDate($day);
                $em->persist($visit);
                $em->flush();
            }
        }

        // get taobao category
        $category = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findAll();

        $arr['category'] = $category;
        $arr['current_id'] = 1;
        $arr['page'] = 2;
        return $this->render('JiliFrontendBundle:Taobao:index.html.twig',$arr);
    }

    /**
     * @Route("/searchBox")
     * @Template
     */
    public function searchBoxAction() {
        //check login, if user don't login, redirect login page
        if (!$this->get('request')->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('jili_frontend_taobao_searchbox'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get search box component code
        $em = $this->getDoctrine()->getManager();
        $search_box = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['search_box']);
        $arr['search_box'] = $search_box[0];

        return $this->render('JiliFrontendBundle:Taobao:searchBox.html.twig', $arr);
    }

    /**
     * @Route("/categoryApi", options={"expose"=true})
     */
    public function categoryApiAction() {
        $request = $this->get('request');
        $id = $request->query->get('id', 1);
        $page = $request->query->get('page', 1);

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao component by category id
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByCategory($id, $taobao_component['keyword'],$page);

        $arr['current_id'] = $id;
        $arr['keywords'] = $keywords;
        $arr['page'] = $page+1;
        return new Response(json_encode($arr));
    }

    /**
     * @Route("/item")
     * @Template
     */
    public function itemAction() {
        //check login, if user don't login, redirect login page
        if (!$this->get('request')->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('jili_frontend_taobao_item'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao item component
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['item']);

        $arr['items'] = $items;
        return $this->render('JiliFrontendBundle:Taobao:item.html.twig', $arr);
    }

    /**
     * @Route("/shop")
     * @Template
     */
    public function shopAction() {
        //check login, if user don't login, redirect login page
        if (!$this->get('request')->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('jili_frontend_taobao_shop'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao item component
        $em = $this->getDoctrine()->getManager();
        $shops = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['shop']);

        $arr['shops'] = $shops;
        return $this->render('JiliFrontendBundle:Taobao:shop.html.twig', $arr);
    }

}