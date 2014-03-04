<?php
namespace Jili\EmarBundle\Api2\Request;
/** API:open.website.get
 * 
 * @author lsj
 * 
 */
class WebsiteGetRequest
{
	/**
	 * 查询字段：Website数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 商家类型type：1->yqfWebSite(亿起发合作商家), 2->pdtWebSite(抓取商品信息的商家)
	 */
	private $type;
	
	/**
	 * 商家id
	 */
	private $webid;
	
	
	private $apiParams = array();
	
	public function getWebid() {
		return $this->$webid;
	}
	public function setWebid($webid) {
		$this->webid = $webid;
		$this->apiParams["webid"] = $webid;
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
		$this->apiParams['type'] = $type;
	}
	public function getWtype()
	{
		return $this->type;
	}
	public function getApiMethodName()
	{
		return "open.website.get";
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
