<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.hotel.list.get
 *
 * @author lsj
 *
 */
class HotelListGetRequest
{
    /**
	 * 查询字段：Product数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 *页码（>0的自然数），1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
	 */
    private $page_no;
    /**
	 *每页条数（>0自然数），每页返回最多100条，默认值为40
	 */
    private $page_size;

    /**
	 *酒店所在的城市，可以从open.hotel.city.get获取
	 */
    private $city;
     /**
	  *酒店星级，可多值，值之间用“,”分隔,值为”1“，”2“，”3“，”4“，”5“，”6“
	  */
    private $star;
     /**
	  *价格区间，多个字段用“,”分隔，默认为空，搜索全部价格区间的酒店
	  */
    private $price_range;
     /**
	  *提供商的id,可多值，多个字段用“,”分隔，默认为空，表示搜索所有提供商的国内酒店
	  */
    private $provider_id;

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

    public function getPage_no()
    {
        return $this->page_no;
    }

    public function getPage_size()
    {
        return $this->page_size;
    }


    public function getCity()
    {
        return $this->city;
    }

    public function getStar()
    {
        return $this->star;
    }

    public function getPrice_range()
    {
        return $this->price_range;
    }

    public function getProvider_id()
    {
        return $this->provider_id;
    }


    public function setProvider_id($provider_id)
    {
        $this->provider_id = $provider_id;
        $this->apiParams["provider_id"] = $provider_id;
    }

    public function setPrice_range($price_range)
    {
        $this->price_range = $price_range;
        $this->apiParams["price_range"] = $price_range;
    }


    public function setStar($star)
    {
        $this->star = $star;
        $this->apiParams["star"] = $star;
    }

    public function setCity($city)
    {
        $this->city = $city;
        $this->apiParams["city"] = $city;
    }


    public function setPage_no($page_no)
    {
        $this->page_no = $page_no;
        $this->apiParams["page_no"] = $page_no;
    }


    public function setPage_size($page_size)
    {
        $this->page_size = $page_size;
        $this->apiParams["page_size"] = $page_size;
    }

    public function getApiMethodName()
    {
        return "open.hotel.list.get";
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
