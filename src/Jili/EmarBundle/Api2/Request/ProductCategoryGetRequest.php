<?php
namespace Jili\EmarBundle\Api2\Request;
/**
 * API:open.category.get
 *
 * @author lsj
 *
 */
class ProductCategoryGetRequest
{
    /**
	 * 查询字段：ProductCategory数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
    private $fields;
    /**
	 * 商品分类的父类id:默认为空，返回该id下的全部子类
	 */
    private $parent_id;

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
    public function setParent_id($parent_id)
    {
        $this->parent_id = $parent_id;
        $this->apiParams["parent_id"] = $parent_id;
    }
    public function getParent_id()
    {
        return $this->parent_id;
    }
    public function getApiMethodName()
    {
        return "open.category.get";
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
