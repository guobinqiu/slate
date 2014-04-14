<?php
namespace Jili\EmarBundle\Api2\Request;
/** API:open.tour.get
 * 
 * @author lsj
 * 
 */
class TourGetRequest
{
	/**
	 * 查询字段：Tour数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 旅游线路的id
	 */
	private $tour_id;
	/**
	 * 旅游线路在商家网站的原始链接
	 */
	private  $tour_url;


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
	
	
	public function getTour_id() {
		return $this->tour_id;
	}
	
	public function getTour_url() {
		return $this->tour_url;
	}
	
	public function setTour_id($tour_id) {
		$this->tour_id = $tour_id;
		$this->apiParams["tour_id"] = $tour_id;
	}

	public function setTour_url($tour_url) {
		$this->tour_url = $tour_url;
		$this->apiParams["tour_url"] = $tour_url;
	}

	public function getApiMethodName()
	{
		return "open.tour.get";
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