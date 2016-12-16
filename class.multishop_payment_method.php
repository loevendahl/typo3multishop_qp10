<?php

class tx_multishop_payment_method extends mslib_payment {
	function displayPaymentButton($orders_id, $ref) {
		$order=mslib_fe::getOrder($orders_id);
		if ($order['orders_id']) {
			$vars=$this->variables;
			
			$amount=(preg_replace("/^0/", '', round($order['total_amount'], 2))*100);
			$ordernumber=time();
			$transaction_id=mslib_fe::createPaymentTransactionId($order['orders_id'], 'quickpay', $order['payment_method'], 'manual', $ordernumber.'-'.$order['orders_id']);
			$merchant=$vars['merchant_id'];
			$language= $ref->lang;
			$amount=str_replace('.', '', $amount);
			//subscription payments prepared, but not yet implemented...
			$subscription ='0';
			$currency = ($ref->cookie["selected_currency"] ? $ref->cookie["selected_currency"] : $ref->ms['MODULES']['CURRENCY_ARRAY']['cu_iso_3']);
			$continueurl=$ref->FULL_HTTP_URL.mslib_fe::typolink('', 'tx_multishop_pi1[page_section]=psp_accepturl');
			$cancelurl=$ref->FULL_HTTP_URL.mslib_fe::typolink('', 'tx_multishop_pi1[page_section]=psp_cancelurl');
			$callbackurl=$ref->FULL_HTTP_URL.mslib_fe::typolink($this->shop_pid.',2002', 'tx_multishop_pi1[page_section]=psp&tx_multishop_pi1[payment_lib]=quickpay10&tx_multishop_pi1[payment_section]=notification&subscriptions='.$subscription.'&oid='.$transaction_id, 1);
			//$callbackurl=$vars['callback_url']; # see http://quickpay.dk/clients/callback-quickpay.php.txt
			$autocapture=($vars['Autocapture'] == "true" ? '1' :'0');
			$autofee=($vars['Autofee'] == "true" ? '1' :'0');;
			$description = $vars['merchant_id']." - Multishop payment";
			
			$process_parameters = array(
				//	'agreement_id'                 => $qp_aggreement_id,
					'amount'                       => $amount,
					'autocapture'                  => $autocapture,
					'autofee'                      => $autofee,
					//'branding_id'                  => $qp_branding_id,
					'callbackurl'                  => $callbackurl,
					'cancelurl'                    => $cancelurl,
					'continueurl'                  => $continueurl,
					'currency'                     => $currency,
					'description'                  => $description,
					'google_analytics_client_id'   => $qp_google_analytics_client_id,
					'google_analytics_tracking_id' => $analytics_tracking_id,
					'language'                     => $language,
					'merchant_id'                  => $qp_merchant_id,
					'order_id'                     => $transaction_id,
					'payment_methods'              => $qp_cardtypelock,
					//'product_id'                   => $qp_product_id,
					//'category'                     => $qp_category,
					//'reference_title'              => $qp_reference_title,
					//'vat_amount'                   => $qp_vat_amount,
					'subscription'                 => $subscription,
					'version'                      => 'v10'
						);

				  $apiorder= new QuickpayApi();
	$apiorder->setOptions($vars['API_user_key']);
	//been here before?
	//set status request mode
	 $apiorder->mode = ($subscription == '0' ? "payments?order_id=" : "subscriptions?order_id=");
	  	
	 $exists = $apiorder->status($transaction_id);
	
      $qid = $exists["id"];
	//set $apiorder to create/update mode
	$apiorder->mode = ($subscription == '0' ? "payments/" : "subscriptions/");
	  
	  if($exists["id"] == null){

      //create new quickpay order	
      $storder = $apiorder->createorder($transaction_id, $currency, $process_parameters);
      $qid = $storder["id"];

      }
		$storder = $apiorder->link($qid, $process_parameters);
	
		if($storder['url']){
			header("location: ".$storder['url']);
		/*	$content.= "<script>
     
window.location.replace('".$storder['url']."');
      </script>";*/
		}else{
			$content.= "<script>
       alert('Quickpay error: Payment module is not properly configured');

      </script>";
			
			}
					$content.='
			<form action="#" method="post" id="pspform">';
		foreach($process_parameters as $key=>$value){
				$content.= "<input type='hidden' value='".$value."' name='".$key."' />". "\n";
				
			}
			    $content.='<input type="submit" value="Pay" />
			</form>
			';
					
			//mail("kl@blkom.dk","t3",json_encode($process_parameters)."n\n" .json_encode($order, true)."n\n" .json_encode($ref, true)."n\n" .json_encode($vars, true));
			
				//$content.= json_encode($order);
				//$content.= json_encode($ref);	
	//$process_button_string .=  "<input type='hidden' value='go' name='callquickpay' />". "\n".
            //	"<input type='hidden' value='" . $_POST['cardlock'] . "' name='cardlock' />
			//	<input type='hidden' value='" . $_POST['conditions'] . "' name='conditions' />";
			
	 
//}



				//$content.= json_encode($ref->cookie["selected_currency"], JSON_PRETTY_PRINT) ;
				//$content.= json_encode($process_parameters). json_encode($order).json_encode($ref).json_encode($vars);
	/*		$amount=(preg_replace("/^0/", '', round($order['total_amount'], 2))*100);
			$ordernumber=time();
			$transaction_id=mslib_fe::createPaymentTransactionId($order['orders_id'], 'quickpay', $order['payment_method'], 'manual', $ordernumber);
			$protocol='10';
			$msgtype='authorize';
			$merchant=$vars['merchant_id'];
			$language='en';
			$amount=str_replace('.', '', $amount);
			$currency='DKK';
			$continueurl=$ref->FULL_HTTP_URL.mslib_fe::typolink('', 'tx_multishop_pi1[page_section]=psp_accepturl');
			$cancelurl=$ref->FULL_HTTP_URL.mslib_fe::typolink('', 'tx_multishop_pi1[page_section]=psp_cancelurl');
			$callbackurl=$vars['callback_url']; # see http://quickpay.dk/clients/callback-quickpay.php.txt
			$autocapture='0';
			//$md5secret=$vars['md5_secret'];
			$group='11111'; //add subscription to this group
			$splitpayment='0'; //should splitpayment be enabled on transaction, can be 0 => disabled, 1 => enabled
			$md5check=md5($protocol.$msgtype.$merchant.$language.$ordernumber.$amount.$currency.$continueurl.$cancelurl.$callbackurl.$autocapture.$group.$splitpayment.$md5secret);
			$content.='
			<form action="https://secure.quickpay.dk/form/" method="post" id="pspform">
			    <input type="hidden" name="protocol" value="'.$protocol.'" />
			    <input type="hidden" name="msgtype" value="'.$msgtype.'" />
			    <input type="hidden" name="merchant" value="'.$merchant.'" />
			    <input type="hidden" name="language" value="'.$language.'" />
			    <input type="hidden" name="ordernumber" value="'.$ordernumber.'" />
			    <input type="hidden" name="amount" value="'.$amount.'" />
			    <input type="hidden" name="currency" value="'.$currency.'" />
			    <input type="hidden" name="continueurl" value="'.$continueurl.'" />
			    <input type="hidden" name="cancelurl" value="'.$cancelurl.'" />
			    <input type="hidden" name="callbackurl" value="'.$callbackurl.'" />
			    <input type="hidden" name="autocapture" value="'.$autocapture.'" />
			    <input type="hidden" name="group" value="'.$group.'" />
			    <input type="hidden" name="splitpayment" value="'.$splitpayment.'" />
			    <input type="hidden" name="md5check" value="'.$md5check.'" />
			    <input type="submit" value="Pay" />
			</form>
			';
			
			
			if ($vars['autosubmit']=='true') {
				$content.='<script type="text/javascript" language="JavaScript">
							jQuery(document).ready(function(){
								jQuery.blockUI({ css: {
									width: \'350\',
									border: \'none\',
									padding: \'15px\',
									backgroundColor: \'#000\',
									\'-webkit-border-radius\': \'10px\',
									\'-moz-border-radius\': \'10px\',
									opacity: .5,
									color: \'#fff\'
								},
								message:  \'ONE MOMENT PLEASE\',
								onBlock: function() {
									jQuery("#pspform").attr(\'target\',\'\');
							
									jQuery("#pspform").submit();
										//		return true;
									}
								});
							
							});
							</script>';
			}*/
		}
		return $content;
	}
	function paymentNotificationHandler() {
	
		//$post=$this->ref->post;
		$vs = unserialize($this->enabledPaymentMethods['Quickpay']['vars']);
		$apikey = $vs["API_user_key"];

		switch ($_REQUEST['tx_multishop_pi1']['payment_section']) {
			case 'notification':
			$transaction_id = $_REQUEST['oid'];
			$subscriptions = $_REQUEST['subscriptions'];
			 $apiorder= new QuickpayApi();
	        $apiorder->setOptions($apikey);

	          //set status request mode
	        $apiorder->mode = ($subscriptions == '0' ? "payments?order_id=" : "subscriptions?order_id=");
	  	
	        $st = $apiorder->status($transaction_id);
		
			if($st[0]["id"]){
   $st[0]["operations"] = array_reverse($st[0]["operations"]);
    $qp_status = $st[0]["operations"][0]["qp_status_code"];
	 $pstatus = $st[0]["operations"][0]["type"];
			}
	       
				if ($pstatus == "capture" && $qp_status =='20000') {
				
					$orders_id=mslib_fe::getOrdersIdByTransactionId($transaction_id, 'quickpay');
			
					if ($orders_id) {
						mslib_fe::updateOrderStatusToPaid($orders_id);
					}
				}
				break;
		}
		return $content;
	}
}

