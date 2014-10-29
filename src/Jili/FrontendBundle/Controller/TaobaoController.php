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
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        // save user taobao visit
        $day = date('Ymd');
        $user_id = $request->getSession()->get('uid');
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
        $em = $this->getDoctrine()->getManager();

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get search box component code
        $search_box = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByComponentId($taobao_component['search_box']);
        $arr['search_box'] = $search_box[0];
//        $arr['search_box'] = 1;

        return $this->render('JiliFrontendBundle:Taobao:searchBox.html.twig', $arr);
    }

    /**
     * @Route("/category/{current_id}")
     * @Template
     */
    public function categoryAction($current_id = 1) {
        $em = $this->getDoctrine()->getManager();

        // get taobao component id
        $taobao_component = $this->container->getParameter('taobao_component');

        // get taobao category
        $category = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findAll();

        // get taobao component by category id
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findByCategory($current_id,$taobao_component['keyword']);

        $arr['category'] = $category;
        $arr['current_id'] = $current_id;
        $arr['keywords'] = $keywords;
        return $this->render('JiliFrontendBundle:Taobao:category.html.twig',$arr);
    }

    /**
     * @Route("/item")
     * @Template
     */
    public function itemAction() {
        return $this->render('JiliFrontendBundle:Taobao:item.html.twig');
    }

    /**
     * @Route("/shop")
     * @Template
     */
    public function shopAction() {
        return $this->render('JiliFrontendBundle:Taobao:shop.html.twig');
    }

}