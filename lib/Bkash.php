<?php 
	namespace Bkash\Urlbased;

	require_once(__DIR__."/BkashAbstract.php");

	class Bkash extends BkashAbstract
	{
	    protected $secretdata = [];
	    protected $data = [];
	    protected $config = [];

		public function __construct() {

	        $this->config = include(__DIR__.'/../config/bkash.php');
	        date_default_timezone_set('Asia/Dhaka');

	        $this->setAppkey($this->config['app_key']);
	        $this->setAppsecret($this->config['app_secret']);
	        $this->setUsername($this->config['username']);
	        $this->setPassword($this->config['password']);
	        $this->setCallbackUrl($this->config['callbackUrl']);

	        if($this->config['is_sandbox']) {
	        	$this->setEnv($this->config['sandboxBaseUrl']);
	        } else {
	        	$this->setEnv($this->config['sliveBaseUrl']);
	        }

	        if($this->config['is_capture']) {
	        	$this->setCapture('authorization');
	        } else {
	        	$this->setCapture('sale');
	        }
	        $token_api_response = json_decode($this->grantToken(), true);
	       	$this->setToken($token_api_response['apiResponse']['id_token']);
	    }

	    public function grantToken() {
	    	$this->secretdata['app_key'] = $this->getAppkey();
	    	$this->secretdata['app_secret'] = $this->getAppsecret();
	    	$this->setApiurl($this->getEnv().$this->config['grantTokenUrl']);

	    	$header = [
				'Content-Type:application/json',
				'password:'.$this->getPassword(),                                                               
		        'username:'.$this->getUsername()                                                          
		    ];	
		    if (!file_exists("../config/token.json")) {
		    	$response = $this->Post($this->secretdata, $header);
		    	$token_response = json_decode($response, true);

		    	if(isset($token_response['id_token']) && $token_response['id_token'] != "") {

		    		$token_creation_time = date('Y-m-d H:i:s');
		    		$json_token = json_encode(['id_token' => $token_response['id_token'], 'refresh_token' => $token_response['refresh_token'] ,'created_time' => $token_creation_time], JSON_PRETTY_PRINT);

					file_put_contents("../config/token.json", $json_token);

					return json_encode(['libMsg' => 'Token file created successfully', "apiResponse" => $token_response]);
			    }
			    else {
			    	return json_encode(['libMsg' => 'Error in token creation!']);
			    }
			}
			else if(file_exists("../config/token.json")) {
				$previous_token = json_decode(file_get_contents("../config/token.json"), true);

				$token_creation_time = date('Y-m-d H:i:s');
				$token_start_time = new \DateTime($previous_token['created_time']);
				$token_end_time = $token_start_time->diff(new \DateTime($token_creation_time));

				if($token_end_time->days > 0 || $token_end_time->d > 0 || $token_end_time->h > 0 || $token_end_time->i > 58) 
				{
					$refresh_token_response = json_decode($this->refreshToken($previous_token['refresh_token']), true);
	
					if(isset($refresh_token_response['id_token']) && $refresh_token_response['id_token'] != "") {
						$retoken_creation_time = date('Y-m-d H:i:s');
			    		$rejson_token = json_encode(['id_token' => $refresh_token_response['id_token'], 'refresh_token' => $refresh_token_response['refresh_token'] ,'created_time' => $retoken_creation_time], JSON_PRETTY_PRINT);

						file_put_contents("../config/token.json", $rejson_token);

						return json_encode(["libMsg" => "Refresh token file created successfully", "apiResponse" => $refresh_token_response]);
					}
					else if(!empty($refresh_token_response['errorCode']))
					{
						return json_encode(["libMsg" => "Error Occurred", "apiResponse" => $refresh_token_response]);
					}
				}
				else {
					return json_encode(['libMsg' => 'Previous token not expired yet!', "apiResponse" => $previous_token]);
				}
			}
	    }

	    public function refreshToken($refresh_token_id) {
	    	$this->secretdata['app_key'] = $this->getAppkey();
	    	$this->secretdata['app_secret'] = $this->getAppsecret();
	    	$this->secretdata['refresh_token'] = $refresh_token_id;
	    	$this->setApiurl($this->getEnv().$this->config['refreshTokenUrl']);

	    	$header = [
				'Content-Type:application/json',
				'password:'.$this->getPassword(),                                                               
		        'username:'.$this->getUsername()                                                          
		    ];	

	    	$response = $this->Post($this->secretdata, $header);
	    	return $response;
	    }

	    public function createPayment($postdata) {
	    	$this->readyParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['createPaymentUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);
		    $status = json_decode($response, true);

		    if(isset($status['transactionStatus']) && $status['transactionStatus'] == "Initiated") {
				$this->redirect($status['bkashURL']);
		    } 
		    else if(isset($status['errorCode']) && !empty($status['errorCode'])) {
		    	return json_encode(["libMsg" => "Unable to create payment", "apiResponse" => $status]);
		    }
		    else {
		    	echo "Unable to create Bkash URL!";
		    }
	    }

	    public function executePayment($payment_id) {
	    	$this->setApiurl($this->getEnv().$this->config['executePaymentUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $this->data['paymentID'] = $payment_id;

		    $response = $this->Post($this->data, $header);
		    $status = json_decode($response, true);

		    if(isset($status['transactionStatus']) && ($status['transactionStatus'] == "Completed" || $status['transactionStatus'] == "Authorized")) {
				return $response;
		    } 
		    else if(isset($status['statusMessage'])) {
		    	return json_encode(["libMsg" => $status['statusMessage']]);
		    }
		    else if(isset($status['errorCode']) && !empty($status['errorCode'])) {
		    	return json_encode(["libMsg" => "Unable to execute payment", "apiResponse" => $status]);
		    }
		    else {
		    	return $response;
		    }
	    }

	    public function queryPayment($payment_id) {
	    	$this->setApiurl($this->getEnv().$this->config['queryUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $this->data['paymentID'] = $payment_id;

		    $response = $this->Post($this->data, $header);
		    $query_response = json_decode($response, true);

		    if((isset($query_response['paymentID']) && $query_response['paymentID'] != "") && (isset($query_response['trxID']) && $query_response['trxID'] != "")) {
		    	return $response;
		    }
		    else if(isset($query_response['errorCode']) && $query_response['errorCode'] != "") {
		    	return json_encode(["libMsg" => "Query Error", "apiResponse" => $query_response]);
		    }
		    else {
		    	echo "Somthing Wrong!";
		    }
	    	
	    }

	    public function searchTransaction($trxid) {
	    	$this->setApiurl($this->getEnv().$this->config['searchTranUrl'].$trxid);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Get($header);

	    	return $response;
	    }

	    public function refundTransaction($postdata) {
	    	$this->readyRefundParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['refundUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);

		    $refund_response = json_decode($response, true);

		    if((isset($refund_response['transactionStatus']) && $refund_response['transactionStatus'] != "") && (isset($refund_response['originalTrxID']) && $refund_response['originalTrxID'] != "")) {
		    	return $response;
		    }
		    else if(isset($refund_response['errorCode']) && $refund_response['errorCode'] != "") {
		    	return json_encode(["libMsg" => "Refund Initiation Failed", "apiResponse" => $refund_response]);
		    }
		    else {
		    	echo "Somthing Wrong!";
		    }
	    }

	    public function refundStatus($postdata) {
	    	$this->readyRefundStatusParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['refundStatusUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);
		    $refund_query_response = json_decode($response, true);

		    if(isset($refund_query_response['errorCode']) && $refund_query_response['errorCode'] != "") {
		    	return json_encode(["libMsg" => "Refund Initiation Failed", "apiResponse" => $refund_query_response]);
		    }
		    else {
		    	return $response;
		    }
	    }

	    public function capturePayment($payment_id) {
	    	if($this->config['is_capture']) {
	    		$this->setApiurl($this->getEnv().$this->config['capturePaymentUrl'].$payment_id);

		    	$header = [ 
			        'Content-Type:application/json',
			        'authorization:'.$this->getToken(),
			        'x-app-key:'.$this->getAppkey()                                                   
			    ];

			    $response = $this->Post("", $header);
			    $status = json_decode($response, true);

			    if(isset($status['transactionStatus']) && $status['transactionStatus'] == "Completed") {
					return $response;
			    } else {
			    	return "Unable to capture payment! Reason: ". $status['errorCode']." - ".$status['errorMessage'];
			    }
	    	} else {
	    		return "Trying to capture payment in sale mode!";
	    	}
	    	
	    }

	    public function readyParameter(array $param) {
	    	$this->data['mode'] = (isset($param['mode'])) ? $param['mode'] : '0011';
	    	$this->data['payerReference'] = (isset($param['payerReference'])) ? $param['payerReference'] : '01111111111';
	    	$this->data['callbackURL'] = $this->getCallbackUrl();
	    	$this->data['amount'] = (isset($param['amount'])) ? $param['amount'] : null;
	    	$this->data['currency'] = "BDT";
	    	$this->data['intent'] = $this->getCapture();
	    	$this->data['merchantInvoiceNumber'] = (isset($param['merchantInvoiceNumber'])) ? $param['merchantInvoiceNumber'] : null;
	    	$this->data['merchantAssociationInfo'] = (isset($param['merchantAssociationInfo'])) ? $param['merchantAssociationInfo'] : null;

	    	return $this->data;
	    }

	    public function readyRefundParameter(array $param) {
	    	$this->data['paymentID'] = (isset($param['paymentID'])) ? $param['paymentID'] : null;
	    	$this->data['amount'] = (isset($param['amount'])) ? $param['amount'] : null;
	    	$this->data['trxID'] = (isset($param['trxID'])) ? $param['trxID'] : null;
	    	$this->data['sku'] = (isset($param['sku'])) ? $param['sku'] : null;
	    	$this->data['reason'] = (isset($param['reason'])) ? $param['reason'] : null;

	    	return $this->data;
	    }

	    public function readyRefundStatusParameter(array $param) {
	    	$this->data['paymentID'] = (isset($param['paymentID'])) ? $param['paymentID'] : null;
	    	$this->data['trxID'] = (isset($param['trxID'])) ? $param['trxID'] : null;

	    	return $this->data;
	    }
	}