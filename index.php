<?php

	$serverlist = file_get_contents("https://launchpad.soapboxrace.world/servers?weblogin");
	$json_serverlist = json_decode($serverlist, true);

?>

<html lang="en" class="">
	<head>
		<title>SBRW - WebLogin</title>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<meta charset="UTF-8">
		<meta name="robots" content="noindex">

		<link rel="stylesheet" type="text/css" href="assets/main.css">
	</head>

	<body>
		<div class="form">

			<div class="server_reply" style="display: none;"></div>

			<select name='server'>
				<option value="" disabled selected>Select server</option>
				<?php foreach ($json_serverlist as $value): ?>
					<option value="<?=$value['id'];?>"><?=$value['name']?></option>
				<?php endforeach ?>
			</select>

			<form class="register-form">
				<input type="text" placeholder="email address">
				<input type="password" placeholder="password">
				<input type="password" placeholder="repeat password">
				<input type="text" placeholder="ticket (if any)">
				<button id="register">register</button>
				<p class="message">Already registered? <a href="#">Sign In</a></p>
			</form>

			<form class="login-form">
				<input id="email" type="text" placeholder="email address">
				<input id="password" type="password" placeholder="password">
				<button id="login">login</button>
				<p class="message">Not registered? <a href="javascript:alert('Soon')">Create an account</a></p>
			</form>
		</div>

		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			//$('.message a').click(function () {
			//	$('form').animate({ height: "toggle", opacity: "toggle" }, "slow");
			//});

			function isEmail(email) {
				var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				return regex.test(email);
			}

			jQuery("#login").click(function(e) {
				e.preventDefault();
				var allowLogin = true;
				jQuery(".server_reply").hide();

				var serverid = jQuery("select[name='server'] option:selected").attr('value');

				var email = jQuery(".login-form #email").val();
				var password = jQuery(".login-form #password").val();

				if(serverid == "") {
					jQuery("select[name='server']").css("border-color", "red");
					allowLogin = false;
				} else {
					jQuery("select[name='server']").css("border-color", "#d2d2d2");
				}

				if(!isEmail(email)) {
					jQuery('.login-form #email').css("border-color", "red");
					allowLogin = false;
				} else {
					jQuery('.login-form #email').css("border-color", "#d2d2d2");
				}

				if(password == "") {
					jQuery('.login-form #password').css("border-color", "red");
					allowLogin = false;
				} else {
					jQuery('.login-form #password').css("border-color", "#d2d2d2");
				}

				if(allowLogin == true) {
					jQuery.post("send_request.php", { server: serverid, email: email, password: password }, function( data ) {
						jQuery(".server_reply").show();

						if(data.error == true) {
							jQuery(".server_reply").html("<div class='alert alert-danger' style='margin: 0;'>" + data.message + "</div>");
						} else {
							var message = '<div class="alert alert-info"><b>INFO:</b> Please note to NOT create any shortcut on desktop using this commandline!</div>';
							message += "Please run your console in NFSW installation folder and type the following command:<br /><br />";
							message += '<div class="commands">';
							message += '$ nfsw.exe SBRW ' + data.serverip + ' ' + data.token + ' ' + data.userid;
							message += '</div>';
							message += '<br />';
							message += 'Or... if you use <b>MacOS</b> or <b>Linux</b>:<br /><br />';
							message += '<div class="commands">';
							message += '$ wine nfsw.exe SBRW ' + data.serverip + ' ' + data.token + ' ' + data.userid;
							message += '</div>';
							message += '<br />';
							message += 'Or... if you use <b>GameLauncherReborn</b>:<br /><br />';
							message += '<a class="launcher" href="nfswlaunch://auth/' + data.authcode + '">Launch GameLauncherReborn</a><br />';
							message += '</div>';

							jQuery(".server_reply").html(message);
							jQuery(".login-form").hide();
							jQuery("select[name='server']").hide();
							jQuery(".server_reply").css("border-bottom", "none");
							jQuery(".server_reply").css("margin-bottom", "0");
							jQuery(".server_reply").css("padding-bottom", "0");
						}

					}, "json");
				} else {
					jQuery(".server_reply").show();
					jQuery(".server_reply").html("<div class='alert alert-danger' style='margin: 0;'>There was an issue sending your datas, please check bellow for errors</div>");
				}
			});
		</script>
	</body>
</html>