class QuickpayApi {

  public $mode = "payments/";
	/**
	* Set the options for this object
	* apikey is found in https://manage.quickpay.net
	*/
	function setOptions($apiKey, $connTimeout=10, $apiVersion="v10") {
		QPConnectorFactory::getConnector()->setOptions($apiKey, $connTimeout, $apiVersion);	
	}

	/**
	* Get a list of payments.
	*/
	function getPayments() {
		$result = QPConnectorFactory::getConnector()->request($this->mode);	
		return json_decode($result, true);	
	}	

        /**
        * Get a specific payment.
        * The errorcode 404 is set in the thrown exception if the order is not found
        */
    function status($id) {
		$result = QPConnectorFactory::getConnector()->request($this->mode.$id);		

		return json_decode($result, true);			
	}
	
    function link($id,$postArray) {
		$result = QPConnectorFactory::getConnector()->request($this->mode.$id."/link?currency=".$postArray["currency"]."&amount=".$postArray["amount"], $postArray,'PUT');	
		
			
		return json_decode($result, true);			
	}
	/**
	* Renew a payment
	*/
        function renew($id) {
                $postArray = array();
                $postArray['id'] = $id;
		$result = QPConnectorFactory::getConnector()->request($this->mode . $id . '/renew', $postArray);	
		return json_decode($result, true);			
	}
	
