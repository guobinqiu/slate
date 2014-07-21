<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.discount.product.list.get
 *
 * @author lsj
 *
 */
class DiscountProductListGetRequest
{
    /**
	 * 查询字段：DiscountProduct数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 页码（>0的自然数):1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
	 */
    private $page_no;
    /**
	 * 每页条数（>0自然数):每页返回最多100条，默认值为40； page_no×page_size≤5000
	 */
    private $page_size;
    /**
	 * 团购商品分类id，可多值，值之间用“,”分隔，id可以通过open.tuan.category.get获取，默认为空，返回全部的数据
	 */
    private $catid;
    /**
	 *商家的id，可写多个值，值之间用“,”分隔，id可以通过open.tuan.merchant.get获取；默认为空，则返回所有商家的商品
	 */
    private $web_id;
    /**
	 * 商品的价格区间:“Price_range=最低价格,最高价格”，之间用“,”，举例:Price_range=50,300，价格不能为负数。其中“,”前的值≤“,”后的值，默认为空，返回所有价格的商品
	 */

    private $price_range;

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

    /**
	 * @return the $page_no
	 */
    public function getPage_no()
    {
        return $this->page_no;
    }

    /**
	 * @return the $page_size
	 */
    public function getPage_size()
    {
        return $this->page_size;
    }

    /**
	 * @return the $catid
	 */
    public function getCatid()
    {
        return $this->catid;
    }
    /**
	 * @return the $web_id
	 */
    public function getWeb_id()
    {
        return $this->web_id;
    }

    /**
	 * @return the $price_range
	 */
    public function getPrice_range()
    {
        return $this->price_range;
    }

    /**
	 * @param field_type $page_no
	 */
    public function setPage_no($page_no)
    {
        $this->page_no = $page_no;
        $this->apiParams["page_no"] = $page_no;
    }

    /**
	 * @param field_type $page_size
	 */
    public function setPage_size($page_size)
    {
        $this->page_size = $page_size;
        $this->apiParams["page_size"] = $page_size;
    }

    /**
	 * @param field_type $catid
	 */
    public function setCatid($catid)
    {
        $this->catid = $catid;
        $this->apiParams["catid"] = $catid;
    }


    /**
	 * @param field_type $web_id
	 */
    public function setWeb_id($web_id)
    {
        $this->web_id = $web_id;
        $this->apiParams["web_id"] = $web_id;
    }

    /**
	 * @param field_type $price_range
	 */
    public function setPrice_range($price_range)
    {
        $this->price_range = $price_range;
        $this->apiParams["price_range"] = $price_range;
    }

    public function getApiMethodName()
    {
        return "open.discount.product.list.get";
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
