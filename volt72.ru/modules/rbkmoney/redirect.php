<?php

include('../../config/config.inc.php');
include('../../init.php');
include('rbkmoney.php');


	$cart= new Cart(intval(Tools::GetValue('id_cart')));

	if(!Validate::isLoadedObject($cart))
		die('cart error');

	$customer=new Customer($cookie->id_customer);
	$rbkmoney = new rbkmoney();
	$total = $cart->getOrderTotal();
	$rbkmoney->validateOrder(intval($cart->id), _PS_OS_PREPARATION_, $total, $rbkmoney->displayName, NULL, array(), NULL, false,$customer->secure_key);

	if(!$order = new Order(intval($rbkmoney->currentOrder)))
		die('order error');

	$currency = new Currency(intval($order->id_currency));
	if ($currency->iso_code=='RUB')
		$iso_code='RUR';
	else
		$iso_code=$currency->iso_code;
	$purse=Configuration::get('rbkmoney_PURSE');
	$total=number_format($total, 2, '.', '');
	$id_order=intval($order->id);

	$customer= new Customer($cart->id_customer);


	$smarty->assign(array(
		'purse' => $purse,
		'id_order' => $id_order,
		'email' => $customer->email,
		'total' => $total,
		'currency_iso' => $iso_code
	));

	$smarty->display(dirname(__FILE__).'/redirect.tpl');

?>
