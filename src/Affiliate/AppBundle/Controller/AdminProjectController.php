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
    const URL_UPLOAD_DIR = '/../web/uploads/urlfiles'; // %kernel.root_dir%/../web/uploads/urlfiles

    public function showProjectAction(Request $request, $partnerId = null)
    {
        $currentPage = $request->query->get('page', 1);
        $adminProjectService = $this->get('app.admin_project_service');
        $pagination = $adminProjectService->getProjectList($partnerId, $currentPage, 20);
        $param = array(
            'partnerId' => $partnerId,
            'pagination' => $pagination
            );
        return $this->render('AffiliateAppBundle:admin:project.html.twig', $param);
    }

    public function openProjectAction(Request $request)
    {
        
    }

    public function closeProjectAction(Request $request)
    {
        
    }

    public function uploadUrlsAction(Request $request, $partnerId = null)
    {
        $builder = $this->createFormBuilder();
        $builder->add('RFQId', 'text', array('label' => 'RFQId:'));
        $builder->add('urlFile', 'file', array('label' => 'File to Submit'));
        $form = $builder->getForm();

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
                $rootDir = $this->get('kernel')->getRootDir();
                $uploadDir = $rootDir . self::URL_UPLOAD_DIR;
                $realUploadName = $originalFileName . "." . md5(uniqid());
                $uploadedFile->move($uploadDir, $realUploadName);
                $fullPath = $uploadDir . "/" . $realUploadName;

                $adminProjectService = $this->get('app.admin_project_service');
                $rtn = $adminProjectService->initProject($partnerId, $RFQId, $originalFileName, $fullPath);
                

                //print 'Max memory usage=' . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB' . '<br>';
                // print 'status=' . $rtn['status'] . '<br>';
                // print 'errmsg' . $rtn['errmsg'] . '<br>';
                if('success' == $rtn['status']){
                    return $this->redirect($this->generateUrl('admin_project_show', array('partnerId' => $partnerId)));
                } else {
                    $errmsg = $rtn['errmsg'];
                }
            }

         }

        return $this->render('AffiliateAppBundle:admin:upload.html.twig',
            array('form' => $form->createView(),
                'partnerId' => $partnerId,
                'errmsg' => $errmsg
                )
        );
    }
}
