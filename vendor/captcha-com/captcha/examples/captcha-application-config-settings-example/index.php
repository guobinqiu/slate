<?php session_start(); ?>
<?php require("custom-captcha-request-handler.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Options: Application Config Settings Code Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
  <script type="text/javascript" src="<?php echo CaptchaUrls::ScriptIncludeUrl() ?>"></script>
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Options: <br /> Application Config Settings Code Example</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

      <?php // Adding BotDetect Captcha to the page
        $AppConfigCustomizedCaptcha = new Captcha("AppConfigCustomizedCaptcha");
        $AppConfigCustomizedCaptcha->UserInputID = "CaptchaCode";
        echo $AppConfigCustomizedCaptcha->Html();
      ?>

      <div class="validationDiv">
        <input name="CaptchaCode" type="text" id="CaptchaCode" />
        <input type="submit" name="ValidateCaptchaButton" id="ValidateCaptchaButton" value="Validate" />
        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $AppConfigCustomizedCaptcha->Validate();

            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo "<span class=\"incorrect\">Incorrect code</span>";
            } else {
              // Captcha validation passed, perform protected action
              echo "<span class=\"correct\">Correct code</span>";
            }
          }
        ?>
      </div>
    </fieldset>
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to configure Captcha challenges by overriding Captcha library defaults in application configuration files.</p>
        <p>BotDetect allows user-defined customization of many Captcha options through a special <code>CaptchaConfig.php</code> file, which should be placed in the same folder as the <code>botdetect.php</code> include used by your PHP forms.</p> 
        <p>Captcha settings from this configuration file will apply to all Captcha challenges shown on forms including that particular copy of <code>botdetect.php</code>, and will act as defaults with which all Captcha objects will be created. This makes configuration file settings the simplest and most convenient way of Captcha customization for most use cases.</p>
        <p>The <code>CaptchaConfig.php</code> file used in this code example contains detailed descriptions and explanations of the many customizable Captcha options exposed by the BotDetect PHP Captcha configuration API.</p>
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