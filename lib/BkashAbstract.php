<?php 
	namespace Bkash\Urlbased;

	abstract class BkashAbstract
	{
		protected $apiurl;
		protected $appkey;
		protected $appsecret;
		protected $username;
		protected $password;
		protected $environment;
		protected $capture;
		protected $token;
		protected $callbakurl;

		protected function setApiurl($apiUrl) {
			$this->apiurl = $apiUrl;
		}

		protected function getApiurl() {
	        return $this->apiurl;
	    }

	    protected function setAppkey($appKey) {
			$this->appkey = $appKey;
		}

		protected function getAppkey() {
	        return $this->appkey;
	    }

	    protected function setAppsecret($appSecret) {
			$this->appsecret = $appSecret;
		}

		protected function getAppsecret() {
	        return $this->appsecret;
	    }

	    protected function setUsername($Username) {
			$this->username = $Username;
		}

		protected function getUsername() {
	        return $this->username;
	    }

	    protected function setPassword($Password) {
			$this->password = $Password;
		}

		protected function getPassword() {
	        return $this->password;
	    }

	    protected function setEnv($Environment) {
			$this->environment = $Environment;
		}

		protected function getEnv() {
	        return $this->environment;
	    }

	    protected function setCapture($Capture) {
			$this->capture = $Capture;
		}

		protected function getCapture() {
	        return $this->capture;
	    }

	    protected function setToken($Token) {
			$this->token = $Token;
		}

		protected function getToken() {
	        return $this->token;
	    }

	    protected function setCallbackUrl($Callbakurl) {
			$this->callbakurl = $Callbakurl;
		}

		protected function getCallbackUrl() {
	        return $this->callbakurl;
	    }

	    protected function Post($postdata, $header) {
	    	
	    	$curl = curl_init($this->getApiurl());			
		    
		    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		    if(isset($postdata) && $postdata != "") {
		    	$post_data = json_encode($postdata);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($curl, CURLOPT_PROXY, $proxy);
			
			$result = curl_exec($curl);
			
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if($code == 200 && !( curl_errno($curl))) {
				curl_close( $curl);
				return $result;   
			} else {
				curl_close( $curl);
				echo "Failed to Connect With Bkash!";
				exit;
			}
	    }

	    protected function Get($header) {
	    	
	    	$curl = curl_init($this->getApiurl());			
		    
		    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($curl, CURLOPT_PROXY, $proxy);
			
			$result = curl_exec($curl);
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if($code == 200 && !( curl_errno($curl))) {
				curl_close( $curl);
				return $result;   
			} else {
				curl_close( $curl);
				echo "Failed to Connect With Bkash!";
				exit;
			}  
	    }

	    public function redirect($url, $permanent = false)
	    {
	        header('Location: ' . $url, true, $permanent ? 301 : 302);
	        exit();
	    }
	}
