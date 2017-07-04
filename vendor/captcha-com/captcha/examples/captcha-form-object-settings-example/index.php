<?php session_start(); ?>
<?php require("botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Options: Form Object Settings Code Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
  <script type="text/javascript" src="<?php echo CaptchaUrls::ScriptIncludeUrl() ?>"></script>
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Options: <br /> Form Object Settings Code Example</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

       <?php // Adding BotDetect Captcha to the page
        $Captcha1 = new Captcha('Captcha1');
        $Captcha1->UserInputID = 'CaptchaCode1';

        $Captcha1->CodeLength = 6;
        $Captcha1->CodeStyle = CodeStyle::Numeric;
        $Captcha1->CodeTimeout = 300; // 5 minutes
        $Captcha1->DisallowedCodeSubstrings = '1,2,3,4,5,00,777,9999';

        $Captcha1->ImageStyle = ImageStyle::SunAndWarmAir;
        $Captcha1->ImageWidth = 250;
        $Captcha1->ImageHeight = 60;
        $Captcha1->ImageFormat = ImageFormat::Png;
        
        $Captcha1->SoundEnabled = true;
        $Captcha1->SoundStyle = SoundStyle::Synth;
        $Captcha1->SoundFormat = SoundFormat::WavPcm8bit8kHzMono;
        $Captcha1->SoundRegenerationMode = SoundRegenerationMode::Limited;
        
        $Captcha1->Locale = 'es-MX';
        $Captcha1->ImageTooltip = 'Custom Mexican Spanish Captcha image tooltip';
        $Captcha1->SoundTooltip = 'Custom Mexican Spanish Captcha sound icon tooltip';
        $Captcha1->ReloadTooltip = 'Custom Mexican Spanish Captcha reload icon tooltip';
        $Captcha1->HelpLinkUrl = 'custom-mexican-spanish-captcha-help-page.html';
        $Captcha1->HelpLinkText = 'Custom Mexican Spanish Captcha help link text';
        
        $Captcha1->ReloadEnabled = true;
        $Captcha1->UseSmallIcons = false;
        $Captcha1->UseHorizontalIcons = false;
        $Captcha1->SoundIconUrl = '';
        $Captcha1->ReloadIconUrl = '';
        $Captcha1->IconsDivWidth = 27;
        $Captcha1->HelpLinkEnabled = true;
        $Captcha1->HelpLinkMode = HelpLinkMode::Text;
        $Captcha1->TabIndex = -1;
        $Captcha1->AdditionalCssClasses = "class1 class2 class3" ;
        $Captcha1->AdditionalInlineCss = "border: 4px solid #fff; background-color: #f8f8f8;";
        
        $Captcha1->AddScriptInclude = false;
        $Captcha1->AddInitScript = true;
        $Captcha1->AutoUppercaseInput = true;
        $Captcha1->AutoFocusInput = true;
        $Captcha1->AutoClearInput = true;
        $Captcha1->AutoReloadExpiredCaptchas = true;
        $Captcha1->AutoReloadTimeout = 7200; // 2 hours
        $Captcha1->SoundStartDelay = 100; // 0.1 seconds
        $Captcha1->RemoteScriptEnabled = true;

        echo $Captcha1->Html();
      ?>

      <div class="validationDiv">
        <input name="CaptchaCode1" type="text" id="CaptchaCode1" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $Captcha1->Validate();

            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo '<span class="incorrect">Incorrect code</span>';
            } else {
              // Captcha validation passed, perform protected action
              echo '<span class="correct">Correct code</span>';
            }
          }
        ?>
      </div>

    </fieldset>


    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

      <?php // Adding BotDetect Captcha to the page
        $Captcha2 = new Captcha('Captcha2');
        $Captcha2->UserInputID = 'CaptchaCode2';

        $Captcha2->CodeLength = 3;
        $Captcha2->CodeStyle = CodeStyle::Alpha;
        $Captcha2->CodeTimeout = 900; // 15 minutes
        $Captcha2->DisallowedCodeSubstrings = 'AAA,BBB,CCC';

        // only re-calcualated on form load
        $Captcha2->ImageStyle = CaptchaRandomization::GetRandomImageStyle(
          array(ImageStyle::BlackOverlap, ImageStyle::Graffiti, ImageStyle::Overlap)
        );

        $Captcha2->ImageWidth = 120;
        $Captcha2->ImageHeight = 35;
        
        $Captcha2->ImageFormat = ImageFormat::Png;
        
        $Captcha2->CustomDarkColor = "DarkGreen";
        $Captcha2->CustomLightColor = "#eeeeff";
        
        $Captcha2->SoundStyle = SoundStyle::Dispatch;
        $Captcha2->SoundFormat = SoundFormat::WavPcm8bit8kHzMono;
        $Captcha2->SoundRegenerationMode = SoundRegenerationMode::None;
        
        $Captcha2->Locale = 'fr-CA';
        $Captcha2->ImageTooltip = 'Custom Canadian French Captcha image tooltip';
        $Captcha2->SoundTooltip = 'Custom Canadian French Captcha sound icon tooltip';
        $Captcha2->ReloadTooltip = 'Custom Canadian French Captcha reload icon tooltip';
        $Captcha2->HelpLinkUrl = 'custom-canadian-french-captcha-help-page.html';
        $Captcha2->HelpLinkText = 'Custom Canadian French Captcha help link text';
        
        $Captcha2->ReloadEnabled = true;
        $Captcha2->UseSmallIcons = null;
        $Captcha2->UseHorizontalIcons = null;
        $Captcha2->SoundIconUrl = '';
        $Captcha2->ReloadIconUrl = '';
        $Captcha2->IconsDivWidth = 50;
        $Captcha2->HelpLinkEnabled = true;
        $Captcha2->HelpLinkMode = HelpLinkMode::Image;
        $Captcha2->TabIndex = 15;
        $Captcha2->AdditionalCssClasses = '';
        $Captcha2->AdditionalInlineCss = '';
        
        $Captcha2->AddScriptInclude = false;
        $Captcha2->AddInitScript = true;
        $Captcha2->AutoUppercaseInput = false;
        $Captcha2->AutoFocusInput = false;
        $Captcha2->AutoClearInput = false;
        $Captcha2->AutoReloadExpiredCaptchas = true;
        $Captcha2->AutoReloadTimeout = 3600; // 1 hour
        $Captcha2->SoundStartDelay = 1000; // 1 second
        $Captcha2->RemoteScriptEnabled = false;
        
        echo $Captcha2->Html();
      ?>

      <div class="validationDiv">
        <input type="text" name="CaptchaCode2" id="CaptchaCode2" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $Captcha2->Validate();

            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo '<span class="incorrect">Incorrect code</span>';
            } else {
              // Captcha validation passed, perform protected action
              echo '<span class="correct">Correct code</span>';
            }
          }
        ?>
      </div>

    </fieldset>

    <input type="submit" name="SubmitButton" id="SubmitButton" value="Submit Form" />
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to configure Captcha challenges by setting <code>Captcha</code> object properties in PHP form source.</p>
        <p>Multiple PHP forms within the same PHP website can be protected by BotDetect Captcha challenges: e.g. you could include <code>botdetect.php</code> in both your Contact form and Registration form source.</p>
        <p>To function properly, separate Captcha challenges placed on each form should have different names (<code>CaptchaId</code> values sent to the <code>Captcha</code> object constructor, <code>Captcha1</code> and <code>Captcha2</code> in this example), and can use completely different Captcha settings.</p>
        <p>Even multiple Captcha instances placed on the same form won't interfere with each other's validation and functionality. And if a user opens the same page in multiple browser tabs, each tab will independently validate the shown Captcha code.</p>
        <p>Shared Captcha settings should always be placed in the <code>CaptchaConfig.php</code> application configuration file, and only diverging settings set through Captcha object instance properties in form code, to avoid code duplication.</p> 
        <p>Settings that affect only Captcha container markup generation take effect immediately (changing <code>$Captcha->Html</code> output), but settings that affect Captcha challenge (image or sound) generation in separate Http requests need to be saved in PHP Session state when set through <code>Captcha</code> object instance properties in form source, consuming server resources and reverting to defaults when the PHP Session expires.</p>
        <p>Please note that if configured values are dynamic (e.g. <code>CaptchaRandomization</code> helper or other function calls in form code), they will be re-calculated only when the form is reloaded (form code is executed). For example, Captcha <code>ImageStyle</code> randomized in PHP form source will not change on each Captcha Reload button click, but only on each form load.</p>
      </div>
    </div>

    <div class="column">
      <?php if (Captcha::IsFree()) { ?>
      <div class="note warning">
        <h3>Free Version Limitations</h3>
        <ul>
          <li>The free version of BotDetect only includes a limited subset of the available CAPTCHA image styles and CAPTCHA sound styles.</li>
          <li>The free version of BotDetect includes a randomized <code>BotDetect™</code> trademark in the background of 50% of all Captcha images generated.</li>
          <li>It also has limited sound functionality, replacing the CAPTCHA sound with "SOUND DEMO" for randomly selected 50% of all CAPTCHA codes.</li>
          <li>Lastly, the bottom 10 px of the CAPTCHA image are reserved for a link to the BotDetect website.</li>
        </ul>
        <p>These limitations are removed if you <a rel="nofollow" href="http://captcha.com/shop.html?utm_source=installation&amp;utm_medium=php&amp;utm_campaign=4.1.0" title="BotDetect CAPTCHA online store, pricing information, payment options, licensing &amp; upgrading">upgrade</a> your BotDetect license.</p>
      </div>
      <?php } ?>
    </div>
  </div>

  <div id="systeminfo">
    <p><?php echo Captcha::LibInfo(); ?></p>
  </div>

</body>
</html>