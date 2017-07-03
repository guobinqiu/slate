<?php

// BotDetect PHP Captcha configuration options


// Captcha codes
// ---------------------------------------------------------------------------

// Number of characters in randomly generated Captcha codes (answers to Captcha 
// challenges).
// The default value is random (4-6 characters).
// Valid user Captcha code length setting values are integers larger than 0 and 
// smaller than 16.
// It is recommended to always randomize Captcha code length since it 
// significantly increases Captcha security vs. automated analysis.
$BotDetect->CodeLength = CaptchaRandomization::GetRandomCodeLength(3, 5);

// Character types used to generate random Captcha codes. 
// The default value is Alphanumeric.
// Valid user Captcha code style setting values are members of the BotDetect
// CodeStyle enumeration (Alpha, Numeric, or Alphanumeric).
// Since entropy of the Captcha challenge depends on character set size to 
// the power of Captcha code length, alpha codes should be slightly longer 
// than alphanumeric ones (while numeric Captcha codes should be significantly
// longer) to achieve an appropriate level of Captcha security. 
$BotDetect->CodeStyle = CodeStyle::Alpha;

// Strings that should never occur in randomly generated Captcha codes. Can be 
// both single characters (allows Captcha character set customization) and 
// sequences of two or more characters (useful for swear words filtering, avoiding
// particular hard-to-read sequences etc.).
// The default value is empty (Captcha code filtering is optional).
// Valid user disallowed Captcha code substring setting values are lists of 
// arbitrary strings, in CSV format. Whitespace is ignored, and disallowed 
// substrings are not case-sensitive (because Captcha codes are case-insensitive 
// as well). 
// The character set used for Captcha code generation is automatically chosen 
// based on Captcha locale. Since each character needs to have its pronunciation 
// recorded and available to BotDetect code, expanding that default character set 
// to include new characters is not supported (it would break Captcha sound 
// functionality). However, if a particular character is found hard to read, 
// it can easily be excluded from randomly generated Captcha codes. Furthermore, 
// offensive or otherwise undesirable words and character sequences can be banned.
// Since Captcha codes are generally short, it doesn't make sense to use an 
// actual dictionary of words, but simple short sequences that cover multiple 
// disallowed values. E.g. to prevent the random generator from using both 'man',
// 'manners' and 'mannequin' in Captcha codes, it's enough to ban the 'man' 
// sequence.
$BotDetect->DisallowedCodeSubstrings = 'd,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,aa,bb,cc,abc,bca,cab';

// The time period after random Captcha code generation during which Captcha 
// challenges based on it can be solved: when it expires, even correct inputs 
// will be considered as invalid submissions.
// The default value is 1200 seconds (20 minutes).
// Valid user Captcha code timeout setting values are integers larger than 30 
// and smaller than 7200 (i.e. between half a minute and 2 hours). 
// Reducing the Captcha code timeout is an optional security improvement that
// narrows the window of opportunity for attacks based on reusing the Captcha 
// challenge on another site controlled by the attacker, proxying it to human 
// solvers, or similar attempts to bypass the Captcha protection. However, to 
// meet usability criteria, users filling out the form should always be given 
// reasonably enough time to solve the Captcha challenge. Since Captcha codes 
// are stored in Session state by default, please note that if the configured 
// code timeout is longer than the active Session timeout, Captcha validation 
// will still fail if the user Session expires due to inactivity.
$BotDetect->CodeTimeout = 600;

// Application configuration switch to use if you want to run automated tests 
// that need to be able to submit a Captcha-protected form in QA environments.
// The default value is false.
// Valid user Captcha test mode setting values are booleans.
// When test mode is enabled, it makes the Captcha challenge trivially solvable 
// (always using "TEST" as the solution instead of a random sequence of 
// characters). Be careful NEVER to enable this on production websites by 
// mistake, since it will allow bots to trivially bypass the Captcha, but will 
// still provide an obstacle for human users. Because of the serious security 
// implications if the Captcha test mode setting is misapplied, it can only be 
// enabled globally for the whole application (individual forms and Captcha 
// object instances can't change it), and will display a warning in generated 
// Captcha container markup.
$BotDetect->TestModeEnabled = false;


