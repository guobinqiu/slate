<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class QQLoginController extends FOSRestController
{
    /**
     * 所有的SDK接口调用，都会传入一个回调，用以接收SDK返回的调用结果
     * 登录成功后调用public void onComplete(JSONObject arg0) 回传的JsonObject
     * {
     *   "ret":0,
     *   "pay_token":"xxxxxxxxxxxxxxxx",
     *   "pf":"openmobile_android",
     *   "expires_in":"7776000",
     *   "openid":"xxxxxxxxxxxxxxxxxxx",
     *   "pfkey":"xxxxxxxxxxxxxxxxxxx",
     *   "msg":"success",
     *   "access_token":"xxxxxxxxxxxxxxxxxxxxx"
     * }
     *
     * @Rest\Post("/qq/login")
     * @Rest\QueryParam(name="openid", nullable=false, description="qq user openid")
     * @Rest\QueryParam(name="access_token", nullable=false, description="qq login access token")
     */
    public function loginAction(ParamFetcher $paramFetcher)
    {
        $data = $paramFetcher->all();
        return $this->view(ApiUtil::formatSuccess($data), HttpStatus::HTTP_OK);
    }
}