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
        $builder->add('RFQId', 'text', array('label' => 'RFQId:', 'trim' => true));
        $builder->add('CompletePoints', 'text', array('label' => '完成问卷后注册的额外奖励积分数:', 'data' => 0, 'trim' => true)); // default 0
        $builder->add('urlFile', 'file', array('label' => 'Csv File with ukey and url. Please rename this file as RFQId_linenumber_YYYYMMDD_hms.txt before upload.'));
        $builder->add('Province', 'text', array('label' => 'Province 输入XX省或“直辖市”,不限制输入空格:', 'data' => null, 'required' => false));
        $builder->add('City', 'text', array('label' => 'City 输入XX市,不限制输入空格:', 'data' => null, 'required' => false));

        $form = $builder->getForm();

        $uploadDir = $this->container->getParameter('affiliate.url_upload_directory');

        $errmsg = '';

        // Check if we are posting stuff
        if ($request->getMethod() == 'POST') {
            // Bind request to the form
            $form->bind($request);

            // If form is valid
            if ($form->isValid()) {
                // Get fields
                $fieldFile = $form->get('urlFile');
                $fieldRFQId = $form->get('RFQId');
                $fieldProvince = $form->get('Province');
                $fieldCity = $form->get('City');
                $fieldCompletePoints = $form->get('CompletePoints');

                $uploadedFile = $fieldFile->getData();
                $RFQId = $fieldRFQId->getData();
                $completePoints = $fieldCompletePoints->getData();
                $province = $fieldProvince->getData();
                $city = $fieldCity->getData();

                if($completePoints <= 2000) {
                    $originalFileName = $uploadedFile->getClientOriginalName();
                    $realUploadName = $originalFileName . "." . md5(uniqid());
                    $uploadedFile->move($uploadDir, $realUploadName);
                    $fullPath = $uploadDir . "/" . $realUploadName;

                    $adminProjectService = $this->get('app.admin_project_service');
                    $adminLocationService = $this->get('app.af_location_service');                  
 
                    // 改partnerId
                    $rtn = $adminProjectService->initProject($affiliatePartnerId, $RFQId, $originalFileName, $fullPath, $province, $city, $completePoints);
                    if('success' == $rtn['status']){
                        return $this->redirect($this->generateUrl('admin_project_show', array('affiliatePartnerId' => $affiliatePartnerId)));
                    } else {
                        $errmsg = $rtn['msg'];
                    }
                } else {
                    $errmsg = '奖励积分数过大';
                }
            } else {
                $errmsg = '输入项目不合要求';
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
        return $this->redirect($this->generateUrl('admin_project_show', array('affiliatePartnerId' => $affiliatePartnerId)));
    }
   
}
