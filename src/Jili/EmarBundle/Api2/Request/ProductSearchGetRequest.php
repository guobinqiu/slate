<?php 
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.product.search
 * 
 * @author lsj
 *
 */
class ProductSearchGetRequest
{
	/**
	 * 查询字段：Product数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 用于搜索的关键词:包括最长38个汉字，或76个字符，商品url除外
	 */
	private $keyword;
	/**
	 * 页码（>0的自然数):1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
	 */
	private $page_no;
	/**
	 * 每页条数（>0自然数):每页返回最多100条，默认值为40； page_no×page_size≤5000
	 */
	private $page_size;
	/**
	 * 分类id:可以一次性指定多个分类id，实现在垂直市场的搜索。默认值为空，可以实现综合搜索
	 */
	private  $catid;
	/**
	 * 抓取商家的id:可写多个值，值之间用“,”分隔；默认为空，返回所有商家的商品
	 */
	private  $webid;
	/**
	 * 商品的价格区间:“Price_range=最低价格,最高价格”，之间用“,”，举例:Price_range=50,300，价格不能为负数。其中“,”前的值≤“,”后的值，默认为空，返回所有价格的商品
	 */
	
	private  $price_range;
	/**
	 * 搜索排序: 排序标识1为按相关度排序，2为价格从低到高排序，3为价格从高到低排序,默认值为1
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
	
	/**
	 * @return the $keyword
	 */
	public function getKeyword() {
		return $this->keyword;
	}

	/**
	 * @return the $page_no
	 */
	public function getPage_no() {
		return $this->page_no;
	}

	/**
	 * @return the $page_size
	 */
	public function getPage_size() {
		return $this->page_size;
	}

	/**
	 * @return the $catid
	 */
	public function getCatid() {
		return $this->catid;
	}

	/**
	 * @return the $web_id
	 */
	public function getWebid() {
		return $this->webid;
	}

	/**
	 * @return the $price_range
	 */
	public function getPrice_range() {
		return $this->price_range;
	}

	/**
	 * @return the $oderby
	 */
	public function getOrderby() {
		return $this->oderby;
	}

	/**
	 * @param field_type $keyword
	 */
	public function setKeyword($keyword) {
		$this->keyword = $keyword;
		$this->apiParams["keyword"] = $keyword;
	}

	/**
	 * @param field_type $page_no
	 */
	public function setPage_no($page_no) {
		$this->page_no = $page_no;
		$this->apiParams["page_no"] = $page_no;
	}

	/**
	 * @param field_type $page_size
	 */
	public function setPage_size($page_size) {
		$this->page_size = $page_size;
		$this->apiParams["page_size"] = $page_size;
	}

	/**
	 * @param field_type $catid
	 */
	public function setCatid($catid) {
		$this->catid = $catid;
		$this->apiParams["catid"] = $catid;
	}

	/**
	 * @param field_type $web_id
	 */
	public function setWebid($webid) {
		$this->webid = $webid;
		$this->apiParams["webid"] = $webid;
	}

	/**
	 * @param field_type $price_range
	 */
	public function setPrice_range($price_range) {
		$this->price_range = $price_range;
		$this->apiParams["price_range"] = $price_range;
	}

	/**
	 * @param field_type $oderby
	 */
	public function setOrderby($orderby) {
		$this->orderby = $orderby;
		$this->apiParams["orderby"] = $orderby;
	}

	public function getApiMethodName()
	{
		return "open.product.search";
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
?>