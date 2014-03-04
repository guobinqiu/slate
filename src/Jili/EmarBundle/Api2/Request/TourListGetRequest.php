<?php
namespace Jili\EmarBundle\Api2\Request;
/** API:open.tour.list.get
 * 
 * @author lsj
 * 
 */
class TourListGetRequest
{
	/**
	 * 查询字段：TourList数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 线路的日期，可选值，多个字段用“,”分隔
	 */
	private $days;
	/**
	 * 旅游线路的价格区间，可选值，用“,”分隔
	 */
	private  $price_range;
	/**
	 * 旅游线路的提供商的id，，可选值，多个字段用“,”分隔
	 */
	private  $provider_id;
	/**
	 * 出发日期，包括最早日期和最晚日期，date[]包括2个值，date[最早日期,最晚日期]
	 */
	private   $tdate;
	/**
	 * 页码（>0的自然数），1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
	 */
	private   $page_no;
	/**
	 * 每页条数（>0自然数），每页返回最多100条，默认值为40
	 */
	private   $page_size;
	/**
	 * 出发城市，一次最多不超过10个城市
	 */
	private    $city_begin;

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
	
	
	public function getDays() {
		return $this->days;
	}
	
	public function getPrice_range() {
		return $this->price_range;
	}
	
	public function getProvider_no() {
		return $this->provider_no;
	}
	public function getTdate() {
		return $this->tdate;
	}
	
	public function getPage_no() {
		return $this->page_no;
	}
	
	public function getPage_size() {
		return $this->page_size;
	}
	
	public function getCity_begin() {
		return $this->city_begin;
	}
	
	public function setDays($days) {
		$this->days = $days;
		$this->apiParams["days"] = $days;
	}

	public function setPrice_range($price_range) {
		$this->price_range = $price_range;
		$this->apiParams["price_range"] = $price_range;
	}

	
	public function setProvider_id($provider_id) {
		$this->provider_id = $provider_id;
		$this->apiParams["provider_id"] = $provider_id;
	}
	
	
	public function setTdate($tdate) {
		$this->tdate = $tdate;
		$this->apiParams["date"] = $tdate;
	}

	public function setPage_no($page_no) {
		$this->page_no = $page_no;
		$this->apiParams["page_no"] = $page_no;
	}

	
	public function setPage_size($page_size) {
		$this->page_size = $page_size;
		$this->apiParams["page_size"] = $page_size;
	}
	public function setCity_begin($city_begin) {
		$this->city_begin = $city_begin;
		$this->apiParams["city_begin"] = $city_begin;
	}

	public function getApiMethodName()
	{
		return "open.tour.list.get";
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