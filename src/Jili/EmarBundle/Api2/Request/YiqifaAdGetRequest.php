<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.yiqifa.ad.get
 * 
 * @author lsj
 *
 */
class YiqifaAdGetRequest
{
	/**
	 * 查询字段：YiqifaAd数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;

	/**
	 *广告活动的id
	 */
	private $ad_id;
	
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

	public function setAd_id($ad_id)
	{
		$this->ad_id = $ad_id;
		$this->apiParams["ad_id"] = $ad_id;
	}
	public function getAd_id()
	{
		return $this->ad_id;
	}
	
	public function getApiMethodName()
	{
		return "open.yiqifa.ad.get";
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