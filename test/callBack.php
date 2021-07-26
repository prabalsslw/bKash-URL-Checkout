<?php
	include '../lib/Bkash.php';

	use Bkash\Urlbased\Bkash;

	$execute_payment = new Bkash();

	$status = $_GET['status'];
	$payment_id = $_GET['paymentID'];

	if($status == 'success' && !empty($payment_id))
	{
		$response = $execute_payment->executePayment($payment_id);
		$execute_response = json_decode($response, true);

		if(!empty($execute_response['paymentID']) && !empty($execute_response['trxID']) && !empty($execute_response['transactionStatus']) && $execute_response['transactionStatus'] == "Completed")
		{
			echo "Execute Success<pre>";
			print_r($execute_response);
		}
		else if(!empty($execute_response['libMsg']))
		{
			echo $execute_response['libMsg'];
		}
		else 
		{
			$queryPayment = $execute_payment->queryPayment($payment_id);
			$query_payment = json_decode($queryPayment, true);
			echo "Query API Response<pre>";
			print_r($query_payment);
		}
	}
	else if($status == 'failure' && !empty($payment_id))
	{
		echo "<pre>";
		print_r($_GET);
	}
	else if($status == 'cancel' && !empty($payment_id))
	{
		echo "<pre>";
		print_r($_GET);
	}
?>