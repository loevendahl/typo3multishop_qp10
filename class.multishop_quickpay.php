<?php

class tx_multishop_quickpay {
	function mslib_payment(&$params, &$ref) {
		// Quickpay.net
		$vars=array();
		$vars['merchant_id']['type']='input';
		$vars['md5_secret']['type']='input';
		$vars['callback_url']['type']='input';
		$vars['test']['type']='radio';
		$vars['test']['options'][]='true';
		$vars['test']['options'][]='false';
		$additional_info=array();
		$additional_info['1']['label']='Merchant ID';
		$additional_info['1']['value']='Indstilinger > Merchant > QuickpayId';
		$additional_info['2']['label']='MD5 Secret';
		$additional_info['2']['value']='Indstilinger > MD5 Kode';
		$additional_info['3']['label']='Callback URL';
		$additional_info['3']['value']=$ref->FULL_HTTP_URL.mslib_fe::typolink($this->shop_pid.',2002', 'tx_multishop_pi1[page_section]=psp&tx_multishop_pi1[payment_lib]=quickpay&tx_multishop_pi1[payment_section]=notification', 1);
		$payment_method=array(
			'name'=>'quickpay.net',
			'country'=>'dk',
			'vars'=>$vars,
			'more_info_link'=>'http://www.typo3multishop.com/payment-service-providers/quickpay/',
			'image'=>'quickpay.png',
			'additional_info'=>$additional_info
		);
		// quickpay.net EOF
		$params['installedPaymentMethods']['quickpay']=$payment_method;
	}
}

?>
