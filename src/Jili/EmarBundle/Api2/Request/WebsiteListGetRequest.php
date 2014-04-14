<?php
namespace Jili\EmarBundle\Api2\Request;
/** API:open.website.list.get
 * 
 * @author lsj
 * 
 */
class WebsiteListGetRequest
{
	/**
	 * 查询字段：WebsiteList数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 商家类型type：1->yqfWebSite(亿起发合作商家), 2->pdtWebSite(抓取商品信息的商家)
	 */
	private $type;
	
	/**
	 * 商家分类的id:可传多值，多个数值以半角逗号(,)分隔
	 */
	private $catid;
	
	
	private $apiParams = array();
	
	public function getCatid() {
		return $this->catid;
	}
	public function setCatid($catid) {
		$this->catid = $catid;
		$this->apiParams["catid"] = $catid;
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
		return "open.website.list.get";
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
