<?php
  // PHP v5.2.0+ required
  session_start();

  // include BotDetect Captcha library files
  require("botdetect.php");

  // create & configure the Captcha object
  $ContactCaptcha = new Captcha("ContactCaptcha");
  $ContactCaptcha->UserInputID = "CaptchaCode";
  $ContactCaptcha->CodeLength = 3;
  $ContactCaptcha->ImageWidth = 150;
  $ContactCaptcha->ImageStyle = ImageStyle::Graffiti2;
  
  require("process-form.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Validation: PHP jQuery AJAX Contact Form CAPTCHA Code Example</title>
  <!-- include the captcha stylesheet -->
  <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="js/validation.js"></script>
</head>
<body>
  <form method="post" action="index.php" class="column" id="contactForm" name="contactForm">

    <h1>BotDetect PHP CAPTCHA Validation: <br /> PHP jQuery AJAX Contact Form CAPTCHA Code Example</h1>

    <fieldset>
      <legend>Contact Form</legend>
      
      <?php echo showValidationMessage("Form"); ?>

      <div class="input">
        <label for="Name">Name:</label>
        <input type="text" name="Name" id="Name" class="textbox" value="<?php echo getValue('Name');?>" />
        <?php // name validation failed, show error message
        echo showValidationMessage("Name");
          ?>
      </div>
      
      <div class="input">
        <label for="Email">Email:</label>
        <input type="text" name="Email" id="Email" class="textbox" value="<?php echo getValue('Email');?>" />

        <?php // email validation failed, show error message
        echo showValidationMessage("Email");
        ?>

      </div>
      
      <div class="input">
        <label for="Message">Short message:</label>
        <textarea class="inputbox" id="Message" name="Message" rows="5" cols="40"><?php echo getValue('Message');?></textarea>
        <br />
        <?php // message validation failed, show error message
        echo showValidationMessage("Message");
        ?>
        
      </div>


      <div class="input">
      <?php
       
      // only show the Captcha if it hasn't been already solved for the current message
      if(!$ContactCaptcha->IsSolved) { ?>
        <label for="CaptchaCode">Retype the characters from the picture:</label>

        <?php echo $ContactCaptcha->Html(); ?>
        <input type="text" name="CaptchaCode" id="CaptchaCode" class="textbox" />

        <?php // Captcha validation failed, show error message
        echo showValidationMessage("CaptchaCode");
      }?>
      </div>

      <input type="submit" name="SubmitButton" id="SubmitButton" value="Submit" />

    </fieldset>
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example demonstrates the use of BotDetect Captcha in a scenario using jQuery and AJAX to validate individual form fields against a PHP server backend. The approach in this scenario is useful in situations where duplication of server-side validation routines on the client side is impractical.</p>
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
