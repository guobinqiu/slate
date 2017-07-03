<?php session_start(); ?>
<?php require("botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Features Demo</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Features Demo</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>
      
      <?php // Adding BotDetect Captcha to the page
        $DemoCaptcha = new Captcha("DemoCaptcha");
        $DemoCaptcha->UserInputID = "CaptchaCode";

        if ($_POST && isset($_POST['ApplyCaptchaSettings'])) {
          if (isset($_POST['Locale'])) {
            $DemoCaptcha->Locale = $_POST['Locale'];
          }
          if (isset($_POST['CodeLength']) && 0 != strcmp($_POST['CodeLength'], 'default')) {
            $DemoCaptcha->CodeLength = $_POST['CodeLength'];
          } else {
            $DemoCaptcha->CodeLength = null;
          }
          if (isset($_POST['CodeStyle'])) {
            $DemoCaptcha->CodeStyle = $_POST['CodeStyle'];
          }
          if (isset($_POST['ImageStyle']) && 0 != strcmp($_POST['ImageStyle'], 'default')) {
            $DemoCaptcha->ImageStyle = $_POST['ImageStyle'];
          } else {
            $DemoCaptcha->ImageStyle = null;
          }
          if (isset($_POST['CustomLightColor'])) {
            $DemoCaptcha->CustomLightColor = $_POST['CustomLightColor'];
          }
          if (isset($_POST['CustomDarkColor'])) {
            $DemoCaptcha->CustomDarkColor = $_POST['CustomDarkColor'];
          }
          if (isset($_POST['ImageFormat'])) {
            $DemoCaptcha->ImageFormat = $_POST['ImageFormat'];
          }
          if (isset($_POST['ImageWidth'])) {
            $DemoCaptcha->ImageWidth = $_POST['ImageWidth'];
          }
          if (isset($_POST['ImageHeight'])) {
            $DemoCaptcha->ImageHeight = $_POST['ImageHeight'];
          }
          if (isset($_POST['SoundStyle']) && 0 != strcmp($_POST['SoundStyle'], 'default')) {
            $DemoCaptcha->SoundStyle = $_POST['SoundStyle'];
          } else {
            $DemoCaptcha->SoundStyle = null;
          }
          if (isset($_POST['SoundFormat'])) {
            $DemoCaptcha->SoundFormat = $_POST['SoundFormat'];
          }
        }
        
        echo $DemoCaptcha->Html(); ?>

      <div class="validationDiv">
          <input name="CaptchaCode" type="text" id="CaptchaCode" />
          <input type="submit" name="ValidateCaptchaButton" value="Validate" id="ValidateCaptchaButton" />

          <?php // Captcha user input validation (only if the form was sumbitted)
            if ($_POST && isset($_POST['ValidateCaptchaButton'])) {
              $isHuman = $DemoCaptcha->Validate();
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

    <fieldset id="CodeProperties">
      <legend>CAPTCHA Code Properties</legend>
      <table cellpadding="5" cellspacing="0" summary="CAPTCHA Properties layout table">
        <tr>
          <td class="left">
            <label for="Locale"><span>Locale:</span></label>
          </td>
          <td>
            <select name="Locale" id="Locale">
              <option <?php if ($DemoCaptcha->Locale == 'en-Latn-US') { echo 'selected="selected" '; } ?> value="en-US">en-US</option>
              <option <?php if ($DemoCaptcha->Locale == 'en-Latn-CA') { echo 'selected="selected" '; } ?> value="en-CA">en-CA</option>
              <option <?php if ($DemoCaptcha->Locale == 'fr-Latn-CA') { echo 'selected="selected" '; } ?> value="fr-CA">fr-CA</option>
              <option <?php if ($DemoCaptcha->Locale == 'es-Latn-MX') { echo 'selected="selected" '; } ?> value="es-MX">es-MX</option>
            </select>
          </td>
          <td></td>
        </tr>
        <tr>
          <td class="left">
            <label for="CodeLength"><span>Code Length:</span></label>
          </td>
          <td>
            <select name="CodeLength" id="CodeLength">
              <option value="default">[Default (4-6)]</option>
              <?php
                for($i=1; $i<=15; $i++) { ?>
                  <option <?php if (isset($_POST['CodeLength']) && 0 == strcmp($_POST['CodeLength'], $i)) { echo 'selected="selected"'; }?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php
                }
              ?>
            </select>
          </td>
          <td></td>
        </tr>
        <tr>
          <td class="left">
            <label for="CodeStyle"><span>Code Style:</span></label>
          </td>
          <td>
            <select name="CodeStyle" id="CodeStyle">
              <?php
                foreach (CodeStyle::$Names as $value => $name) { ?>
                  <option <?php if ($DemoCaptcha->CodeStyle == $value) { echo 'selected="selected"'; }?> value="<?php echo $value; ?>"><?php echo $name; ?></option>
                  <?php
                }
              ?>
            </select>
          </td>
          <td></td>
        </tr>
      </table>
    </fieldset>
    <fieldset id="ImageProperties">
        <legend>CAPTCHA Image Properties</legend>
        <table cellpadding="5" cellspacing="0" summary="CAPTCHA Properties layout table">
          <tr>
            <td class="left">
              <label for="ImageStyle"><span>Image Style:</span></label>
            </td>
            <td>
              <select name="ImageStyle" id="ImageStyle">
                <option value="default">[Default]</option>
                <?php
                $names = ImageStyle::$Names;
                asort($names);
                foreach ($names as $value => $name) { ?>
                  <option <?php if (isset($_POST['ImageStyle']) && 0 == strcmp($_POST['ImageStyle'], $value)) { echo 'selected="selected"'; } if (in_array($value, ImageStyle::$LicenseRestrictedStyles, false)) { echo 'disabled="disabled" '; } ?> value="<?php echo $value; ?>"><?php echo $name; ?></option>
                  <?php
                }
              ?>
              </select>
            </td>
            <td></td>
          </tr>
          <tr>
            <td class="left">
              <label for="CustomLightColor"><span>Custom Light Color:</span></label>
            </td>
            <td>
              <select name="CustomLightColor" id="CustomLightColor">
                <option value="">[Default]</option>
                <?php
                foreach (BDC_HtmlColor::$Names as $value => $name) { ?>
                  <option <?php if (!is_null($DemoCaptcha->CustomLightColor) && 0 == strcmp($DemoCaptcha->CustomLightColor->ToHexString(), $value)) { echo 'selected="selected"'; }?> value="<?php echo $value; ?>"><?php echo $name; ?></option>
                  <?php
                }
              ?>
              </select>
            </td>
            <td></td>
          </tr>
          <tr>
            <td class="left">
              <label for="CustomDarkColor"><span>Custom Dark Color:</span></label>
            </td>
            <td>
              <select name="CustomDarkColor" id="CustomDarkColor">
                <option value="">[Default]</option>
                <?php
                foreach (BDC_HtmlColor::$Names as $value => $name) { ?>
                  <option <?php if (!is_null($DemoCaptcha->CustomDarkColor) && 0 == strcmp($DemoCaptcha->CustomDarkColor->ToHexString(), $value)) { echo 'selected="selected"'; }?> value="<?php echo $value; ?>"><?php echo $name; ?></option>
                  <?php
                }
              ?>
              </select>
            </td>
            <td></td>
          </tr>
          <tr>
            <td class="left">
            <label for="ImageFormat"><span>Image Format:</span></label>
          </td>
          <td>
            <select name="ImageFormat" id="ImageFormat">
              <?php
                foreach (ImageFormat::$Names as $value => $name) { ?>
                  <option <?php if ($DemoCaptcha->ImageFormat == $value) { echo 'selected="selected"'; }?> value="<?php echo $value; ?>"><?php echo $name[0]; ?></option>
                  <?php
                }
              ?>
            </select>
          </td>
          <td></td>
          </tr>
          <tr>
            <td class="left">
              <label for="ImageWidth">Image Width:</label>
            </td>
            <td>
              <input name="ImageWidth" id="ImageWidth" type="text" class="textboxSmall" value="<?php echo $DemoCaptcha->ImageWidth ?>" />&nbsp;px
            </td>
            <td></td>
          </tr>
          <tr>
            <td class="left">
              <label for="ImageHeight">Image Height:</label>
            </td>
            <td>
              <input name="ImageHeight" id="ImageHeight" type="text" class="textboxSmall" value="<?php echo $DemoCaptcha->ImageHeight ?>" />&nbsp;px
            </td>
            <td></td>
          </tr>
          </table>
        </fieldset>
        <fieldset id="AudioProperties">
        <legend>CAPTCHA Audio Properties</legend>
        <table cellpadding="5" cellspacing="0" summary="CAPTCHA Properties layout table">
          <tr>
            <td class="left">
            <label for="SoundStyle"><span>Sound Style:</span></label>
          </td>
          <td>
            <select name="SoundStyle" id="SoundStyle">
              <option value="">[Default]</option>
              <?php
                foreach (SoundStyle::$Names as $value => $name) { ?>
                  <option <?php if (isset($_POST['SoundStyle']) && 0 == strcmp($_POST['SoundStyle'], $value)) { echo 'selected="selected"'; } if (in_array($value, SoundStyle::$LicenseRestrictedStyles, false)) { echo 'disabled="disabled" '; } ?> value="<?php echo $value; ?>"><?php echo $name; ?></option>
                  <?php
                }
              ?>
            </select>
          </td>
          <td></td>
          </tr>
          <tr>
            <td class="left">
            <label for="SoundFormat"><span>Sound Format:</span></label>
          </td>
          <td>
            <select name="SoundFormat" id="SoundFormat">
              <?php
                foreach (SoundFormat::$Names as $value => $name) { ?>
                  <option <?php if ($DemoCaptcha->SoundFormat == $value) { echo 'selected="selected"'; }?> value="<?php echo $value; ?>"><?php echo $name; ?></option>
                  <?php
                }
              ?>
            </select>
          </td>
          <td></td>
          </tr>
        </table>
    </fieldset>

    <div class="submit">
      <input type="submit" name="ApplyCaptchaSettings" value="Apply" id="ApplyButton" />
    </div>
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>Description</h3>
        <p>This demo allows you to easily experiment with various BotDetect parameters and their combinations, so you can see how powerful and customizable BotDetect Captcha is.</p>
        <p>Please note that values in brackets (such as <code>[Default]</code> and <code>[Random]</code>) are not actual parameter values you can use directly in your code.</p>
      </div>
      <div class="note" id="localization">
        <h3>Localization</h3>
        <p>BotDetect installations include pronunciation sound packages (required for localized Captcha sounds) only for North-American locales by deafult. The <code>Locale</code> drop-down list values relect this fact.</p>
        <p>You can specify any other locale string for the <code>Locale</code> parameter value (e.g. <code>"en-GB"</code>, <code>"ru"</code>, <code>"zh-Hans"</code>). However, not all character sets might be supported yet, and you will have to download the pronunciation sound package separately from our site when it's available.</p>
        <p>You can always see the full list of locales for which we support both Captcha images and Captcha sounds &ndash; and download the required pronunciation sound packages &ndash; at the <a rel="nofollow" href="http://captcha.com/captcha-localizations.html?utm_source=installation&amp;utm_medium=php&amp;utm_campaign=4.1.0" title="BotDetect 4 CAPTCHA Localizations">BotDetect 4 CAPTCHA localizations</a> page.</p>
      </div>
    </div>
    
    <div class="column">
      <?php if (Captcha::IsFree()) { ?>
      <div class="note warning">
        <h3>Free Version Limitations</h3>
        <ul>
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
