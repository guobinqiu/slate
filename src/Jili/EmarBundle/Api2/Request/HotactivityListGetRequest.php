<?php
namespace Jili\EmarBundle\Api2\Request;
	/**
	*api:open.hotactivity.list.get
	*@auther:lsj
	**/

	class HotactivityListGetRequest
	{
		/**
		* 查询字段：HotactivityBrand数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
		*/
		private $fields;
		/**
	     *特卖活动的分类:可多值，多个字段用“,”分隔，当不填写时，返回全部分类的品牌
		 */
		private $catid;
		/**
	     *特卖活动的商家:可多值，多个字段用“,”分隔，当不填写时，返回全部商家的品牌
		 */
		private $webid;
		/**
	     *页码（>0的自然数），1代表第1页，2代表第2页，依次类推；默认值为1，返回的数据是第1页
		 */
		private $page_no;
		/**
	     *每页条数（>0自然数），每页返回最多100条，默认值为40
		 */
		private $page_size;

		private $apiParams = array();

		public function setFields($fields){
			$this->fields = $fields;
			$this->apiParams["fields"] = $fields;
		}

		public function getFields(){
			return $this->fields;
		}

		public function setCatid($catid){
			$this->catid = $catid;
			$this->apiParams["catid"] = $catid;
		}
		public function getCatid(){
			return $this->catid;
		}

		public function setWebid($webid){
			$this->webid = $webid;
			$this->apiParams["webid"] = $webid;
		}

		public function getApiParams(){
			return $this->apiParams;
		}

	    public function setPage_no($page_no){
			$this->page_no = $page_no;
			$this->apiParams["page_no"] = $page_no;
		}
		public function getPage_no(){
			return $this->page_no;
		}
		 public function setPage_size($page_size){
			$this->page_size = $page_size;
			$this->apiParams["page_size"] = $page_size;
		}
		public function getPage_size(){
			return $this->page_size;
		}

		public function getApiMethodName(){
			return "open.hotactivity.list.get";
		}

		public function putOtherTextParam($key,$value){
			$this->apiParams[$key]=$value;
			$this->$key=$value;
		}
	}
?>