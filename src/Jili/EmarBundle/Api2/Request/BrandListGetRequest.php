<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.brand.list.get
 * 
 * @author lsj
 *
 */
class BrandListGetRequest
{
	/**
	 * 查询字段：BrandList数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;

	/**
	 *品牌分类id：可多值，值之间用“,”半角逗号分隔
	 */
	private $catid;
	
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

	public function setCatid($catid)
	{
		$this->catid = $catid;
		$this->apiParams["catid"] = $catid;
	}
	public function getCatid()
	{
		return $this->catid;
	}
	
	public function getApiMethodName()
	{
		return "open.brand.list.get";
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