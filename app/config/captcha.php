<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options

function is_mobile_agent()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (!preg_match('/(iPad)/i', $ua) && preg_match('/(iPhone|Mobile|UP.Browser|Android|BlackBerry|Windows CE|Nokia|webOS|Opera Mini|SonyEricsson|opera mobi|Windows Phone|IEMobile|POLARIS)/i', $ua)) {
        return true;
    }
    return false;
}

return [
    // Captcha configuration for signup page
    'SignupCaptcha' => [
        'UserInputID' => 'signup_captchaCode',
        'ImageWidth' => is_mobile_agent() ? 100 : 150,
        'ImageHeight' => is_mobile_agent() ? 32 : 45,
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 4),
        'SoundEnabled' => false,
        'Locale' => 'cmn-CN',
        'CodeStyle' => CodeStyle::Alpha,
    ],

];