// Captcha images
// ---------------------------------------------------------------------------

// The BotDetect drawing algorithm used to render Captcha codes in image Captcha 
// challenges.
// The default value is random (an image style is chosen from all available 
// values for each Captcha image generated).
// Valid user Captcha image style setting values are members of the BotDetect 
// ImageStyle enumeration. Please note that some image styles are restricted to 
// licensed versions of BotDetect, and will be ignored in free version 
// implementations.
// It's best to randomize the Captcha image style, since randomly choosing a 
// style for each Captcha image generated provides the highest level of Captcha 
// security against automated OCR analysis.
$imageStyles = array(
  ImageStyle::Chipped, 
  ImageStyle::Fingerprints, 
  ImageStyle::Graffiti, 
  ImageStyle::Bullets
);
$BotDetect->ImageStyle = CaptchaRandomization::GetRandomImageStyle($imageStyles);

// Size of Captcha image challenges generated.
// The default value is 250 x 50 pixels.
// Valid user Captcha image size setting values are integers: widths can be 
// between 20 and 500 pixels and heights between 20 and 200 pixels.
// To keep Captcha images reasonably readable, their width:height ratio 
// should be approximately the same as the average Captcha code length.ls
$BotDetect->ImageWidth = 200;
$BotDetect->ImageHeight = 50;

// Image format in which Captcha images will be generated and sent to the client.
// The default value is Jpeg.
// Valid user Captcha image format setting values are members of the BotDetect 
// ImageFormat enumeration (Jpeg, Png, Gif).
// Please note that some Captcha image styles will result in low-quality 
// (pixelated) images when the image format is set to Gif, due to randomized 
// color schemes and use of color gradients. When switching image formats, 
// please take care to check the impact on Captcha image readability.
$BotDetect->ImageFormat = ImageFormat::Png;

// BotDetect allows Captcha image color scheme customization though two color 
// points: a custom dark color and a custom light color. 
// The default values are empty (Captcha color customization is optional).
// Valid user Captcha custom dark / light color setting values are Html colors, 
// so you can use both predefined color names and custom color hex values.
// Since many Captcha drawing styles randomize the actual color used, the 
// user-defined values are used as randomization starting points instead of 
// absolute values. Furthermore, since some drawing styles use light text on 
// a dark background, while other draw dark text on a light background, text 
// and background colors are not set directly, but are referred to as simply 
// the "dark" and the "light" color. This allows you to randomize the image 
// drawing style, for example, and still keep a consistent color scheme 
// adjusted to your website design.
$BotDetect->CustomDarkColor = '#483d8b';
$BotDetect->CustomLightColor = '#87cefa';

// Application configuration setting that allows centralized temporary 
// disabling of individual BotDetect image styles if there is ever an urgent 
// issue that requires it. 
// The default value is empty (image style disabling is an optional feature 
// meant for short-term use during exceptional situations).
// Valid user disabled image styles setting values are CSV strings of ImageStyle 
// names (case-insensitive, separator whitespace is ignored).
// BotDetect image styles used on user forms can be configured in different ways 
// (a single static value, a randomized value, a dynamic value that adapts to 
// visitor behavior) and can apply to all Captcha instances in an application 
// or be specific to a particular Captcha challenge placed on a single form. If 
// an urgent issue is ever discovered in a BotDetect image style implementation 
// (e.g. a bug causing it to throw errors in certain circumstances, or a 
// weakness allowing some forms of automated analysis to bypass it, or a 
// memory leak etc.), users should be able to deactivate the problematic 
// ImageStyle while they're waiting for issue resolution. This BotDetect 
// setting acts as a centralized application configuration switch which allows 
// such image style deactivation, without requiring users to examine and 
// possibly modify all of their source code that might be affected. It also 
// makes it much easier to revert the change later when the issue gets fixed.
$BotDetect->DisabledImageStyles = 'Chipped,Lego,Wave';


