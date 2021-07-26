<?php 
	include '../lib/Bkash.php';

	use Bkash\Urlbased\Bkash;

	$token = new Bkash();

	echo "<pre>";
	print_r($token);

?>