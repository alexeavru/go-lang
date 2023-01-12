<?php

function curl_get($url, $referer = 'http://www.google.com') {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;

}


?>