<?php 
	include '../lib/Bkash.php';

	use Bkash\Urlbased\Bkash;

	$refund_query = new Bkash();

	$post_data = array();

	$post_data = [
		'paymentID' => "TR0011I81627298244871",
		'trxID' => "8GQ404UNS4"
	];

	echo $refund_query->refundStatus($post_data);

?>