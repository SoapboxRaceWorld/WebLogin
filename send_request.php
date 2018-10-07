<?php
	header('Content-Type: application/json');

	$server = $_POST['server'];
	$email = $_POST['email'];
	$password = sha1($_POST['password']);

	//Let's validate server
	$serverlist = file_get_contents("https://launchpad.soapboxrace.world/servers?weblogin");
	$json_serverlist = json_decode($serverlist, true);

	foreach($json_serverlist as $value) {
		$servers[$value['id']] = $value['ip_address'];
	}

	if($servers[$server] == NULL) die(json_encode(array("error" => true, "message" => "Unknown server")));


	$opts = array(
		'http' => array(
			'header' => "User-agent: GameLauncher (+https://github.com/SoapboxRaceWorld/GameLauncher_NFSW)\r\nX-HWID: WebLogin\r\n",
			'ignore_errors' => '1'
		)
	);
	$context = stream_context_create($opts);


	$file = file_get_contents($servers[$server]."/User/authenticateUser?email=".$email."&password=".$password, false, $context);
	$xml = simplexml_load_string($file);
	$xml = json_decode(json_encode($xml), TRUE);

	if($xml['Description'] != NULL) {
		die(json_encode(array("error" => true, "message" => $xml['Description'])));
	} elseif($xml['Ban']['Reason'] != NULL) {
		die(json_encode(array("error" => true, "message" => "This user is banned until ".$xml['Ban']['Expires'].".<br /> <b>Reason:</b> ".$xml['Ban']['Reason'])));
	} else {
		die(json_encode(array("error" => false, "serverip" => $servers[$server], "token" => $xml['LoginToken'], "userid" => $xml['UserId'], "fullreply" => $xml, "authcode" => base64_encode($xml['UserId']."__".$xml['LoginToken']."__".$servers[$server]))));
	}
?>