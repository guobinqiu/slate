<?php
namespace Jili\EmarBundle\Api2\Request;
	/**
	*api:open.hotactivity.brand.get
	*@auther:lsj
	**/

	class HotactivityBrandGetRequest
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

		private $apiParams = array();

		public function setFields($fields){
			$this->fields = $fields;
			$this->apiParams[fields] = $fields;
		}

		public function getFields(){
			return $this->fields;
		}

		public function setCatid($catid){
			$this->catid = $catid;
			$this->apiParams[catid] = $catid;
		}
		public function getCatid(){
			return $this->catid;
		}

		public function setWebid($webid){
			$this->webid = $webid;
			$this->apiParams[webid] = $webid;
		}

		public function getApiParams(){
			return $this->apiParams;
		}

		public function getApiMethodName(){
			return "open.hotactivity.brand.get";
		}

		public function putOtherTextParam($key,$value)
		{
			$this->apiParams[$key] = $value;
			$this->$key = $value;
		}
	}
?>