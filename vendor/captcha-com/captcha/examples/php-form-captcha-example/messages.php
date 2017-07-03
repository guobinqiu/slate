<?php session_start(); ?>
<?php require("botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Validation: PHP Form CAPTCHA Code Example</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <div class="column">
    <h1>BotDetect CAPTCHA PHP Form Example</h1>
    
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
        <p>This BotDetect Captcha PHP code example shows how to add BotDetect CAPTCHA protection to a typical PHP form.</p>
        <p>Captcha validation is integrated with other form fields validation, and only submissions that meet all validation criteria are accepted.</p>
        <p>If the Captcha is sucessfully solved but other field validation fails, the Captcha is hidden since the users have already proven they are human.</p>
        <p>This kind of validation could be used on various types of public forms which accept messages, and are at risk of unwanted automated submissions.</p>
        <p>For example, it could be used to ensure bots can't submit anything to a contact form, add guestbook entries, blog post comments or anonymous message board / forum replies.</p>
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
