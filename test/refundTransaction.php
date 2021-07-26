<?php 
	include '../lib/Bkash.php';

	use Bkash\Urlbased\Bkash;

	$refund_transaction = new Bkash();

	$post_data = array();

	$post_data = [
		'paymentID' => "TR0011I81627298244871",
		'amount' => "15",
		'trxID' => "8GQ404UNS4",
		'sku' => "SK-M2323",
		'reason' => "Faulty"
	];

	echo $refund_transaction->refundTransaction($post_data);
?>