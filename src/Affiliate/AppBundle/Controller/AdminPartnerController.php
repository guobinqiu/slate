<?php
namespace Affiliate\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



/**
 * 问卷代理 的 管理页面
 * 1, show current partner / add parnter
 * 2, show current projects / open/close project
 * 3, upload urls
 */
class AdminPartnerController extends Controller
{

    public function indexAction(Request $request)
    {
        return $this->render('AffiliateAppBundle:admin:index.html.twig');
    }

    public function showParnterAction(Request $request)
    {
        $currentPage = $request->query->get('page', 1);
        $adminPartnerService = $this->get('app.admin_partner_service');
        $pagination = $adminPartnerService->getPartnerList($currentPage, 10);

        return $this->render('AffiliateAppBundle:admin:partner.html.twig', 
            array(
                'pagination' => $pagination
                ));
    }

    public function addParnterAction(Request $request)
    {
        $builder = $this->createFormBuilder();
        $builder->add('name', 'text', array('label' => 'PartnerName:'));
        $builder->add('description', 'textarea', array('label' => 'Description:'));
        $form = $builder->getForm();
        
        $addResult = '';
        $errmsg = '';
        if ($request->getMethod() == 'POST') {
            // Bind request to the form
            $form->bind($request);

            // If form is valid
            if ($form->isValid()) {
                // Get fields
                $fieldName = $form->get('name');
                $fieldDescription = $form->get('description');

                $name = $fieldName->getData();
                $description = $fieldDescription->getData();
                
                $adminPartnerService = $this->get('app.admin_partner_service');
                $rtn = $adminPartnerService->addPartner($name, $description);
                //$builder->add('name', 'text', array('label' => 'PartnerName:'));
                $addResult = $rtn['status'];
                if('success' == $addResult){
                    return $this->redirect($this->generateUrl('admin_partner_show'));
                } else {
                    $errmsg = $rtn['errmsg'];
                }
            }

        }
        return $this->render('AffiliateAppBundle:admin:partnerAdd.html.twig', 
            array(
                'form' => $form->createView(),
                'addResult' => $addResult,
                'errmsg' => $errmsg
                )
            );
    }

}
