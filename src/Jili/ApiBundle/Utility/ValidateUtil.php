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
     * get all form errors
     *
     * @param object $form
     *
     * @return array The error meeeages
     */
    public static function getFormErrors($form)
    {
        $error_meeeages = array ();
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            if ($error) {
                $error_meeeages[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $key => $child) {
            $error_tiems = $child->getErrors();
            foreach ($error_tiems as $child_error) {
                if ($child_error) {
                    $error_meeeages[] = $key . ": " . $child_error->getMessage();
                }
            }
        }

        return $error_meeeages;
    }
}