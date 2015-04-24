<?php

namespace Jili\FrontendBundle\Entity;

/**
 * 用于 {cps_asp}_advertisement.
 */
interface CustomRedirectUrlInterface 
{
    /**
     * 将自定义的url中的反馈内容设置为jili的userid
     */
    public function getRedirectUrlWithUserId($uid);
}
?>
