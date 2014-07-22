<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.tuan.city.region.get
 *
 * @author lsj
 *
 */
class TuanCityRegionGetRequest
{
    /**
	 * 查询字段：TuanCityRegion数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 商家类型type：1->yqfWebSite(亿起发合作商家), 2->pdtWebSite(抓取商品信息的商家)
	 */
    private $type;
    /**
	 *城市id，可以获取这个城市下的行政区域，可以一次填写多个城市id，之间用“,”分隔开，当city_id=0时，返回type类型下全部城市的行政区域.
	 */
    private $city_id;

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
    public function setCity_id($city_id)
    {
        $this->city_id = $city_id;
        $this->apiParams["city_id"] = $city_id;
    }
    public function getCity_id()
    {
        return $this->city_id;
    }
    public function setWtype($type)
    {
        $this->type = $type;
        $this->apiParams['type'] = $type;
    }
    public function getWtype()
    {
        return $this->type;
    }
    public function getApiMethodName()
    {
        return "open.tuan.city.region.get";
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
