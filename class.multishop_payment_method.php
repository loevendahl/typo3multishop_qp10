<?php
class tx_multishop_payment_method extends mslib_payment {
	function displayPaymentButton($orders_id, $ref) {
		$order=mslib_fe::getOrder($orders_id);
		if ($order['orders_id']) {
			$vars=$this->variables;
			$amount=(preg_replace("/^0/", '', round($order['total_amount'], 2))*100);
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
			}
		}
		return $content;
	}
	function paymentNotificationHandler() {
		$post=$this->ref->post;
		switch ($_REQUEST['tx_multishop_pi1']['payment_section']) {
			case 'notification':
				$fields=array(
					'msgtype',
					'ordernumber',
					'amount',
					'currency',
					'time',
					'state',
					'qpstat',
					'qpstatmsg',
					'chstat',
					'chstatmsg',
					'merchant',
					'merchantemail',
					'transaction',
					'cardtype',
					'cardnumber',
					'splitpayment',
					'fraudprobability',
					'fraudremarks',
					'fraudreport',
					'fee',
					'md5check'
				);
				while (list(, $k)=each($fields)) {
					if (isset($post[$k])) {
						$message.="$k: ".$post[$k]."\r\n";
					}
				}
				if ($post['state']==1 && $post['qpstat']='000') {
					$transaction_id=$_POST['ordernumber'];
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


?>