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
        $maxrows = 50;
        $em = $this->getDoctrine()->getManager();

        //create vote form
        $form = $this->createForm(new PanelistSearchType());

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $values = $form->getData();

                $sop = $this->container->getParameter('sop');

                $paginator = $this->get('knp_paginator');

                if ($values['type_registered'] == 1) {
                    $registeredList = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'registered');

                    //分页显示
                    $arr['pagination_registered'] = $paginator->paginate($registeredList, $request->query->get('page', 1), $maxrows);
                    $arr['pagination_registered']->setTemplate('JiliApiBundle::pagination.html.twig');
                }
                if ($values['type_withdrawal'] == 1) {
                    $withdrawalList = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'withdrawal');
                    //分页显示
                    $arr['pagination_withdrawal'] = $paginator->paginate($withdrawalList, $request->query->get('page', 1), $maxrows);
                    $arr['pagination_withdrawal']->setTemplate('JiliApiBundle::pagination.html.twig');
                }

                $arr['form'] = $form->createView();
                $arr['sop'] = $sop;
                return $this->render('JiliBackendBundle:Panelist:search.html.twig', $arr);
            }
        }

        //todo: 了解一下enqueteHistory, PartnerPublicationPanelistManager

        return $this->render('JiliBackendBundle:Panelist:search.html.twig', array (
            'form' => $form->createView()
        ));
    }
}
