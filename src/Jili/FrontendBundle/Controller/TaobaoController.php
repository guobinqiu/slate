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
            $visit = $em->getRepository('JiliFrontendBundle:UserTaobaoVisit')->getTaobaoVisit($user_id, $day);
            if (empty ($visit)) {
                $visit = new UserTaobaoVisit();
                $visit->setUserId($user_id);
                $visit->setVisitDate($day);
                $em->persist($visit);
                $em->flush();
            }
        } else {
            if ( $this->getRequest()->query->has('l') ) {
                $this->get('session')->set('goToUrl',
                    $this->get('router')->generate('jili_frontend_taobao_index', array('l'=> $this->getRequest()->query->get('l'))));

                return $this->redirect($this->generateUrl('_user_login'));
            }
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

    /**
     * @Route("/test")
     * @Template
     */
    public function testAction() {

        $pid = 'mm_49376465_4372428_26502700';

        $user_id = $this->get('request')->getSession()->get('uid');

        $arr['search'] = '<a data-type="6" data-tmpl="573x66" data-tmplid="140" data-style="2" data-border="0" biz-s_logo="1" biz-s_hot="1" href="#"></a>';
        $arr['item_a'] = '<a data-type="0" biz-itemid="38484921637" data-tmpl="230x312" data-tmplid="4" data-rd="2" data-style="2" data-border="1" href="http://detail.tmall.com/item.htm?id=38484921637&ali_trackid=2:mm_49376465_4372428_25658343:1421143797_310_1532929028&clk1=8ca11a415ad425d504db03a5d5938c19&spm=0.0.0.0.flh1JC#app_pvid=200_10.103.30.20_371_1421143794264">http://detail.tmall.com/item.htm?id=38484921637&ali_trackid=2:mm_49376465_4372428_25658343:1421143797_310_1532929028&clk1=8ca11a415ad425d504db03a5d5938c19&spm=0.0.0.0.flh1JC#app_pvid=200_10.103.30.20_371_1421143794264</a>';
        $arr['item_b'] = '<a data-type="0" biz-itemid="40999740482" data-tmpl="230x312" data-tmplid="4" data-rd="2" data-style="2" data-border="1" href="http://detail.tmall.com/item.htm?id=40999740482&ali_trackid=2:mm_49376465_4372428_25658343:1421143846_310_969157030&clk1=661c16dffd72bbac9e1921b85f31f9f2&spm=0.0.0.0.0YBRCI#app_pvid=200_10.237.10.115_149350_1421143844128">http://detail.tmall.com/item.htm?id=40999740482&ali_trackid=2:mm_49376465_4372428_25658343:1421143846_310_969157030&clk1=661c16dffd72bbac9e1921b85f31f9f2&spm=0.0.0.0.0YBRCI#app_pvid=200_10.237.10.115_149350_1421143844128</a>';

        $arr['user_id'] = $user_id;
        return $this->render('JiliFrontendBundle:Taobao:test.html.twig', $arr);
    }

}
