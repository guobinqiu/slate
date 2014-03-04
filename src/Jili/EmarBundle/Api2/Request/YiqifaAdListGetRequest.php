<?php
namespace Jili\EmarBundle\Api2\Request;
/** API:open.yiqifa.ad.list.get
 * 
 * @author lsj
 */
class YiqifaAdListGetRequest
{
	/**
	 * 查询字段：Website数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;
	/**
	 * 亿起发活动的计费类型:目前只支持cps
	 */
	private $charge_type;
	/**
	 * 广告活动的分类:可多值，多个字段用“,”分隔
	 */
	private $ad_catid;
	/**
	 * 审核方式:可多值，多个字段用“,”分隔，包括：无需审核，自动审核，人工审核
	 */
	private $audit_mode;
	/**
	 * 广告活动类型:可多值，多个字段用“,”分隔，值包括3个：web，app，wap
	 */
	private $ad_type;
	
	private $apiParams = array();
	
	public function getCharge_type() {
		return $this->charge_type;
	}

	public function getAd_catid() {
		return $this->ad_catid;
	}

	public function getAudit_mode() {
		return $this->audit_mode;
	}

	public function getAd_type() {
		return $this->ad_type;
	}

	public function setCharge_type($charge_type) {
		$this->charge_type = $charge_type;
		$this->apiParams["charge_type"] = $charge_type;
	}

	public function setAd_catid($ad_catid) {
		$this->ad_catid = $ad_catid;
		$this->apiParams["ad_catid"] = $ad_catid;
	}

	
	public function setAudit_mode($audit_mode) {
		$this->audit_mode = $audit_mode;
		$this->apiParams["audit_mode"] = $audit_mode;
	}

	/**
	 * @param field_type $ad_type
	 */
	public function setAd_type($ad_type) {
		$this->ad_type = $ad_type;
		$this->apiParams["ad_type"] = $ad_type;
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
	public function getApiMethodName()
	{
		return "open.yiqifa.ad.list.get";
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