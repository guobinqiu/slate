// Define the name of the Captcha field.
// It serves to access BotDetect Captcha client-side API later.
// http://captcha.com/doc/php/api/captcha-client-side-reference.html
var captchaUserInputId = "CaptchaCode";

$(document).ready(function() {
  // AJAX argument is added to differentiate from regular POST.
  var validationUrl = "index.php?AJAX=1"
  
  // Collect form elements we want to handle.
  var formElements = $('#contactForm input, #contactForm textarea');
  var form = $('#contactForm');

  formElements.blur(
    function(){
      var postData = {};
      // Additional check to skip over empty fields
      // This igores non-relevant triggering of onBlur
      if (this.value != ''){
        postData[this.id] = this.value;
      }

      if(this.id == captchaUserInputId){
        // In case of our Captcha field, we also send the InstanceId
        captchaUserInputField = $('#' + captchaUserInputId).get(0);
        postData["CaptchaInstanceId"] = captchaUserInputField.Captcha.InstanceId;
      }
      
      if(this.id == "SubmitButton"){
        return false;
      }

      $.post(validationUrl, postData, postValidation);
    }
  );
  
  form.submit(
    function(){
      var postData = {}
      formElements.each(
        function(){
        postData[this.id] = this.value;
        }
      );
      $.post(validationUrl, postData, postValidation);
      return false;
    }
   );
  
});

function postValidation(data){

  if (data[captchaUserInputId]){

    // Get the Captcha instance, as per client side API
    captcha = $('#' + captchaUserInputId).get(0).Captcha;

    if(!data[captchaUserInputId]["isValid"]){
      // We want to get another image if the Captcha validation failed.
      // User gets one try per image.
      captcha.ReloadImage();
    }
  }

  if (data["Form"] && data["Form"]["isValid"]){
    $("#SubmitButton").attr("disabled", "disabled");
  }
  updateValidatorMessages(data);
}

// Handling the display of validation messages
function updateValidatorMessages(data){
  for(var elementKey in data){
    validatedElement = data[elementKey];

    var elementValidatorMessage = $("#" + elementKey + "ValidatorMessage");
    
    if(validatedElement.hasOwnProperty("validationMessage")){
      elementValidatorMessage.text(validatedElement["validationMessage"]);
    }else{
      elementValidatorMessage.empty();
    }
    
    if(validatedElement["isValid"]){
      elementValidatorMessage.toggleClass("correct", true);
      elementValidatorMessage.toggleClass("incorrect", false);
    }else{
      elementValidatorMessage.toggleClass("correct", false);
      elementValidatorMessage.toggleClass("incorrect", true); 
    }
  }
}
