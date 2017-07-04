<?php 
  // An array of validation responses
  $validationResult = array();
  
  // Type of request we are interested in. $_POST, $_GET or both - $_REQUEST
  $request = $_REQUEST;
  foreach($request as $formItem => $value) {
    $validationMethod = "Validate" . ucfirst($formItem);
    if (is_callable($validationMethod)) {
      // This is an array of validation result arrays for each field
      // see setElementValidationResponse() for more details.
      $validationResult[$formItem] = call_user_func($validationMethod, $value);
    }
  }

  // Total form validation result
  $isFormValid = true;
  foreach($validationResult as $formItem){
    $isFormValid = $isFormValid && $formItem['isValid']; 
  }

  // Respond according to results and request type
  
  // Regular response to POST form submission
  if (key_exists("SubmitButton", $request)) {
    // We want to make sure our Captcha is solved before continuing
    if (!$isFormValid || !$ContactCaptcha->IsSolved) { 
      // Form validation failed, show our error message
      $validationResult['Form'] = setElementValidationResult(false, null, "Please review your input.");
    } else {
      // We send the message with content from the validator
      // Additional sanitization should be implemented along with the validation 
      $isSent = sendMessage($validationResult['Name']['validContent'], $validationResult['Email']['validContent'], $validationResult['Message']['validContent']);
      if ($isSent === true) {
        // each message requires a new Captcha challenge
        $ContactCaptcha->Reset();
        $validationResult['Form'] = setElementValidationResult(true, null, 'Your message was sent.');
      } else {
        $validationResult['Form'] = setElementValidationResult(false, null, "The server had problems sending your message.");   
      }
    }
  }
  
  // JSON response when AJAX parameter is passed
  if (isset($_GET['AJAX']) && $_GET['AJAX'] == 1) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    
    echo json_encode($validationResult);
    // Terminate further output after JSON
    exit;
  }

  // we are processing a valid submited form
  function sendMessage($name, $email, $message) {
    return true;

    // TODO: send email only after configuring your email server settings
    /*
    $to = "YOUR_EMAIL_HERE";    
    $subject = "Message from your website"; 
        
    $headers = 'From: ' . $email;
    $headers .= '\r\n\'Reply-To: ' . $email;
    $headers .= '\r\n\'X-Mailer: PHP/' . phpversion();
    
    $body = $name . " (" . $email . ") sent the following message:\n" . $message;
    $body = wordwrap($body, 70);
    
    return mail($to, $subject, $body, $headers);
    */
  }
  
  // form a proper validation response to be assigned to a element
  // validation response to the field is an array of following:
  // "isValid" - status
  // "validContent" - optionaly filtered content
  // "validationMessage" - message for the user
  // Note that both valid and non-valid messages can be supplied.
  // This array format is a convention between both serverside methods
  // and clientside JS methods.
  function setElementValidationResult($status, $validContent = null, $validationMessage = null) {
    $result['isValid'] = $status;
    if ($status && $validContent) $result['validContent'] = $validContent;
    if ($validationMessage) $result['validationMessage'] = $validationMessage;

    return $result;
  }

  // validate the Captcha
  function ValidateCaptchaCode($code) {
    global $ContactCaptcha;
    // We want to check if the user already solved the Captcha for this message
    $isHuman = $ContactCaptcha->IsSolved;
    if (!$isHuman) {
      // Validate the captcha
      // Both the user entered $code and $instanceId are used.
      
      global $request;
      if (array_key_exists('CaptchaInstanceId', $request)) { // ajax validation of Captcha input only
        $instanceId = $request["CaptchaInstanceId"];
        $isHuman = $ContactCaptcha->AjaxValidate($code, $instanceId);
      } else { // regular full form post validation of all fields
        $isHuman = $ContactCaptcha->Validate($code);
      }
    }
    if ($isHuman === true) {
      return setElementValidationResult(true, null, "Solved!");
    } else {
      return setElementValidationResult(false, null, "Please retype the code.");  
    }
  }
  
  // name validation
  function ValidateName($name) {
    $name = stripcslashes($name);
    if (strlen($name) > 2 && strlen($name) < 30) {
      return setElementValidationResult(true, $name, "Ok!");
    } else {
      return setElementValidationResult(false, $name, "Please enter your name.");
    };
  }
  
  // email validaton
  function ValidateEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return setElementValidationResult(true, $email, "Ok!");
    } else {
      return setElementValidationResult(false, $email, "This email is not valid.");
    }
  }
  
  // message validation
  function ValidateMessage($message) {
    $message = stripcslashes($message);
    $headerInjection = preg_match("/(bcc:|cc:|content\-type:)/i", $message);
    if (strlen($message) > 2 && strlen($message) < 255 && !$headerInjection) {
      return setElementValidationResult(true, $message, "Ok!");
    } else {
      return setElementValidationResult(false, $message, "Please enter your message.");
    }
  }
  
  
  // remember user input if validation fails
  function getValue($fieldName) {
    $value = '';
    if (isset($_REQUEST[$fieldName])) { 
      $value = $_REQUEST[$fieldName];
    }
    return $value;
  }
  

  // Validation message helper function used in HTML
  // the validation result is stored in a global array
  function showValidationMessage($element) {
    global $validationResult;

    $message = "";
    $messageClass[] = "validatorMessage";
    
    if (is_array($validationResult) && array_key_exists($element, $validationResult)) {
      $elementStatus = $validationResult[$element];
      
      if ($elementStatus['isValid'] == false) {
        $messageClass[] = "incorrect";
      } elseif ($elementStatus['isValid'] == true) {
        $messageClass[] = "correct";
      }
    
      if (array_key_exists('validationMessage', $elementStatus) && $elementStatus['validationMessage'] != null) {
        $message = $elementStatus['validationMessage'];
      }
    }
        
    $validator = $element . "ValidatorMessage";
    $messageClass = implode(" ", $messageClass);
    
    $messageHtml = '<span class="' . $messageClass . '" id="' . $validator . '">' . $message . '</span>';
    
    return $messageHtml;
  }
?>