	/**
	* Capture a payment
	*/
        function capture($id, $amount, $extras=null) {
                $postArray = array();
                $postArray['id'] = $id;
                $postArray['amount'] = $amount;
                if (!is_null($extras)) {
		  $postArray['extras'] = $extras;
		}
		$result = QPConnectorFactory::getConnector()->request($this->mode . $id . '/capture', $postArray);	
		return json_decode($result, true);			
	}

	/**
	* Refund a payment
	*/
        function refund($id, $amount, $extras=null) {
                $postArray = array();
                $postArray['id'] = $id;
                $postArray['amount'] = $amount;
                if (!is_null($extras)) {
		  $postArray['extras'] = $extras;
		}
		$result = QPConnectorFactory::getConnector()->request($this->mode . $id . '/refund', $postArray);	
		return json_decode($result, true);			
	}


	/**
	* Cancel a payment
	*/
        function cancel($id) {
                $postArray = array();
                $postArray['id'] = $id;
		$result = QPConnectorFactory::getConnector()->request($this->mode . $id . '/cancel', $postArray);	
		
		return json_decode($result, true);			
	}
 
 function createorder($order_id, $currency,$postArray,$addlink='') {
             
		$result = QPConnectorFactory::getConnector()->request($this->mode.$addlink.'?order_id='.$order_id.'&currency='.$currency, $postArray);	

		return json_decode($result, true);			
	}

function log_operations($operations, $currency = ""){
	$str="<ul>";
foreach($operations as $op){
	$str .= "<li><b>".$op["type"]."</b> - ".number_format($op["amount"]/100,2,',','')." ".$currency.", <b>Quickpay info</b>: ".$op["qp_status_msg"].", <b>Aquirer info</b>: ".$op["aq_status_msg"].", <b>Log</b>: ".$op["created_at"]."</li>";
	
}
	$str .= "<ul>";
	return $str;
}   

public function init() {
        //check for curl 
        if(!extension_loaded('curl')) {
         
            return false;
        }
	

        return true;
    }
}

interface QPConnectorInterface {
    
    public function request($data);
    
}

class QPConnectorFactory {

    public static function getConnector() {
        static $inst = null;
        if ($inst === null) {
            $inst = new QPConnectorCurl();
        }
        return $inst;
    }

    private function __construct() {
    }
}
class QPConnectorCurl implements QPConnectorInterface {

    protected $connTimeout = 10;
    protected $apiUrl = "https://api.quickpay.net";
    protected $apiVersion = 'v10';
    protected $apiKey = "";
    protected $format = "application/json";    

    public function __constructor() {
        if (!function_exists('curl_init')){
            throw Exception('CURL is not installed, please install curl or change connection method');
        }     
    }

    public function setOptions($apiKey, $connTimeout=10, $apiVersion="v10") {
       $this->connTimeout = $connTimeout;
       $this->apiKey = $apiKey;
       $this->apiVersion = $apiVersion;
    }
    
   

 
    public function request($resource, $postdata=null, $sendmode='GET-POST') {
        $curl =  curl_init();
        $url = $this->apiUrl . "/" . $resource;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->connTimeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode(":" . $this->apiKey),
	    'Accept-Version: ' . $this->apiVersion,
            'Accept: ' . $this->format
        ));
        if (!is_null($postdata)) {
			if($sendmode=='GET-POST'){
	  curl_setopt($curl, CURLOPT_POST, 1);
			}else{
	  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); 		
			}
	  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postdata));		
	}

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);         
        curl_close($curl);

	if ($httpCode!=200 && $httpCode!=202) {
	//  throw new Exception($response, $httpCode);	
	}

        return $response;
    }

   
}
?>