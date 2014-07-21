<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.product.link.convert
 *
 * @author lsj
 *
 */
class ProductLinkGetRequest
{
    /**
	 * 查询字段：ProductLink数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 商品的在商家网站的原始url，可以填写多个值，之间用“,”分隔，值个数在1~1000。url必须以“http://”开头
	 */
    private $pdt_url;
    /**
	 * 商家的类型，分2种：1=亿起发合作商家；2=抓取商品信息的商家
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


    public function getWtype()
    {
        return $this->type;
    }
    public function getPdt_url()
    {
        return $this->pdt_url;
    }


    public function setWtype($type)
    {
        $this->type = $type;
        $this->apiParams["type"] = $type;
    }

    public function setPdt_url($pdt_url)
    {
        $this->pdt_url = $pdt_url;
        $this->apiParams["pdt_url"] = $pdt_url;
    }
    public function getApiMethodName()
    {
        return "open.product.link.convert";
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
