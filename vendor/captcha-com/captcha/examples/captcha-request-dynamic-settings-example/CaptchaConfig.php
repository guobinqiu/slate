<?php

// BotDetect PHP Captcha configuration options
// ---------------------------------------------------------------------------

// CaptchaConfig.php settings are usually global and apply to all Captcha instances in
// the application; if some settings need to be apply only to a particular Captcha 
// instance, this is how settings can be conditionally applied based on CaptchaId
if (0 == strcasecmp($CurrentCaptchaId, 'DynamicCaptcha')) {
  $BotDetect->SoundEnabled = false;
}

// re-calculated on each image request
$BotDetect->ImageStyle = CaptchaRandomization::GetRandomImageStyle(
  array(ImageStyle::Graffiti, ImageStyle::SunAndWarmAir, ImageStyle::Overlap)
);

// dynamic Captcha settings depending on failed validation attempts: increase Captcha 
// difficulty according to number of previously failed validations
include_once('counter.php');
$count = GetFailedValidationsCount();
if ($count < 3) {
  $BotDetect->CodeLength = CaptchaRandomization::GetRandomCodeLength(3, 4);
  $BotDetect->CodeStyle = CodeStyle::Numeric;
  $BotDetect->CodeTimeout = 600; // 10 minutes
} else if ($count < 10) {
  $BotDetect->CodeLength = CaptchaRandomization::GetRandomCodeLength(4, 6);
  $BotDetect->CodeStyle = CodeStyle::Alpha;
  $BotDetect->CodeTimeout = 180; // 3 minutes
} else {
  $BotDetect->CodeLength = CaptchaRandomization::GetRandomCodeLength(6, 9);
  $BotDetect->CodeStyle = CodeStyle::Alphanumeric;
  $BotDetect->CodeTimeout = 60; // 1 minute
}


// set Captcha locale to Chinese for requests from a certain IP range
$test_ip_range = '223.254.';
if (array_key_exists('REMOTE_ADDR', $_SERVER) && 
    substr($_SERVER['REMOTE_ADDR'], 0, strlen($test_ip_range)) === $test_ip_range) {
  $BotDetect->CodeStyle = CodeStyle::Alpha;
  $BotDetect->Locale = 'cmn';
}


$BotDetect->HelpLinkMode = HelpLinkMode::Image;

?>