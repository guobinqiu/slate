<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options

return [
    // Captcha configuration for signup page
    'SignupCaptcha' => [
        'UserInputID' => 'captchaCode',
        'ImageWidth' => 150,
        'ImageHeight' => 50,
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 4),
        'SoundEnabled' => false,
        'Locale' => 'cmn-CN',
        'CodeStyle' => CodeStyle::Alpha,
    ],

];