<?php

namespace Jili\ApiBundle\Utility;

class ValidateUtil
{

    /**
     * validate mobile number
     *
     * @param string $mobile
     *
     * @return boolean
     */
    public static function validateMobile($mobile)
    {
        if (preg_match("/^1\d{10}$/", $mobile)) {
            return true;
        }
        return false;
    }

    /**
     * validate period
     *
     * @param string $start_time
     * @param string $end_time
     *
     * @return boolean
     */
    public static function validatePeriod($start_time, $end_time)
    {
        if (!empty($start_time) && !empty($end_time)) {
            if ($start_time > $end_time) {
                return false;
            }
        }
        return true;
    }

    /**
     * validate password, 用户密码为6-20个字符，不能含特殊符号
     *
     * @param string $password
     *
     * @return boolean
     */
    public static function validatePassword($password)
    {
        if (!preg_match("/^[0-9A-Za-z_]{6,20}$/", $password)) {
            return false;
        }
        return true;
    }
}