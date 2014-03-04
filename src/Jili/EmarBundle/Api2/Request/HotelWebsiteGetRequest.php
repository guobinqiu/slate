<?php 
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.hotel.website.get
 * 
 * @author lsj
 *
 */
class HotelWebsiteGetRequest
{
	/**
	 * 查询字段：HotelWebsite数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	
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
		
	public function getApiMethodName()
	{
		return "open.hotel.website.get";
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