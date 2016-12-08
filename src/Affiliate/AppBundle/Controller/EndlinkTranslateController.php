<?php
namespace Affiliate\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * 问卷代理
 * 对还没有注册的用户提供回答问卷的机会
 */
class EndlinkTranslateController extends Controller
{
    // Intage 期望的 endlink url格式
    // http://r.researchpanelasia.com/redirect/reverse/complete?prj=71d49b547cbaf22fcac4287d0aa0113f &uid=****

    // TripleS 可接受的Endlink
    // http://r.researchpanelasia.com/redirect/reverse/71d49b547cbaf22fcac4287d0aa0113f/complete?uid=
    // http://r.researchpanelasia.com/redirect/reverse/71d49b547cbaf22fcac4287d0aa0113f/complete?uid=

    const TRIPLE_BASE_ENDLINK = 'http://r.researchpanelasia.com/redirect/reverse/';

    public function redirectAction(Request $request, $status = 'error')
    {
        $prj = $request->query->get('prj');
        $uid = $request->query->get('uid');

        return $this->redirect($this->transferEndlink($status, $prj, $uid));
    }

    // 实际为private function

    public function transferEndlink($status, $prj, $uid){

        $triplesEndlink = self::TRIPLE_BASE_ENDLINK . $prj . '/' . $status . '?uid=' . $uid;
        return $triplesEndlink;
    }

}
