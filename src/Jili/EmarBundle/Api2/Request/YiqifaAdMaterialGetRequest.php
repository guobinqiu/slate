<?php
namespace Jili\EmarBundle\Api2\Request;
/**add sdk**/
/** API:open.yiqifa.ad.material.get
 * 
 * @author lsj
 * 
 */
class YiqifaAdMaterialGetRequest
{
	/**
	 * 查询字段：YiqifaAdMaterail数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 商家类型type：1->yqfWebSite(亿起发合作商家), 2->pdtWebSite(抓取商品信息的商家)
	 */
	private $type;
	
	/**
	 * 素材所属的活动id
	 */
	private $ad_id;
	
	
	private $apiParams = array();
	
	public function getAd_id() {
		return $this->$ad_id;
	}
	public function setAd_id($ad_id) {
		$this->ad_id = $ad_id;
		$this->apiParams["ad_id"] = $ad_id;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParams["fields"] = $fields;
	}
	public function getFields()
	{
		return $this->fields;
	}
	public function setWtype($type)
	{
		$this->type = $type;
		$this->apiParams["type"] = $type;
	}
	public function getWtype()
	{
		return $this->type;
	}
	public function getApiMethodName()
	{
		return "open.yiqifa.ad.material.get";
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