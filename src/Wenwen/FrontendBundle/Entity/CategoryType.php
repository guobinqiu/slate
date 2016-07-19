<?php

namespace Wenwen\FrontendBundle\Entity;

/**
 * CategoryType
 * 具体的积分的类型常量 对应task_history0x.category_type的值
 */
class CategoryType
{
    // 以下为获得/失去 积分的类型定义
    // 实际的数字来自于前任猪队友们的遗产，所以暂时不好改

    // TaskType.EXCHANGE
    const MOBILE = 12;           // (-) 积分兑换手机费  
    const ALIPAY = 11;           // (-) 积分兑换支付宝  

    // TaskType.RECOVER
    const EXPIRE = 15;           // (-) 积分过期清零
    
    // TaskType.CPA
    const OFFERWOW_COST = 17;         // (+) 完成offerwow任务获得积分 
    const OFFER99_COST = 18;          // (+) 完成offer99任务获得积分

    // TaskType.RENTENTION
    const SIGNUP = 32;           // (+) 完成注册获得积分
    // 这里将来希望进一步分开 属性问卷，快速问答，AGREEMENT，网站活动 
    // *要注意* 修改实际的类型值的时候，积分统计的脚本也需要对应修改
    const QUICK_POLL = 93;       // (+) 快速问答
    const SOP_EXPENSE = 93;      // (+) 属性问卷，IR CHECK等 (快速问答  ,アンケート回答（自社）61)
    const SSI_EXPENSE = 93;      // (+) SSI AGREEMENT PRESCREEN等
    const CINT_EXPENSE = 93;     // (+) Cint AGREEMENT 
    const FULCRUM_EXPENSE = 93;  // (+) Fulcrum AGREEMENT
    const EVENT_XXX = 99;        // (+) 这个还没有被用到，具体活动的类型，需要的时候定义

    // TaskType.SURVEY
    // 这里，将来希望分成 自己公司的问卷，各类对接API的类型（Cint SSI Fulcrum）
    // *要注意* 修改实际的类型值的时候，积分统计的脚本也需要对应修改
    const SURVEY = 92;           // (+) 回答商业问卷            获得积分 (问卷回答  ,アンケート回答11) 将来不要了
    const SOP_COST = 92;         // (+) 回答SOP的实际商业问卷  
    const SSI_COST = 92;         // (+) 回答SSI的实际商业问卷  
    const CINT_COST = 92;        // (+) 回答Cint的实际商业问卷
    const FULCRUM_COST = 92;     // (+) 回答Fulcrum的实际商业问卷


}