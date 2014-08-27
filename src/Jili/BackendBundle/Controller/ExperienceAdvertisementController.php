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
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $ea = $form->getData();
                $path =  $this->container->getParameter('upload_experience_advertisement_dir');
                $ea->setDeleteFlag(0);
                $ea->setCreateTime(new \Datetime(date('Y-m-d H:i:s',time())));
                $ea->setUpdateTime(new \Datetime(date('Y-m-d H:i:s',time())));
                $em->persist($ea);
                $code = $ea->upload($path,$ea->getMissionHall());
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('experience_ad_list') );
                }
            }
        }
        return array( 'form'=> $form->createView(),'code'=>$code );
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
        if ($request->getMethod() == 'POST') {
            $form  = $this->createForm(new ExperienceAdvertisementType());
            $form->bind($request);
            if ($form->isValid()) {
                $formdata = $form->getData();
                $ea = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById( $formdata['id'] );
                $ea->setPoint($formdata['point']);
                if($formdata['missionImgUrl'] || $formdata['missionHall']!=$ea->getMissionHall()){
                    $path = $this->container->getParameter('upload_experience_advertisement_dir');
                    $ea->setMissionImgUrl($formdata['missionImgUrl']);
                    $code = $ea->upload($path,$formdata['missionHall']);
                }
                $ea->setMissionHall($formdata['missionHall']);
                $ea->setMissionTitle($formdata['missionTitle']);
                $ea->setUpdateTime(new \Datetime(date('Y-m-d H:i:s',time())));
                $em->persist($ea);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('experience_ad_list') );
                }
            }
        } else {
            $detail = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->findOneById($id);
            $form =  $this->createForm(new ExperienceAdvertisementType(), $detail);
        }
        return array( 'form'=> $form->createView(),'code'=>$code);
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