// Captcha sounds
// ---------------------------------------------------------------------------

// Is Captcha sound enabled.
// The default value is true.
// Valid user Captcha sound enabled setting values are booleans.
// Captcha sound can be disabled entirely (for example if you are using the 
// free version of BotDetect, which only supports demo sound that is not 
// actually accessible to human visitors) by setting this property to false. 
$BotDetect->SoundEnabled = true;

// The BotDetect audio generation algorithm used to pronounce Captcha codes in 
// sound Captcha challenges.
// The default value is random (a sound style is chosen from all available 
// values for each Captcha sound generated).
// Valid user Captcha sound style setting values are members of the BotDetect 
// SoundStyle enumeration. Please note that some sound styles are restricted 
// to licensed versions of BotDetect, and will be ignored in free version 
// implementations.
// It's best to randomize the Captcha sound style, since randomly choosing a 
// style for each Captcha sound generated provides the highest level of Captcha 
// security against automated audio analysis and voice recognition.
$soundStyles = array(
  SoundStyle::Dispatch, 
  SoundStyle::RedAlert, 
  SoundStyle::Synth
);
$BotDetect->SoundStyle = CaptchaRandomization::GetRandomSoundStyle($soundStyles);

// Audio format in which Captcha sounds will be generated and sent to the client.
// The default value is WavPcm16bit8kHzMono.
// Valid user Captcha sound format setting values are members of the BotDetect 
// SoundFormat enumeration (WavPcm16bit8kHzMono, WavPcm8bit8kHzMono).
// Using 8 bit sound instead of default 16 bits per example lowers the WAV file 
// download size, but reduces sound quality.
$BotDetect->SoundFormat = SoundFormat::WavPcm16bit8kHzMono;

// How will multiple consecutive requests for audio Captcha with the same 
// Captcha code ("sound regeneration") be handled by BotDetect - a tradeoff 
// of security, usability, and storage requirements.
// The default value is Limited.
// Valid user Captcha sound regeneration mode setting values are members of the 
// BotDetect SoundRegenerationMode enumeration (None, Limited, Unlimited).
// BotDetect defaults to limited sound regeneration as the most reasonable 
// overall trade-off. At user discretion, higher security and usability can be 
// achieved by disabling sound regeneration at the cost of significant amounts 
// of server-side storage space. Unlimited sound regeneration is not recommended 
// due to low security, but is left as an option for backwards-compatibility.
$BotDetect->SoundRegenerationMode = SoundRegenerationMode::None;

// Application configuration setting that controls whether BotDetect disables 
// (greys-out and prevents clicks on) the Captcha sound icon and displays a 
// warning tooltip when the sound package file containing character 
// pronunciations for the currently set Captcha locale can not be found.
// The default value is true (display a warning when the required .bdsp file 
// cannot be found).
// Valid user warn about missing sound packages setting values are booleans.
// Warnings about missing sound packages help during development and deployment, 
// so you don't mistakenly forget to download and copy the needed files. 
// However, this warning is not meant for (and should never be seen by) site 
// visitors. So if you didn't copy a particular sound package because you 
// intentionally don't want to support audio Captcha sounds in that language, 
// you can disable the warning (and the sound icon for such locales).
$BotDetect->WarnAboutMissingSoundPackages = false;

