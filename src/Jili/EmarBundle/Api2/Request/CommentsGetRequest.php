<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.comment.get
 *
 * @author lsj
 *
 */
class CommentsGetRequest
{
    /**
	 * 查询字段：Comments数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 *抓取商品的id:可以通过open.product.search和open.product.list.get获取，此id必须是同一商家的商品。值在1-1000个
	 */
    private $pdt_id;
    /**
	 *商家的id
	 */
    private $webid;
    /**
	 *评论的类型:可选值，多个字段用“,”分隔，值包括： good=好评，normal=中评，less=差评，默认为空，即返回全部类型的评论
	 */
    private $type;


    private $apiParams = array();

    public function setFields($fields)
    {
        $this->fields = $fields;
        $this->apiParams["fields"] = $fields;
    }
    public function getFields()
    {
        return $this->fields;
    }
    public function setPdt_id($pdt_id)
    {
        $this->pdt_id = $pdt_id;
        $this->apiParams["pdt_id"] = $pdt_id;
    }
    public function getPdt_id()
    {
        return $this->pdt_id;
    }
    public function setWebid($webid)
    {
        $this->webid = $webid;
        $this->apiParams["webid"] = $webid;
    }
    public function getWebid()
    {
        return $this->webid;
    }
    public function setWtype($type)
    {
        $this->type = $type;
        $this->apiParams["type"] = $type;
    }
    public function getWtype()
    {
        return $this->type;
    }

    public function getApiMethodName()
    {
        return "open.comment.get";
    }
    public function getApiParams()
    {

        return $this->apiParams;
    }
    public function putOtherTextParam($key,$value)
    {
        $this->apiParams[$key] = $value;
        $this->$key = $value;
    }

}
