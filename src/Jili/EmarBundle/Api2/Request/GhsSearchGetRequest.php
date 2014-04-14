<?php
namespace Jili\EmarBundle\Api2\Request;
/** API:open.ghs.search
 * 
 * @author lsj
 * 
 */
class GhsSearchGetRequest
{
	/**
	 * 查询字段：Ghs数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 *购划算商品分类，可选值，多个字段用“,”分隔
	 */
	private $keyword;
	/**
	 * 页码（>0的自然数），1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页。
	 */
	private  $page_no;
	/**
	 * 每页条数（>0自然数），每页返回最多100条，默认值为40
	 */
	private  $page_size;

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
	
	
	public function getKeyword() {
		return $this->keyword;
	}
	
	public function getPage_no() {
		return $this->page_no;
	}
	
	public function getPage_size() {
		return $this->page_size;
	}

	
	public function setKeyword($keyword) {
		$this->keyword = $keyword;
		$this->apiParams["keyword"] = $keyword;
	}

	public function setPage_no($page_no) {
		$this->page_no = $page_no;
		$this->apiParams["page_no"] = $page_no;
	}

	
	public function setPage_size($page_size) {
		$this->page_size = $page_size;
		$this->apiParams["page_size"] = $page_size;
	}

	public function getApiMethodName()
	{
		return "open.ghs.search";
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