<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.hotel.search
 *
 * @author lsj
 *
 */
class HotelSearchGetRequest
{
    /**
	 * 查询字段：Product数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 搜索关键词，从酒店名称里进行搜索
	 */
    private $keyword;
    /**
	 *页码（>0的自然数），1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
	 */
    private $page_no;
    /**
	 *每页条数（>0自然数），每页返回最多100条，默认值为40
	 */
    private $page_size;
    /**
	 *包括开始日期和结束日期，两个日期用“,”分隔开，此处的date格式是年月日，如：2012-12-24，date[]的格式举例如：date=2012-12-22,2012-12-24
	 */
    private $tdate;
    /**
	 *酒店所在的城市，可以从open.hotel.city.get获取
	 */
    private $city;
     /**
	  *包含2个值：[类型,排序方式]，类型只有Price，排序方式分：1=从低到高，2=从高到低，默认为空，表示按相关度排序
	  */
    private $orderby;
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

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function getPage_no()
    {
        return $this->page_no;
    }

    public function getPage_size()
    {
        return $this->page_size;
    }

    public function getTdate()
    {
        return $this->tdate;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getOrderby()
    {
        return $this->orderby;
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

    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        $this->apiParams["orderby"] = $orderby;
    }

    public function setCity($city)
    {
        $this->city = $city;
        $this->apiParams["city"] = $city;
    }


    public function setTdate($tdate)
    {
        $this->tdate = $tdate;
        $this->apiParams["date"] = $tdate;
    }
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
        $this->apiParams["keyword"] = $keyword;
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
        return "open.hotel.search";
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
