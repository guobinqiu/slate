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
        if(!$this->get('request')->getSession()->get('uid')){
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('jili_frontend_taobao_index') );
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

        return $this->render('JiliFrontendBundle:Taobao:index.html.twig');
    }

    /**
     * @Route("/searchBox")
     * @Template
     */
    public function searchBoxAction() {
        //check login, if user don't login, redirect login page
        if(!$this->get('request')->getSession()->get('uid')){
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('jili_frontend_taobao_searchbox') );
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get search box component code
        $em = $this->getDoctrine()->getManager();
        $search_box = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['search_box']);
        $arr['search_box'] = $search_box[0];
//        $arr['search_box'] = 1;

        return $this->render('JiliFrontendBundle:Taobao:searchBox.html.twig', $arr);
    }

    /**
     * @Route("/category/{id}")
     * @Template
     */
    public function categoryAction($id) {
        //check login, if user don't login, redirect login page
        if(!$this->get('request')->getSession()->get('uid')){
            $this->get('request')->getSession()->set( 'referer', $this->generateUrl('jili_frontend_taobao_category', array('id'=>1)) );
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao category
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findAll();

        // get taobao component by category id
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByCategory($id,$taobao_component['keyword']);

        $arr['category'] = $category;
        $arr['current_id'] = $id;
        $arr['keywords'] = $keywords;
        return $this->render('JiliFrontendBundle:Taobao:category.html.twig',$arr);
    }

    /**
     * @Route("/item")
     * @Template
     */
    public function itemAction() {
        //check login, if user don't login, redirect login page
        if(!$this->get('request')->getSession()->get('uid')){
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('jili_frontend_taobao_item') );
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao item component
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['item']);

        $arr['items'] = $items;
        return $this->render('JiliFrontendBundle:Taobao:item.html.twig',$arr);
    }

    /**
     * @Route("/shop")
     * @Template
     */
    public function shopAction() {
        //check login, if user don't login, redirect login page
        if(!$this->get('request')->getSession()->get('uid')){
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('jili_frontend_taobao_shop') );
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao item component
        $em = $this->getDoctrine()->getManager();
        $shops = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['shop']);

        $arr['shops'] = $shops;
        return $this->render('JiliFrontendBundle:Taobao:shop.html.twig',$arr);
    }

}