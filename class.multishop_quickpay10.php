<?php

class tx_multishop_quickpay10 {
	function mslib_payment(&$params, &$ref) {
		// Quickpay.net
		$vars=array();
		$vars['merchant_id']['type']='input';
		$vars['API_user_key']['type']='input';
		//$vars['callback_url']['type']='input';
		
		$vars['Autocapture']['type']='radio';
		$vars['Autocapture']['options'][]='true';
		$vars['Autocapture']['options'][]='false';
		$vars['Autofee']['type']='radio';
		$vars['Autofee']['options'][]='true';
		$vars['Autofee']['options'][]='false';
		
		$additional_info=array();
		$additional_info['1']['label']='Merchant ID';
		$additional_info['1']['value']='<a href=\"https://manage.quickpay.net\" >Quickpay manager</a> > Integration > Merchant ID';
		$additional_info['2']['label']='API user key';
		$additional_info['2']['value']='<a href=\"https://manage.quickpay.net\" >Quickpay manager</a> > Integration > API user key';
		$additional_info['3']['label']='Autocapture payment';
		$additional_info['3']['value']='Capture payment instantly';
		$additional_info['4']['label']='Autofee';
		$additional_info['4']['value']='Should customer be charged for the card fee?';
		//$additional_info['3']['label']='Callback URL';
		//$additional_info['3']['value']=$ref->FULL_HTTP_URL.mslib_fe::typolink($this->shop_pid.',2002', 'tx_multishop_pi1[page_section]=psp&tx_multishop_pi1[payment_lib]=quickpay10&tx_multishop_pi1[payment_section]=notification', 1);
		$payment_method=array(
			'name'=>'quickpay.net payment v10',
			'country'=>'dk',
			'vars'=>$vars,
			'more_info_link'=>'https://manage.quickpay.net',
			'image'=>'quickpay.png',
			'additional_info'=>$additional_info
		);
		// quickpay.net EOF
		$params['installedPaymentMethods']['quickpay10']=$payment_method;
	}
}

?>
