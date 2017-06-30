$(document).ready(function() {
  $("#form1").validate({
    rules: {
      Name: { required: true, minlength: 3 },
      Email: { required: true, email: true },
      Message: { required: true, minlength: 10 },
      // Captcha code is a required input, and its value is validated remotely
      // the remote validation Url is exposed through the BotDetect client-side API
      CaptchaCode: { required: true, remote: $("#CaptchaCode").get(0).Captcha.ValidationUrl }
    },
    messages: {
      Name: {
        required: "A name is required",
        minlength: jQuery.format("Enter at least {0} characters")
      },
      Email: {
        required: "An email address is required",
        email: "Please enter a valid email address"
      },
      Message: {
        required: "A message is required",
        minlength: jQuery.format("Enter at least {0} characters")
      },
      // error messages for Captcha code validation 
      CaptchaCode: {
        required: "The Captcha code is required",
        remote: "The Captcha code must be retyped correctly"
      }
    },
    // the Captcha input must only be validated when the whole code string is
    // typed in, not after each individual character (onkeyup must be false)
    onkeyup: false,
    // validate user input when the element loses focus
    onfocusout: function(element) { $(element).valid(); },
    // reload the Captcha image if remote validation failed
    showErrors: function(errorMap, errorList) {
      if (typeof(errorMap.CaptchaCode) != "undefined" &&
          errorMap.CaptchaCode === this.settings.messages.CaptchaCode.remote) {
        $("#CaptchaCode").get(0).Captcha.ReloadImage();
      }
      this.defaultShowErrors();
    },
    success: function(label) {
      label.text("Ok!");
      label.addClass(this.validClass);
    },
    errorClass: "incorrect",
    validClass: "correct",
    errorElement: "label"
  });
});