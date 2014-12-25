<?php
namespace Jili\BackendBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\BackendBundle\Form\ExperienceAdvertisementType;
use Jili\FrontendBundle\Entity\ExperienceAdvertisement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExperienceAdvertisementController extends Controller  implements  IpAuthenticatedController
{
    /**
     * @Route("/ExperienceAdvertisementList", name="experience_ad_list")
     * @Template
     * @abstract
     */
    public function experienceAdvertisementListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lists= $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisementList();
        return array('lists'=>$lists);
    }
    
    /**
     * @Route("/addExperienceAdvertisement", name="_bankend_addExperienceAdvertisement")
     * @Template
     */
    public function addExperienceAdvertisementAction()
    {
        $request = $this->get('request');
        $code = "";
        $ea = new ExperienceAdvertisement();
        $em = $this->getDoctrine()->getManager();
        $form  = $this->createForm(new ExperienceAdvertisementType(),$ea);
        if ($request->getMethod() == 'GET') {
            return array( 'form'=> $form->createView(),'code'=>$code);
        }
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $ea = $form->getData();
                $path =  $this->container->getParameter('upload_experience_advertisement_dir');
                $ea->setDeleteFlag(0);
                $ea->setCreateTime(new \Datetime());
                $ea->setUpdateTime(new \Datetime());
                $em->persist($ea);
                $code= $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->upload($path,$ea);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('experience_ad_list') );
                }
            }
            return array( 'form'=> $form->createView(),'code'=>$code );
        }
    }
    
    /**
    * @Route("/editExperienceAdvertisement/{id}", name="_bankend_editExperienceAdvertisement")
    * @Template
    */
    public function editExperienceAdvertisementAction($id)
    {
        $request = $this->get('request');
        $code = "";
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'GET') {
            $detail = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById($id);
            $form =  $this->createForm(new ExperienceAdvertisementType(), $detail);
            return array( 'form'=> $form->createView(),'code'=>$code);
        }
        if ($request->getMethod() == 'POST') {
            $form  = $this->createForm(new ExperienceAdvertisementType());
            $form->bind($request);
            if ($form->isValid()) {
                $formdata = $form->getData();
                $ea = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById( $formdata['id'] );
                $old_img = $ea->getMissionImgUrl();
                $ea->setPoint($formdata['point']);
                $ea->setMissionHall($formdata['missionHall']);
                if($formdata['missionImgUrl'] || $formdata['missionHall']!=$ea->getMissionHall()){
                    $path = $this->container->getParameter('upload_experience_advertisement_dir');
                    $remain_path = $this->container->getParameter('upload_experience_advertisement_dir_old');
                    $ea->setMissionImgUrl($formdata['missionImgUrl']);
                    $code= $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->upload($path,$ea,$remain_path,$old_img);
                }
                $ea->setMissionTitle($formdata['missionTitle']);
                $ea->setUpdateTime(new \Datetime(date('Y-m-d H:i:s',time())));
                $em->persist($ea);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('experience_ad_list') );
                }
            }
            return array( 'form'=> $form->createView(),'code'=>$code);
        } 
    }
    
    /**
    * @Route("/delExperienceAdvertisement/{id}", name="_backend_delExperienceAdvertisement")
    */
    public function delExperienceAdvertisementAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ea = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById($id);
        $ea->setDeleteFlag($this->container->getParameter('delete_flag_true'));
        $em->persist($ea);
        $em->flush();
        return $this->redirect($this->generateUrl('experience_ad_list'));
    }
}

