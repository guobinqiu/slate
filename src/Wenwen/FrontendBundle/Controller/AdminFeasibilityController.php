<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminFeasibilityController.
 *
 * @Route("/admin/feasibility")
 */
class AdminFeasibilityController extends BaseController #implements IpAuthenticatedController
{
    /**
     * @Route("/area/distribution", name="admin_feasibility_area_distribution")
     */
    public function showAreaDistributionAction()
    {
        $sql = "
            (
            select cl.cityName as areaName, count(up.id) as cnt
            from user_profile up 
            join user u on (up.user_id = u.id) 
            join cityList cl on (up.city = cl.id) 
            where TIMESTAMPDIFF(MONTH, u.last_get_points_at, NOW()) <= 1 and cl.provinceId = 1
            group by cl.cityName order by cl.id
            )
            union
            (
            select pl.provinceName as areaName, count(up.id) as cnt
            from user_profile up 
            join user u on (up.user_id = u.id) 
            join provinceList pl on (up.province = pl.id) 
            where TIMESTAMPDIFF(MONTH, u.last_get_points_at, NOW()) <= 1 and up.province <> 1
            group by pl.provinceName order by pl.id
            )
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        $distributionMau = $result;

        $sql = "
            (
            select cl.cityName as areaName, count(up.id) as cnt
            from user_profile up 
            join user u on (up.user_id = u.id) 
            join cityList cl on (up.city = cl.id) 
            where TIMESTAMPDIFF(MONTH, u.last_get_points_at, NOW()) <= 6 and cl.provinceId = 1
            group by cl.cityName order by cl.id
            )
            union
            (
            select pl.provinceName as areaName, count(up.id) as cnt
            from user_profile up 
            join user u on (up.user_id = u.id) 
            join provinceList pl on (up.province = pl.id) 
            where TIMESTAMPDIFF(MONTH, u.last_get_points_at, NOW()) <= 6 and up.province <> 1
            group by pl.provinceName order by pl.id
            )
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        $distribution6au = $result;



        return $this->render('WenwenFrontendBundle:admin:Feasibility/areaDistribution.html.twig', array('distributionMau' => $distributionMau, 'distribution6au' => $distribution6au));
    }


}
