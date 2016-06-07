<?php

$username = Config::get('constants.props.kw.username');
$password = Config::get('constants.props.kw.password');
$server = Config::get('constants.props.kw.server');
$callingId = Config::get('constants.props.kw.callingId');

$kwikTagAuth = $server . "Configuration/Auth/$callingId?userName=$username&password=$password";
$ch = curl_init();
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $kwikTagAuth] );


$result = curl_exec($ch);

$token = preg_replace("/^.*<Token>(.*)<\/Token>.*$/", "$1", $result);


$docLink = $server . "Document/DownloadImage/$callingId?securityToken=$token&barcode=$barcode&includePhysicalFile=false&userName=$username";

curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $docLink] );
$docData = curl_exec($ch);
if ($docData) {
	header("Content-Disposition: inline; filename=$barcode.pdf;");
	header("Content-type: application/pdf");
	echo $docData;
} else {
	echo "<html><body><h1>Invalid barcode</h1></body></html>";
}
curl_close($ch);
ob_flush();
flush();
