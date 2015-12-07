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
        if (!$page) {
            $page = $request->query->get('page', 1);
        }

        $pageSize = 1;
        $arr['sop'] = $this->container->getParameter('sop');
        $em = $this->getDoctrine()->getManager();

        //create vote form
        $form = $this->createForm(new PanelistSearchType());
        $pagination = null;
        $registeredCount = 0;
        $withdrawalCount = 0;
        $pageCount = 0;
        $pages = 0;

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $values = $form->getData();

                if ($values['type_registered'] == 1) {
                    $registeredCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'registered');
                    $arr['registeredUserList'] = $em->getRepository('JiliApiBundle:User')->getSearchUserSql($values, 'registered', $pageSize, $page);
                }

                if ($values['type_withdrawal'] == 1) {
                    $withdrawalCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'withdrawal');
                    $arr['withdrawalUserList'] = $em->getRepository('JiliApiBundle:User')->getSearchUserSql($values, 'withdrawal', $pageSize, $page);
                }
            }
        }

        //todo: 了解一下enqueteHistory, PartnerPublicationPanelistManager


        if ($registeredCount > $withdrawalCount) {
            $arr['total'] = $registeredCount;
        } else {
            $arr['total'] = $withdrawalCount;
        }

        $arr['page'] = $page;
        $arr['page_size'] = $pageSize;
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
