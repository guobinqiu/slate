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
     * validate password 用户密码为5-100个字符，密码至少包含1位字母和1位数字
     *
     * @param string $password
     *
     * @return boolean
     */
    public static function validatePassword($password)
    {
        if (empty($password)) {
            return false;
        }

        $length = strlen($password);
        if ($length < 5) {
            return false;
        } else if ($length > 100) {
            return false;
        }

        if (!preg_match('/^.*(?=.*?[A-Za-z])(?=.*?[0-9]).*$/', $password)) {
            return false;
        }
        return true;
    }
}