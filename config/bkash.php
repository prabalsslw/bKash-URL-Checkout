<?php 
	
	return [
		"callbackUrl" => "http://localhost/bkash/url_checkout/test/callBack.php",
		"sandboxBaseUrl" => "https://tokenized.sandbox.bka.sh/v1.2.0-beta",
		"sliveBaseUrl" => "",
		"grantTokenUrl" => "/tokenized/checkout/token/grant",
		"refreshTokenUrl" => "/tokenized/checkout/token/refresh",
		"createPaymentUrl" => "/tokenized/checkout/create",
		"executePaymentUrl" => "/tokenized/checkout/execute",
		"capturePaymentUrl" => "",
		"voidUrl" => "",
		"queryUrl" => "/tokenized/checkout/payment/status",
		"refundUrl" => "/tokenized/checkout/payment/refund",
		"refundStatusUrl" => "/tokenized/checkout/payment/refund",
		"searchTranUrl" => "/tokenized/checkout/general/searchTran",
		"app_key" => "7epj60ddf7id0chhcm3vkejtab",
		"app_secret" => "18mvi27h9l38dtdv110rq5g603blk0fhh5hg46gfb27cp2rbs66f",
		"username" => "sandboxTokenizedUser01",
		"password" => "sandboxTokenizedUser12345",
		"proxy" => "",
		"is_sandbox" => true, 	# true - sandbox, false - live
		"is_capture" => false 	# true - authorization, false - sale
	];