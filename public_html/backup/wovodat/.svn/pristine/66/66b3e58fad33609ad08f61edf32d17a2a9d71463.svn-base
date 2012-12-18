<?php
@session_start();
// If session was already started
if (!isset($_SESSION['dev_login']) || !($_SESSION['dev_login'])) {
?>

<script type="text/javascript">
function submitLogin() {
	var uname = $("#uname").val();
	var password = $("#password").val();
	$.ajax({
	  method: "get", url: "verifyuser.php", data: "uname="+ uname +"&password="+ password,
	  beforeSend: function(){$(".validateTips").html("Sending");},
	  complete: function(){},
	  success: function(html){
		$(".validateTips").html(html);
		if (html == "Login Success!") {
			setTimeout("$(\"#dialog-form\").dialog(\"close\"); $(\"#wrap\").show();", 500);
		}
	  }
	});
}

$(function() {
	$("#wrap").hide();
    $("#dialog").dialog("destroy");
	$('#dialog-form').dialog({
			resizable: false,
			height: 330,
			width: 350,
			modal: true,
			buttons: {
				'Login': function() {
					submitLogin();
				}
			},
			close: function() {
			}
		});
		
		});
		</script>

<div id="dialog-form" title="Please Login First">
	<p class="validateTips">All form fields are required.</p>

	<form>
	<fieldset>
		<label for="name">Username:</label>
		<input type="text" name="uname" id="uname" class="text ui-widget-content ui-corner-all" />
		<label for="password">Password:</label>
		<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	</form>
</div>
<?php } ?>