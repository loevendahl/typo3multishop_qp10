<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "multishop_quickpay".
 * Auto generated 23-11-2012 09:15
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/
$EM_CONF[$_EXTKEY]=array(
	'title'=>'Multishop quickpay',
	'description'=>'Adds quickpay payment provider to Multishop.',
	'category'=>'fe',
	'author'=>'Bas van Beek',
	'author_email'=>'bas@bvbmedia.nl',
	'shy'=>'',
	'dependencies'=>'multishop',
	'conflicts'=>'',
	'priority'=>'',
	'module'=>'',
	'state'=>'alpha',
	'internal'=>'',
	'uploadfolder'=>0,
	'createDirs'=>'',
	'modify_tables'=>'',
	'clearCacheOnLoad'=>0,
	'lockType'=>'',
	'author_company'=>'',
	'version'=>'1.0.3',
	'constraints'=>array(
		'depends'=>array(
			'typo3'=>'4.5.0-6.2.99',
			'multishop'=>'3.0.0',
		),
		'conflicts'=>array(),
		'suggests'=>array(),
	),
	'_md5_values_when_last_written'=>'a:6:{s:34:"class.multishop_payment_method.php";s:4:"4e80";s:28:"class.multishop_quickpay.php";s:4:"9910";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"7c9b";s:14:"ext_tables.sql";s:4:"1f5c";s:10:"README.txt";s:4:"ee2d";}',
	'suggests'=>array(),
);

?>