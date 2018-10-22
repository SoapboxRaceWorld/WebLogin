<?php

	header('Content-Type: application/json');

	$server = $_GET['server'];

	//Let's validate server
	$serverlist = file_get_contents("https://launchpad.soapboxrace.world/servers?weblogin");
	$json_serverlist = json_decode($serverlist, true);

	foreach($json_serverlist as $value) {
		$servers[$value['id']] = $value['ip_address'];
	}

	if($servers[$server] == NULL) die(json_encode(array("error" => true, "message" => "Unknown server")));
	$file = file_get_contents($servers[$server]."/GetServerInformation", false, $context);

	die(json_encode(array("error" => false, "message" => json_decode($file))));

