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
    const URL_UPLOAD_DIR = '/../web/uploads/urlfiles'; // %kernel.root_dir%/../web/uploads/urlfiles

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
        if ($request->getMethod('post') == 'POST') {
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
        return $this->render('AffiliateAppBundle:admin:addPartner.html.twig', 
            array(
                'form' => $form->createView(),
                'addResult' => $addResult,
                'errmsg' => $errmsg
                )
            );
    }

    public function showProjectAction(Request $request)
    {
        // 设置endpage时要有status参数
        // status: complete/screenout/quotafull/error
        $status = $request->get('status');


        $param = array(
            'answer_status' => $status
            );
        return $this->render('AffiliateAppBundle::endpage.html.twig', $param);
    }

    public function openProjectAction(Request $request)
    {
        
    }

    public function closeProjectAction(Request $request)
    {
        
    }

    public function uploadUrlsAction(Request $request)
    {
        $builder = $this->createFormBuilder();
        $builder->add('RFQId', 'text', array('label' => 'RFQId:'));
        $builder->add('urlFile', 'file', array('label' => 'File to Submit'));
        $form = $builder->getForm();

        // Check if we are posting stuff
        if ($request->getMethod('post') == 'POST') {
            // Bind request to the form
            $form->bindRequest($request);

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
                $rtn = $adminProjectService->initProject(1, $RFQId, $originalFileName, $fullPath);
                

                //print 'Max memory usage=' . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB' . '<br>';
                // print 'status=' . $rtn['status'] . '<br>';
                // print 'errmsg' . $rtn['errmsg'] . '<br>';
            }

         }

        return $this->render('AffiliateAppBundle:admin:upload.html.twig',
            array('form' => $form->createView(),)
        );
    }
}
