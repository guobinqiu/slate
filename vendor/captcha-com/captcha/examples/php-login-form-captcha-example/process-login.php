<?php 
  session_start(); 
  require("botdetect.php"); 

  $form_page = "index.php";
	
	// directly accessing this script is an error
	if (!$_SERVER['REQUEST_METHOD'] == "POST") {
    header("Location: ${form_page}");
    exit;
	}

	// sumbitted login data
	$username = $_REQUEST['Username'];
	$password = $_REQUEST['Password'];
	
  
  // CAPTCHA user input validation 
  $LoginCaptcha = new Captcha('LoginCaptcha');
	$LoginCaptcha->UserInputID = 'CaptchaCode';
	if (!$LoginCaptcha->IsSolved) {
		$isHuman = $LoginCaptcha->Validate();
    if (!$isHuman) {
      // CAPTCHA validation failed, show error message
      $form_page = $form_page . '?Username=' . urlencode($username) . "&error=Captcha";
			header("Location: ${form_page}");
      exit;
    }
	}
	
	// CAPTCHA validation passed, only now do we perform the protected action (try to authenticate the user)

	// check login format
	$isValidLogin = ValidateLogin($username, $password);
	if (!$isValidLogin) {
		// invalid login format, show error message
		$form_page = $form_page . '?Username=' . urlencode($username) . "&error=Format";
    header("Location: ${form_page}");
    exit;
	}
	
	// authenticate the user
	$isAuthenticated = Authenticate($username, $password);
	if (!$isAuthenticated) {
    // failing authentication 3 times shows the Captcha again
    $count = 0;
    if (isset($_SESSION['FailedAuthCount'])) {
      $count = (int) $_SESSION['FailedAuthCount'];
    }
    $count++;
    if ($count > 2) {
      $LoginCaptcha->Reset();
      $count = 0;
    }
    $_SESSION['FailedAuthCount'] = $count;
  
		// authentication attempt failed, show error message
		$form_page = $form_page . '?Username=' . urlencode($username) . "&error=Auth";
    header("Location: ${form_page}");
    exit;
	}

	
	function ValidateLogin($p_Username, $p_Password) {
	  $result = false;
		// we check both username and password are specified and alphanumeric
		if (strlen($p_Username) > 0 && strlen($p_Password) > 0) {
      $pattern = '/^[a-zA-Z0-9_]+$/'; // alphanumeric chars and underscores only
		  $result = (1 == preg_match($pattern, $p_Username));
      $result &= (1 == preg_match($pattern, $p_Password));
		}
	  return $result;
	}
	
	function Authenticate($p_Username, $p_Password) {
	  $result = false;
		// Since this is a simple example project, we consider all authentication attempts with usernames and 
		// passwords longer than 5 characters valid instead of looking up the info in a database etc.
		if (strlen($p_Username) > 4 && strlen($p_Password) > 4) {
		  $result = true;
		}
	  return $result;
	}
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Validation: PHP Login Form CAPTCHA Code Example</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <div class="column">
    <h1>BotDetect PHP CAPTCHA Validation: <br /> PHP Login Form CAPTCHA Code Example</h1>
    
    <h2>Protected Page</h2>

    <fieldset id="Properties">
      <legend>Validation passed!</legend>
      
      <div class="input">
        <label for="Username">Username:</label>
        <input name="Username" id="Username" type="text" class="textbox" readonly="readonly" value="<?php echo urlencode($username); ?>" />
      </div>
      
      <div class="input">
        <label for="Password">Password:</label>
        <input name="Password" id="Password" type="text" class="textbox" readonly="readonly" value="<?php echo urlencode($password); ?>" />
      </div>
      
      <p class="navigation">
        <?php // Example only, we want to show the Captcha again when returning to the form
          $LoginCaptcha->Reset() ?>
        <a href="index.php">Back to login page</a>
      </p>
    </fieldset>
  </div>

	<div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to add BotDetect CAPTCHA validation to simple PHP login forms.</p>
        <p>To prevent bots from trying to guess the login info by brute force submission of a large number of common values, the visitor first has to prove they are human (by solving the CAPTCHA), and only then is their username and password submission checked against the authentication data store.</p>
        <p>Also, if they enter an invalid username + password combination three times, they have to solve the CAPTCHA again. This prevents attempts in which the attacker would first solve the CAPTCHA themselves, and then let a bot brute-force the authentication info.</p>
        <p>To keep the example code simple, the example doesn't access a data store to authenticate the user, but <strong>accepts all logins with usernames and passwords at least 5 characters long as valid</strong>.</p>
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