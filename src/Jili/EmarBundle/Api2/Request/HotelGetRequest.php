<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.hotel.get
 *
 * @author lsj
 *
 */
class HotelGetRequest
{
    /**
	 * 查询字段：Product数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 酒店的id
	 */
    private $hotel_id;
    /**
	 * 可以在酒店提供商的商家获取该url
	 */
    private $hotel_url;


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


    public function getHotel_id()
    {
        return $this->hotel_id;
    }

    public function getHotel_url()
    {
        return $this->hotel_url;
    }

    public function setHotel_id($hotel_id)
    {
        $this->hotel_id = $hotel_id;
        $this->apiParams["hotel_id"] = $hotel_id;
    }

    public function setHotel_url($hotel_url)
    {
        $this->hotel_url = $hotel_url;
        $this->apiParams["hotel_url"] = $hotel_url;
    }

    public function getApiMethodName()
    {
        return "open.hotel.get";
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