// Application configuration setting that allows centralized temporary 
// disabling of individual BotDetect sound styles if there is ever an urgent 
// issue that requires it. 
// The default value is empty (sound style disabling is an optional feature 
// meant for short-term use during exceptional situations).
// Valid user disabled sound styles setting values are CSV strings of SoundStyle 
// names (case-insensitive, separator whitespace is ignored).
// BotDetect sound styles used on user forms can be configured in different ways 
// (a single static value, a randomized value, a dynamic value that adapts to 
// visitor behavior) and can apply to all Captcha instances in an application 
// or be specific to a particular Captcha challenge placed on a single form. If 
// an urgent issue is ever discovered in a BotDetect sound style implementation 
// (e.g. a bug causing it to throw errors in certain circumstances, or a 
// weakness allowing some forms of automated analysis to bypass it, or a 
// memory leak etc.), users should be able to deactivate the problematic 
// SoundStyle while they're waiting for issue resolution. This BotDetect 
// setting acts as a centralized application configuration switch which allows 
// such sound style deactivation, without requiring users to examine and 
// possibly modify all of their source code that might be affected. It also 
// makes it much easier to revert the change later when the issue gets fixed.
$BotDetect->DisabledSoundStyles = 'RedAlert,HiveMind';


// Captcha localization & locale-dependent strings
// ---------------------------------------------------------------------------

// Captcha locale string, determining the exact character set used for random 
// Captcha code generation and the pronunciation language used for sound 
// Captcha generation.
// The default value is en-US.
// Valid user Captcha locale setting values are composed of ISO language codes 
// (for example en, ru, cmn, ...), charset codes (for example ja-Hira uses 
// Japanese Hiragana characters, while ja-Kana uses Japanese Katakana characters) 
// and country codes (for example en-US and en-GB differ in the pronunciation 
// used).
// Check the BotDetect localization page to find the list of currently supported 
// locales, and download the pronunciation resources required for Captcha sounds.
// If you use a right-to-left locale setting like Arabic or Hebrew, you should 
// also set the appropriate text direction on the textbox element used for 
// Captcha code retyping (dir="rtl").
$BotDetect->Locale = 'en-US';

// The alternative text of the Captcha image Html element.
// The default value is Retype the CAPTCHA code from the image.
// Valid user Captcha image tooltip setting values are arbitrary strings.
$BotDetect->ImageTooltip = 'Custom Captcha image tooltip';

// Tooltip of the Captcha sound icon.
// The default value is "Speak the CAPTCHA code".
// Valid user Captcha sound tooltip setting values are arbitrary strings.
$BotDetect->SoundTooltip = 'Custom Captcha sound icon tooltip';

// Tooltip of the Captcha reload icon.
// The default value is "Change the CAPTCHA code".
// Valid user Captcha reload tooltip setting values are arbitrary strings.
$BotDetect->ReloadTooltip = 'Custom Captcha reload icon tooltip';

// Text or tooltip of the Captcha help link, depending on help link mode.
// The default value depends on the width of the Captcha image.
// Valid user Captcha help link setting values are strings at least 4 characters 
// long.
$BotDetect->HelpLinkText = 'Custom Captcha help link text';

// Url of the localized Captcha help page the help link points to.
// The default value depends on Captcha locale.
// Valid user Captcha help link url setting values are absolute or relative Urls.
// This setting is only supported in licensed versions of BotDetect.
$BotDetect->HelpLinkUrl = 'custom-captcha-help-page.html';


// Captcha controls & appearance
// ---------------------------------------------------------------------------

// Is Captcha reloading (changing the Captcha code because the current one is 
// too hard to read) enabled.
// The default value is true.
// Valid user Captcha reload enabled setting values are booleans.
// Requesting a new Captcha challenge on the current form requires client-side 
// scripting, so the reload icon is only shown in browsers that have JavaScript 
// enabled. When JavaScript is disabled or unsupported, the visitor can still 
// get a different Captcha challenge by reloading the form.
$BotDetect->ReloadEnabled = false;

