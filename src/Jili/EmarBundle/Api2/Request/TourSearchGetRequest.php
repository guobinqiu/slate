<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.tour.search
 *
 * @author lsj
 *
 */
class TourSearchGetRequest
{
    /**
	 * 查询字段：Tour数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 出发城市
	 */
    private $city_begin;
    /**
	 * 搜索关键词，包括：旅游线路的目的地、目的城市、旅游景点等方面
	 */
    private $keyword;
    /**
	 * 页码（>0的自然数），1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
	 */
    private $page_no;
    /**
	 * 每页条数（>0自然数），每页返回最多100条，默认值为40
	 */
    private $page_size;
    /**
	 *包含2个值：[类型,排序方式]，类型只有Price和days，排序方式分：1=从低到高，2=从高到低
	 */
    private $orderby;

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


    public function getCity_begin()
    {
        return $this->city_begin;
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

    public function getOrderby()
    {
        return $this->orderby;
    }


    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        $this->apiParams["orderby"] = $orderby;
    }

    public function setPage_size($page_size)
    {
        $this->page_size = $page_size;
        $this->apiParams["page_size"] = $page_size;
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

    public function setCity_begin($city_begin)
    {
        $this->city_begin = $city_begin;
        $this->apiParams["city_begin"] = $city_begin;
    }

    public function getApiMethodName()
    {
        return "open.tour.search";
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
