<?php session_start(); ?>
<?php require("botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Options: Client-Side Workflow Settings Code Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Options: <br /> Client-Side Workflow Settings Code Example</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

      <?php // Adding BotDetect Captcha to the page
        $ClientSideEventsCaptcha = new Captcha("ClientSideEventsCaptcha");
        $ClientSideEventsCaptcha->UserInputID = "CaptchaCode";
        echo $ClientSideEventsCaptcha->Html();
      ?>

      <div class="validationDiv">
        <input name="CaptchaCode" type="text" id="CaptchaCode" />
        <input type="submit" name="ValidateCaptchaButton" id="ValidateCaptchaButton" value="Validate" onclick="startAsyncCaptchaValidation(); return false;" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $ClientSideEventsCaptcha->Validate();

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
    
    <h4>Custom BotDetect Client-Side Events Debug Log</h4>
    <div id="output"></div>
  </form>

  <script type="text/javascript">
    function log(text) {
      var output = document.getElementById('output');
      var line = document.createElement('pre');
      line.innerHTML = timestamp() + ' ' + text;
      output.insertBefore(line, output.firstChild);
    }
    
    function timestamp() {
      return new Date().toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
    }
    
    function format(url) {
      return url.replace(/^.*?\?/g, '').replace(/&/g, '\n  &');
    }

     BotDetect.RegisterCustomHandler('PostInit', function() { 
      log('PostInit \n  CaptchaId ' + this.Id + '\n  InstanceId ' + this.InstanceId); 
    });
    
    // custom javascript handler executed before Captcha sounds are played
    BotDetect.RegisterCustomHandler('PrePlaySound', function() { 
      log('PrePlaySound'); 
    });
    
    // custom javascript handler executed before Captcha images are reloaded
    BotDetect.RegisterCustomHandler('PreReloadImage', function() { 
      log('PreReloadImage\n  ' + format(this.Image.src) + '\n  AutoReload: ' + this.AutoReloading); 
    });
    
    // custom javascript handler executed after Captcha images are reloaded
    BotDetect.RegisterCustomHandler('PostReloadImage', function() { 
      log('PostReloadImage\n  ' + format(this.Image.src)); 
    });
    
     // register handlers for the four steps of the BotDetect Ajax validation workflow
    BotDetect.RegisterCustomHandler('PreAjaxValidate', function() { 
      log('PreAjaxValidate\n  ' + format(this.ValidationUrl + '&i=' + this.GetInputElement().value));
    });
    
    BotDetect.RegisterCustomHandler('AjaxValidationFailed', function() { 
      log('AjaxValidationFailed');
    });
    
    BotDetect.RegisterCustomHandler('AjaxValidationPassed', function() { 
      log('AjaxValidationPassed');
    });
    
    BotDetect.RegisterCustomHandler('AjaxValidationError', function() { 
      log('AjaxValidationError');
    });
    
    BotDetect.RegisterCustomHandler('OnHelpLinkClick', function() { 
      log('OnHelpLinkClick');
      this.FollowHelpLink = false; // abort help page opening
    });

    
    function startAsyncCaptchaValidation() {
      var input = document.getElementById('CaptchaCode');
      if (input && 'text' == input.type) {
        // call the BotDetect built-in client-side validation function
        // this function must be called after the Captcha is displayed on the page, otherwise the
        // client-side object won't be initialized
        input.Captcha.StartAjaxValidation();
      }
    }
  </script>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to use custom BotDetect client-side events to execute user-defined JavaScript code at various stages of the Captcha challenge workflow.</p>
        <p>Client-side Captcha object initialization, Captcha image reloading, Captcha sound playback, built-in Captcha Ajax validation, and Captcha help link clicks all have a number of related client-side "events" and hooks where user-defined client-side callbacks can be injected.</p>
         <p>User code can be associated with Captcha workflow events using the <code>BotDetect.RegisterCustomHandler()</code> function, as shown in the example JavaScript code.</p>
        <p>Loading the form will initialize the client-side <code>Captcha</code> object (created by the <code>BotDetect.Init()</code> JavaScript call included in Captcha markup), and result in the <code>PostInit</code> event.</p>
        <p>Clicking the Captcha sound icon will result in the <code>PrePlaySound</code> event before the audio elements are added to the page DOM. There is no <code>PostPlaySound</code> event since not all browsers allow user callbacks when browser sound playing finishes.</p>
        <p>Clicking the Captcha reload icon will result in <code>PreReloadImage</code> and <code>PostReloadImage</code> events, executed before and after the Http request loading the new Captcha image from the server.</p>
        <p>Clicking the Captcha image (i.e. the included Captcha help link) will result in the <code>OnHelpLinkClick</code> event.</p>
        <p>Typing in a Captcha code and clicking the <em>Validate</em> button will first result in the <code>PreAjaxValidate</code> event, and later in either <code>AjaxValidationFailed</code> or <code>AjaxValidationPassed</code> depending on whether the server responds that the typed-in Captcha code was correct or not. In case of Ajax asynchronous request errors, <code>AjaxValidationError</code> will be called.</p>
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