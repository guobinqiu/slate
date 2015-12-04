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
        $pageSize = 50;
        $em = $this->getDoctrine()->getManager();

        //create vote form
        $form = $this->createForm(new PanelistSearchType());
        $pagination = null;
        $total_count = 0;

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $values = $form->getData();

                $sop = $this->container->getParameter('sop');

                $paginator = $this->get('knp_paginator');

                if ($values['type_registered'] == 1) {
                    $registeredList = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'registered');
                    $arr['pagination_registered'] = $paginator->paginate($registeredList, $page, $pageSize);
                    $arr['pagination_registered']->setTemplate('JiliApiBundle::pagination.html.twig');

                    if ($arr['pagination_registered']->getTotalItemCount() > 0) {
                        $pagination = $arr['pagination_registered'];
                        $total_count = $arr['pagination_registered']->getTotalItemCount();
                    }
                }
                if ($values['type_withdrawal'] == 1) {
                    $withdrawalList = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'withdrawal');

                    $arr['pagination_withdrawal'] = $paginator->paginate($withdrawalList, $page, $pageSize);
                    $arr['pagination_withdrawal']->setTemplate('JiliApiBundle::pagination.html.twig');

                    if ($arr['pagination_withdrawal']->getTotalItemCount() > $total_count || ($total_count == 0 && $arr['pagination_withdrawal']->getTotalItemCount() > 0)) {
                        $pagination = $arr['pagination_withdrawal'];
                    }
                }
                $arr['form'] = $form->createView();
                $arr['sop'] = $sop;
                $arr['pagination'] = $pagination;
                $arr['total_count'] = $total_count;
                return $this->render('JiliBackendBundle:Panelist:search.html.twig', $arr);
            }
        }

        //todo: 了解一下enqueteHistory, PartnerPublicationPanelistManager


        return $this->render('JiliBackendBundle:Panelist:search.html.twig', array (
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/edit", name="_admin_panelist_edit")
     */
    public function editAction(Request $request)
    {

    }
}
