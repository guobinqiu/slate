<?php session_start(); ?>
<?php require("botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Options: Request Dynamic Settings Code Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Options: <br /> Request Dynamic Settings Code Example</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

      <?php // Adding BotDetect Captcha to the page
        $DynamicCaptcha = new Captcha("DynamicCaptcha");
        $DynamicCaptcha->UserInputID = "CaptchaCode";
        echo $DynamicCaptcha->Html();
      ?>

      <div class="validationDiv">
        <input name="CaptchaCode" type="text" id="CaptchaCode" />
        <input type="submit" name="ValidateCaptchaButton" value="Validate" id="ValidateCaptchaButton" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $DynamicCaptcha->Validate();
            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo "<span class=\"incorrect\">Incorrect code</span>";
              IncrementFailedValidationsCount();
            } else {
              // Captcha validation passed, perform protected action
              echo "<span class=\"correct\">Correct code</span>";
              ResetFailedValidationsCount(); // reset counter after successful validation
            }
          }
        ?>
      </div>
    </fieldset>
    
    <div id="output">
    <?php
      $count = GetFailedValidationsCount();
      echo "<p>Failed Captcha validations: {$count}</p>";
      if ($count < 3) {
        echo "<p>Dynamic Captcha difficulty: Easy</p>";
      } else if ($count < 10) {
        echo "<p>Dynamic Captcha difficulty: Moderate</p>";
      } else {
        echo "<p>Dynamic Captcha difficulty: Hard</p>";
      }
    ?>
    </div>
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to dynamically adjust Captcha configuration, potentially on each Http request made by the client.</p>
        <p>Any PHP code setting Captcha properties in the <code>CaptchaConfig.php</code> file will be executed not only for each protected form GET or POST request (like Captcha configuration code placed in form source would be), but also for each each GET request loading a Captcha image or sound, or making an Ajax Captcha validation call.</p>
        <p>If configured values are dynamic (e.g. <code>CaptchaRandomization</code> helper or other function calls in <code>CaptchaConfig.php</code> code), they will be re-calculated for each Captcha challenge generated. For example, Captcha <code>ImageStyle</code> randomized in <code>CaptchaConfig.php</code> code will change on each Captcha reload button click.</p>
        <p>This means your code can reliably keep track of visitor interaction with the Captcha challenge and dynamically adjust its settings. Also, while <code>CaptchaConfig.php</code> settings apply to all Captcha instances by default, you can also selectively apply them based on CaptchaId.</p>
        <p>To show an example of the possible dynamic Captcha configuration adjustments, this code example increases the difficulty of the Captcha test if the visitor associated with the current PHP Session fails a certain number of Captcha validation attempts, and also sets the Captcha locale to Chinese for requests from a certain IP range.</p>
      </div>
    </div>
      
    <div class="column">
      <?php if (Captcha::IsFree()) { ?>
      <div class="note warning">
        <h3>Free Version Limitations</h3>
        <ul>
          <li>The free version of BotDetect only includes a limited subset of the available CAPTCHA image styles and CAPTCHA sound styles.</li>
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