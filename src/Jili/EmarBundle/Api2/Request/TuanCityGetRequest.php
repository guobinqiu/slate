<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.tuan.city.get
 * 
 * @author lsj
 *
 */
class TuanCityGetRequest
{
	/**
	 * 查询字段：TuanCity数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 商家类型type：1->yqfWebSite(亿起发合作商家), 2->pdtWebSite(抓取商品信息的商家)
	 */
	private $type;
	
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
		return "open.tuan.city.get";
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
