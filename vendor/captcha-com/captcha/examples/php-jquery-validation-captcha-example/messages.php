<?php session_start(); ?>
<?php require("botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Validation: PHP jQuery Validation CAPTCHA Code Example</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <div class="column">
    <h1>BotDetect PHP CAPTCHA Validation: <br /> PHP jQuery Validation CAPTCHA Code Example</h1>
    
    <h2>View Messages</h2>

    <?php
      $count = 0;
      foreach($_SESSION as $key => $val) {
        if (false !== strpos($key, "Message_") && isset($val)) {
          echo "<p class='message'>${val}</p>";
          $count++;
        }
      }
      
      if ($count == 0) {
        echo '<p class="message">No messages yet.</p>';
      }
    ?>
    
    <br />
    
    <p class="navigation"><a href="index.php">Add Message</a></p>
  </div>
  
  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to integrate BotDetect PHP CAPTCHA validation with <a target="_blank" href="http://jqueryvalidation.org/validate">jQuery Validation</a> client-side form validation.</p>
        <p>It uses the Captcha Form Example as a starting point, and adds client-side jQuery Validation rules for all form fields.</p>
        <p>Client-side validation is not secure by itself (it can be bypassed trivially), so the example also shows how the protected form action must always be secured by server-side CAPTCHA validation first, and use client-side validation only to improve the user experience.</p>
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
