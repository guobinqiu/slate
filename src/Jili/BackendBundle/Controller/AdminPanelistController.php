<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpKernel\HttpKernelInterface;
// use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Jili\BackendBundle\Form\PanelistSearchType;
use Jili\ApiBundle\Utility\String;

/**
 * @Route("/admin/panelist",requirements={"_scheme"="https"})
 */
class AdminPanelistController extends Controller implements IpAuthenticatedController
{

    /**
     * @Route("/search",  name="_admin_panelist_index")
     */
    public function searchAction(Request $request)
    {

        $page = $request->request->get('page', 1);
        if(!$page){
           $page = $request->query->get('page', 1);
        }

        $pageSize = 2;
        $em = $this->getDoctrine()->getManager();

        //create vote form
        $form = $this->createForm(new PanelistSearchType());
        $pagination = null;
        $registeredCount = 0;
        $withdrawalCount = 0;

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $values = $form->getData();

                $sop = $this->container->getParameter('sop');

                $paginator = $this->get('knp_paginator');

                if ($values['type_registered'] == 1) {
                    $registeredCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'registered');
                    $registeredSql = $em->getRepository('JiliApiBundle:User')->getSearchUserSql($values, 'registered');
                    $registeredQuery = $em->createQuery($registeredSql)->setHint('knp_paginator.count', $registeredCount);
                    $arr['pagination_registered'] = $paginator->paginate($registeredQuery, $page, $pageSize, array (
                        'distinct' => false
                    ));

                    echo "<br>line_".__LINE__."_aaaaaaaaaa<pre>";
                    print_r($arr['pagination_registered']);
                    $arr['pagination_registered']->setTemplate('JiliApiBundle::pagination.html.twig');
                }

                if ($values['type_withdrawal'] == 1) {
                    $withdrawalCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'withdrawal');
                    $withdrawalSql = $em->getRepository('JiliApiBundle:User')->getSearchUserSql($values, 'withdrawal', $withdrawalCount);
                    $withdrawalQuery = $em->createQuery($withdrawalSql)->setHint('knp_paginator.count', $withdrawalCount);
                    $arr['pagination_withdrawal'] = $paginator->paginate($withdrawalQuery, $page, $pageSize, array (
                        'distinct' => false
                    ));
                    $arr['pagination_withdrawal']->setTemplate('JiliApiBundle::pagination.html.twig');
                }

                $arr['sop'] = $sop;

            }
        }

        //todo: 了解一下enqueteHistory, PartnerPublicationPanelistManager


        $arr['page'] = $page;
        $arr['form'] = $form->createView();
        $arr['registeredCount'] = $registeredCount;
        $arr['withdrawalCount'] = $withdrawalCount;

        return $this->render('JiliBackendBundle:Panelist:search.html.twig', $arr);
    }

    /**
     * @Route("/edit", name="_admin_panelist_edit")
     */
    public function editAction(Request $request)
    {
    }
}
