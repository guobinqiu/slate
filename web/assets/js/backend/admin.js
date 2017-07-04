$(document).ready(function() {
  $("#select").on('click', function() {
    window.location.href = Routing.generate("admin_user")+"?user_id=" + $('#user_id').val() + "&email=" + $('#email').val() + "&nick=" + $('#nick').val();
  });
  $("#edit").on('click', function() {
    window.location.href = Routing.generate("_admin_member_edit")+"?user_id=" + $('#id').val();
  });
  $('#sendConfirmationEmail').on('click', function() {
    $.ajax({
      type: 'GET',
      url: Routing.generate("admin_user_send_confirmation_email"),
      data: { 'email': $.trim($("#user_email").text()) }, 
      dataType: 'JSON',
      context: this,
    }).done(function(data) {
      $(this).val(data.message).attr("disabled", true);
    });
  });
});
