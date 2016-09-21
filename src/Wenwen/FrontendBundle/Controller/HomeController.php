<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    public function indexAction(Request $request)
    {
        if (!$request->getSession()->has('uid')) {
            return $this->render('WenwenFrontendBundle:Home:index.html.twig');
        }
        return $this->render('WenwenFrontendBundle:Home:home.html.twig');
    }

    public function adExperienceAction()
    {
        $cache_fn = $this->container->getParameter('cache_config.api.top_adExperience.key');
        $cache_duration = $this->container->getParameter('cache_config.api.top_adExperience.duration');
        $cache_proxy = $this->get('cache.file_handler');

        if ($cache_proxy->isValid($cache_fn, $cache_duration)) {
            $adExperience = $cache_proxy->get($cache_fn);
        } else {
            $cache_proxy->remove($cache_fn);
            $em = $this->getDoctrine()->getManager();
            $adExperience = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement(2);
            $cache_proxy->set($cache_fn, $adExperience);
        }

        $arr['ad_experience'] = $adExperience;
        return $this->render('WenwenFrontendBundle:Advertisement:_hallHome.html.twig', $arr);
    }

}
