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
class AdminProjectController extends Controller
{

    public function showProjectAction(Request $request, $affiliatePartnerId = null)
    {
        $currentPage = $request->query->get('page', 1);
        $adminProjectService = $this->get('app.admin_project_service');
        $result = $adminProjectService->getProjectList($affiliatePartnerId, $currentPage, 20);
        $param = array(
            'status' => $result['status'],
            'errmsg' => $result['errmsg'],
            'affiliatePartnerId' => $affiliatePartnerId,
            'pagination' => $result['pagination']
            );
        return $this->render('AffiliateAppBundle:admin:project.html.twig', $param);
    }


    public function addProjectAction(Request $request, $affiliatePartnerId = null)
    {
        $builder = $this->createFormBuilder();
        $builder->add('RFQId', 'text', array('label' => 'RFQId:'));
        $builder->add('urlFile', 'file', array('label' => 'Csv File with ukey and url. Please rename this file as RFQId_linenumber_YYYYMMDD_hms.txt before upload.'));
        $form = $builder->getForm();

        $uploadDir = $this->container->getParameter('affiliate.url_upload_directory');

        $errmsg = '';
        // Check if we are posting stuff
        if ($request->getMethod('post') == 'POST') {
            // Bind request to the form
            $form->bind($request);

            // If form is valid
            if ($form->isValid()) {
                // Get fields
                $fieldFile = $form->get('urlFile');
                $fieldRFQId = $form->get('RFQId');

                $uploadedFile = $fieldFile->getData();
                $RFQId = $fieldRFQId->getData();

                $originalFileName = $uploadedFile->getClientOriginalName();
                $realUploadName = $originalFileName . "." . md5(uniqid());
                $uploadedFile->move($uploadDir, $realUploadName);
                $fullPath = $uploadDir . "/" . $realUploadName;

                $adminProjectService = $this->get('app.admin_project_service');
                // 改partnerId
                $rtn = $adminProjectService->initProject($affiliatePartnerId, $RFQId, $originalFileName, $fullPath);
                

                //print 'Max memory usage=' . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB' . '<br>';
                // print 'status=' . $rtn['status'] . '<br>';
                // print 'errmsg' . $rtn['errmsg'] . '<br>';
                if('success' == $rtn['status']){
                    return $this->redirect($this->generateUrl('admin_project_show', array('affiliatePartnerId' => $affiliatePartnerId)));
                } else {
                    $errmsg = $rtn['msg'];
                }
            }

         }

        return $this->render('AffiliateAppBundle:admin:projectAdd.html.twig',
            array('form' => $form->createView(),
                'affiliatePartnerId' => $affiliatePartnerId,
                'errmsg' => $errmsg
                )
        );
    }


    public function closeProjectAction(Request $request, $affiliatePartnerId = null, $affiliateProjectId = null)
    {
        $currentPage = $request->query->get('page', 1);

        $param = array();
        $adminProjectService = $this->get('app.admin_project_service');
        $rtn = $adminProjectService->closeProject($affiliateProjectId);

        if($rtn['status'] == 'success'){
            $result = $adminProjectService->getProjectList($affiliatePartnerId, $currentPage, 20);
            $param = array(
                'status' => $result['status'],
                'errmsg' => $result['errmsg'],
                'affiliatePartnerId' => $affiliatePartnerId,
                'pagination' => $result['pagination']
                );
        } else {
            $param = array(
                'status' => $rtn['status'],
                'errmsg' => $rtn['msg'],
                'affiliatePartnerId' => $affiliatePartnerId,
                'pagination' => array()
                );
        }
        return $this->render('AffiliateAppBundle:admin:project.html.twig', $param);
    }
}
