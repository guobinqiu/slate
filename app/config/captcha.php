<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options

return [
    // Captcha configuration for signup page
    'SignupCaptcha' => [
        'UserInputID' => 'captchaCode',
        'ImageWidth' => 140,
        'ImageHeight' => 50,
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
        'ImageStyle' => ImageStyle::AncientMosaic,
        'SoundEnabled' => false,
        'Locale' => 'cmn-CN',
    ],

];