// Default BotDetect Captcha icons are 22x22 pixels large, but there is also a 
// smaller set of 17x17 px icons used when the default ones are too large. 
// This settings allows you to control which icon set will be used.
// The default value is true when the Captcha image height is < 50px, and false 
// otherwise.
// Valid user use small Captcha icons setting values are booleans: setting this 
// value to true will force BotDetect to use small built-in icons, while false 
// will disable automatic switching to small icons depending on image height.
// This setting only applies to default BotDetect icons, and should not be used 
// in combination with user-defined icons.
$BotDetect->UseSmallIcons = false;

// BotDetect displays the Captcha sound and reload icon one below the other by 
// default, and switches to displaying them one beside the other when Captcha 
// images are small enough. This setting allows you to control which BotDetect 
// icon layout will be used.
// The default value is true when the Captcha image height is <40px, and false 
// otherwise.
// Valid user use horizontal Captcha icons setting values are booleans: setting 
// this value to true will force BotDetect to use a horizontal icon layout, 
// while false will disable automatic switching to horizontal icons depending 
// on image height.
// This setting only applies to default BotDetect icons, and should not be used 
// in combination with user-defined icons.
$BotDetect->UseHorizontalIcons = false;

// Url of the optional custom Captcha sound icon that will be used instead of 
// the default one.
// The default value is 'botdetect/public/bdc-sound-icon.gif'.
// Valid user Captcha sound icon setting values are absolute or relative Urls.
// When specifying a custom Captcha sound icon, you should make sure its 
// filename includes "icon", and also provide a disabled variation of the icon 
// that will be shown during sound playback (to prevent the user from clicking 
// the icon multiple times). The disabled sound icon variant should be the same 
// size and have a filename based on the active one ("icon" replaced with 
// "disabled-icon").
$BotDetect->SoundIconUrl = 'custom-sound-icon.gif';

// Url of the optional custom Captcha reload icon that will be used instead of 
// the default one.
// The default value is 'botdetect/public/bdc-reload-icon.gif'.
// Valid user Captcha reload icon setting values are absolute or relative Urls.
// When specifying a custom Captcha reload icon, you should make sure its 
// filename includes "icon", and also provide a disabled variation of the icon 
// that will be shown while the browser is waiting to fetch a new Captcha 
// challenge from the server (to prevent the user from clicking the icon
// multiple times). The disabled reload icon variant should be the same size 
// and have a filename based on the active one ("icon" replaced with 
// "disabled-icon").
$BotDetect->ReloadIconUrl = BDC_URL_ROOT . 'bdc-reload-icon.gif';

// Custom width of the Captcha icons div element.
// The default value depends on Captcha image height, since BotDetect will 
// automatically determine default icon size and position to match it.
// Valid user Captcha icons div width setting values are positive integers.
// If your custom Captcha icons are not of the same size as the default 
// BotDetect ones (22x22 px), the UseHorizontalIcons setting won't be able to 
// control the icon layout correctly. You can control whether your custom icons 
// will be displayed one beneath the other or one beside the other by setting 
// an appropriate icons div width: setting it to at least twice the icon width 
// + 8px of padding will result in horizontal icon layout, while smaller values 
// will result in vertical icon layout.
$BotDetect->IconsDivWidth = 25;

// Will Captcha markup include a link to a Captcha help page providing Captcha 
// instructions and explanations for form users.
// The default value is true.
// Valid user help link enabled setting values are booleans.
// This setting is only supported in licensed version of BotDetect.
$BotDetect->HelpLinkEnabled = true;

// How will the Captcha help link be displayed.
// The default value is Text.
// Valid user Captcha help link mode setting values are members of the 
// BotDetect HelpLinkMode enumeration ("Image" or "Text").
// When using the Image help link mode, Captcha image is wrapped in a link, and 
// clicking it opens the help page in a new browser tab. This mode takes less 
// space, but can lead to accidental clicks (particularly by mobile visitors).
// When using the Text help link mode, Captcha image height is automatically 
// reduced by 10 px and a text link to the Captcha help page is inserted below 
// it. If this makes the Captcha images less readable, you can compensate by 
// increasing the Captcha image height.
$BotDetect->HelpLinkMode = HelpLinkMode::Text;

