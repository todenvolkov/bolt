<?php

error_reporting (E_ALL);

include('kcaptcha.php');

session_start();

$captcha = new KCAPTCHA();

//if($_REQUEST[session_name()]){
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
	//setcookie('captcha_keystring', $captcha->getKeyString(), time()+60 * 15, '/', 'aquaterm72.ru');
//}

?>
