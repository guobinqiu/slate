<?php
namespace Jili\ApiBundle\Utility;

class ValidateUtil {

    public static function validateMobile($mobile) {
        if (preg_match("/^1\d{10}$/", $mobile)) {
            return true;
        }
        return false;
    }
}