// User-defined CSS classes that will be added to the BotDetect CAPTCHA 
// container <div>.
// The default value is empty.
// Valid user additional Css classes setting values are strings containing 
// desired class names in standard space-delimited CSS class format.
// CSS style declarations for these custom classes must be defined in a user 
// stylesheet added to the page.
$BotDetect->AdditionalCssClasses = 'class1 class2 class3';

// User-defined CSS style declarations that will be added as inline style 
// of the BotDetect CAPTCHA container <div>.
// The default value is empty.
// Valid user additional Css style setting values are strings containing 
// desired CSS style declarations in standard semicolon-delimited CSS style format.
$BotDetect->AdditionalInlineCss = 'border: 4px solid #fff; background-color: #f8f8f8;';



// Captcha client-side
// ---------------------------------------------------------------------------

// Should the BotDetect JavaScript client-side script code be included by the 
// generated Captcha container markup. 
// The default value is true.
// Valid user add script include setting values are booleans.
// This setting will usually only be set to false if you have multiple Captcha 
// instances on the same form and only want the first one's markup to include 
// the required BotDetect client-side code. Another possible use is when you 
// manually add the necessary <script> include to page <head>, possibly combined 
// with other JavaScript code and minified to reduce the number of Http requests 
// made by the page.
$BotDetect->AddScriptInclude = false;

// Should the JavaScript code for BotDetect client-side object creation be 
// included in the generated Captcha container markup.
// The default value is true.
// Valid user add init script setting values are booleans.
// Adding the initialization script fragment should be turned off only if you 
// will manually add the necessary <script> code to form <head> for example.
$BotDetect->AddInitScript = true;

// Should user Captcha code input be automatically uppercased on the fly.
// The default value is true.
// Valid user auto uppercase input setting values are booleans.
// Since Captcha validation is not and should not be case-sensitive (it would 
// hinder human visitors more than bots, and how would case differences be 
// communicated through audio Captcha in all supported pronunciation languages?), 
// automatically uppercasing user input is a small usability improvement that 
// helps communicate the case-insensitivity of the Captcha challenge to users.
$BotDetect->AutoUppercaseInput = true;

// Should the Captcha code input textbox automatically be assigned focus on 
// all Captcha sound and Captcha reload icon clicks, allowing the users to 
// more easily type in the code as they hear it or as the new image loads. 
// The default value is true.
// Valid user Captcha auto focus input setting values are booleans.
// Automatic input element focusing is not triggered by auto-reloading of 
// expired Captcha challenges, since the user might be filling out another 
// field on the form when the auto-reload starts and shouldn't be distracted.
$BotDetect->AutoFocusInput = true;

// Should the Captcha user input textbox automatically be cleared on all 
// reload icon clicks and auto-reloads of expired Captcha codes.
// The default value is true.
// Valid user auto clear input setting values are booleans.
// Automatic input clearing is a small usability improvement: since any 
// previous user input will be invalidated by Captcha reloading, it helps so 
// users don't have to delete the previous input themselves.
$BotDetect->AutoClearInput = true;

// Should Captcha challenges automatically be reloaded when the Captcha code 
// expires (controlled by the CodeTimeout property).
// The default value is true.
// Valid user auto reload expired Captchas setting values are booleans.
// Automatic reloading of expired Captcha codes allows you to have a short 
// Captcha code timeout (e.g. 2 minutes) to narrow the window of opportunity 
// for Captcha reusing on other sites or human-solver-powered bots, and actual 
// visitors can still fill out your form at their own pace and without rushing 
// (since the Captcha image will be reloaded automatically when it is no longer 
// valid).
$BotDetect->AutoReloadExpiredCaptchas = true;

