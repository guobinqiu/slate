<?php

namespace Wenwen\FrontendBundle\Entity;

/**
 * TaskType
 * 积分的类型常量 对应task_history0x.task_type的值
 * 比CategoryType高一个级别
 */
class TaskType 
{
    const RENTENTION = 4;  // (+) 自己负担的积分，如，完成注册，快速问答，属性问卷，AGREEMENT，网站活动等
    const CPA = 5;         // (+) Cost per action类型的任务，如，offer99，offerwow之类的任务型平台
    const CPS = 8;         // (+) Cost per action类型的任务，如，购物返利平台
    const SURVEY = 9;      // (+) 问卷类型的任务
    const EXCHANGE = 10;   // (-) 将积分兑换成钱
    const RECOVER = 11;    // (-) 积分回收
}
