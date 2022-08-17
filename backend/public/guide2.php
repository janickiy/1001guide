<?php

$base = "https://api.getyourguide.com/1/";
$url = "tours?cnt_language=en&currency=eur&q=".urlencode("paris");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base.$url);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = [
	'X-ACCESS-TOKEN: dsepUDtYXmUEc824oXfMKXjPdrwqRLgogOZEQU6s3kjUziuU',
	'Accept: application/json'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$server_output = curl_exec ($ch);

curl_close ($ch);

print  $server_output ;