// Time period in seconds after which automatic reloading of expired Captcha 
// challenges will cease.
// The default value is 7200 seconds (2 hours).
// Valid user auto reload timeout setting values are positive integers.
// This timeout prevents indefinite extension of the visitor Session, when the 
// user leaves the form open in a background browser tab over the weekend for 
// example.
$BotDetect->AutoReloadTimeout = 3600; // 1 hour

// Starting delay (in miliseconds) of Captcha audio JavaScript playback.
// The default value is 0 (no delay).
// Valid user Captcha sound start delay setting values are positive integers.
// An initial delay before browser sound playback can be useful for improving 
// usability of the Captcha audio for blind people using JAWS or similar screen 
// readers. Such assistive technology will read the label associated with the 
// Captcha code textbox and start sound playback simultaneously when the sound 
// icon is activated (since Captcha sound playing automatically focuses the 
// Captcha code textbox by default). Setting this delay to e.g. 2000 (2 seconds) 
// will give the user time to hear both the pronounced label and the Captcha 
// sound clearly.
$BotDetect->SoundStartDelay = 1000;

// Should BotDetect also add a remote JavaScript include 
// (remote.captcha.com/include.js) loaded from the captcha.com server (which is 
// currently used only for stats, but is planned to develop into additional 
// Captcha functionality).
// The default value is true.
// Valid user remote script enabled setting values are booleans.
// This setting is only supported in licensed version of BotDetct.
$BotDetect->RemoteScriptEnabled = true;



// Captcha-related PHP application settings
// ---------------------------------------------------------------------------

// The Url of the PHP script processing Captcha challenge Http requests.
// The default value is "botdetect.php".
// Valid user Captcha handler Url setting values are strings containing Url 
// paths.
// If you need to implement a custom handler for Captcha requests (for example, 
// to conform to conventions of a MVC framework), you can change the base Url 
// of Captcha image/sound/validation requests through the HandlerUrl 
// configuration property. You will of course also have to implement the 
// BotDetect Captcha library initialization (usually performed by botdetect.php) 
// in your custom handler .php source.
$BotDetect->HandlerUrl = 'custom-captcha-request-handler.php';

// Functions used by BotDetect to store Captcha codes and other Captcha data 
// between Http requests.
// The default values are names of built-in BotDetect functions wrapping access 
// to PHP Session state ('PHP_Session_Save', 'PHP_Session_Load', 
// 'PHP_Session_Clear').
// Valid user Captcha persistence function names setting values are strings with 
// names of defined Save($key, $value) / Load($key) / Clear($key) functions.
// BotDetect requires server-side storage to persist Captcha codes and other 
// Captcha data across Http requests (so the Captcha code generated during the 
// Captcha image GET request can be compared to user input when processing the 
// form POST request etc.). In case you want to use a custom storage medium for 
// Captcha data in your website (e.g. a database), you can use this BotDetect 
// setting to customize the persistence back end used by BotDetect code.
// Please note that BotDetect code calls these persistence function when the 
// Captcha generation & validation workflow requires it, and doesn't know the 
// details of the underlying persistence medium. Due to the nature of the data 
// stored (Captcha codes & similar), the persistence medium should be 
// visitor-specific and automatically cleared when a "visit" ends (just like 
// PHP Sessions are).
// In your implementation, you should take care that data is kept separate for 
// different visitors (so any Captcha code can be validated only by the same 
// visitor that caused it to be generated in the first place – otherwise you'd 
// reduce Captcha security), and cleared when appropriate (if you just save 
// Captcha data in a Session-like SQL database but never clear it, the database 
// will grow in size infinitely!).
$BotDetect->SaveFunctionName = 'PHP_Session_Save';
$BotDetect->LoadFunctionName = 'PHP_Session_Load';
$BotDetect->ClearFunctionName = 'PHP_Session_Clear';

?>