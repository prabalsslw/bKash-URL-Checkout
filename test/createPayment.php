<?php 
	include '../lib/Bkash.php';

	use Bkash\Urlbased\Bkash;

	$create_payment = new Bkash();

	$post_data = array();

	$post_data = [
		'amount' => 15,
		'merchantInvoiceNumber' => strtoupper(uniqid()),
		'payerReference' => '01770618575'
	];

	echo $create_payment->createPayment($post_data);

?>