<?php

// helper functions for counting Captcha validation failures at form submission
if ((function_exists('session_status ') && PHP_SESSION_NONE === session_status()) || !isset($_SESSION)) {
  session_start(); 
}

define('FAILED_VALIDATIONS_COUNT_KEY', 'FailedValidationsCount');

function GetFailedValidationsCount() {
  $count = 0;
  if (isset($_SESSION) && array_key_exists(FAILED_VALIDATIONS_COUNT_KEY, $_SESSION)) {
    $temp = unserialize($_SESSION[FAILED_VALIDATIONS_COUNT_KEY]);
    if (false !== $temp) {
      $count = $temp;
    }
  }
  return $count;
}

function IncrementFailedValidationsCount() {
  $count = GetFailedValidationsCount();
  $count++;
  $_SESSION[FAILED_VALIDATIONS_COUNT_KEY] = serialize($count);
}

function ResetFailedValidationsCount() {
  unset($_SESSION[FAILED_VALIDATIONS_COUNT_KEY]);
}

?>