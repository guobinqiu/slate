<?php
namespace Jili\BackendBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\BackendBundle\Form\ExperienceAdvertisementType;
use Jili\FrontendBundle\Entity\ExperienceAdvertisement;

class ExperienceAdvertisementController extends Controller 
{
    private function getAdminIp(){
        if($_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_ele_ip') || 
            $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_un_ip') ||
            $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||
           substr( $_SERVER['REMOTE_ADDR'],0,10)  == '192.168.1.' ||
            $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_vpn_ip'))
            return false;
        else
            return true;
          
    }
    
    /**
     * @Route("/ExperienceAdvertisementList", name="experience_ad_list")
     * @Template
     * @abstract
     */
    public function experienceAdvertisementListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lists= $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisementList(50);
        return array('lists'=>$lists);
    }
    
     /**
     * @Route("/toAddExperienceAdvertisement", name="_bankend_toAddExperienceAdvertisement")
     * @Template
     */
    public function toAddExperienceAdvertisementAction()
    {
        $form  = $this->createForm(new ExperienceAdvertisementType());
        return $this->render('JiliBackendBundle:ExperienceAdvertisement:addExperienceAdvertisement.html.twig',array('form'=>$form->createView()));
    }
    
    /**
     * @Route("/addExperienceAdvertisement", name="_bankend_addExperienceAdvertisement")
     * @Template
     */
    public function addExperienceAdvertisementAction()
    {
        $request = $this->get('request');
        $ea = new ExperienceAdvertisement();
        $em = $this->getDoctrine()->getManager();
        $form  = $this->createForm(new ExperienceAdvertisementType());
        $form->bind($request);
        if ($request->getMethod() == 'POST') {
            if ($form->isValid()) {
                //$path =  $this->container->getParameter('upload_activity_dir');
                $ea->setDeleteFlag(0);
                $ea->setCreateTime(date('Y-m-d H:i:s',time()));
                $ea->setUpdateTime(date('Y-m-d H:i:s',time()));
                $em->persist($ea);
                //$code = $em->upload($path);
                return $this->redirect($this->generateUrl('experience_ad_list') );
            }else{
            }
        }
        return array( 'form'=> $form->createView() );
    }
    
    /**
    * @Route("/toEditExperienceAdvertisement/{id}", name="_bankend_toEditExperienceAdvertisement")
    * @Template
    */
    public function toEditExperienceAdvertisementAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $detail = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById($id);
        $form =  $this->createForm(new ExperienceAdvertisementType(), $detail);
        return $this->render('JiliBackendBundle:ExperienceAdvertisement:editExperienceAdvertisement.html.twig',array('form'=>$form->createView()));
    }
    
    /**
    * @Route("/editExperienceAdvertisement", name="_bankend_editExperienceAdvertisement")
    */
    public function editExperienceAdvertisementAction()
    {
        $request = $this->get('request');
        $ea = new ExperienceAdvertisement();
        $em = $this->getDoctrine()->getManager();
        $form  = $this->createForm(new ExperienceAdvertisementType());
        $mission_hall = $request->request->get('mission_hall');
        $mission_img_url = $request->request->get('mission_img_url');
        $point = $request->request->get('point');
        $missionUrl = $request->request->get('missionUrl');
        $form->bind($request);
        if ($form->isValid()) {
            $path =  $this->container->getParameter('upload_activity_dir');
            $ea->setMissionHall($mission_hall);
            $ea->setPoint($point);
            $ea->setMissionImgUrl($mission_img_url);
            $ea->setMissionUrl($missionUrl);
            //$ea->setDeleteFlag(0);
            $ea->setCreateTime(date('Y-m-d H:i:s',time()));
            $ea->setUpdateTime(date('Y-m-d H:i:s',time()));
            $em->persist($ea);
            $code = $em->upload($path);
            return $this->redirect($this->generateUrl('experience_ad_list') );
        }else{

        }
    }
    
    /**
    * @Route("/delExperienceAdvertisement/{id}", name="_backend_delExperienceAdvertisement")
    */
    public function delExperienceAdvertisementAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ea = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById($id);
        $ea->setDeleteFlag(1);
        $em->persist($ea);
        $em->flush();
        return $this->redirect($this->generateUrl('experience_ad_list'));
    }
}

