<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/bankform.php');


if(!$id_order=Tools::getValue('id_order'))
	die('Не указан номер заказа');

$order=new Order(intval($id_order));



if (!(int)($order->getCurrentState()))
	die('Такого заказа не существует');


if ($order->hasBeenPaid())
	die('Заказ уже оплачен');

$securekey=Tools::getValue('securekey');


if($order->secure_key !=$securekey)
    die('Неверный ключ доступа');



//if($order->id_customer!=$cookie->id_customer)
//	Tools::redirect('authentication.php?back='.urlencode('modules\bankform\form.php?id_order=').$id_order);

$currency=new Currency($order->id_currency);
$addr= new Address($order->id_address_invoice);

$bankform = new bankform();

		$smarty->assign(array(
			'firstname' => $addr->firstname,
			'lastname' => $addr->lastname,
			'city' => $addr->city,
			'addr' => $addr->address1,
			'id_order' => $order->id,
			'total_to_pay' => Tools::displayPrice($order->total_paid, $currency, false, false),
			'compname' => $bankform->compname,
			'schet' => $bankform->schet,
			'inn' => $bankform->inn,
			'kpp' => $bankform->kpp,
			'bankname' => $bankform->bankname,
			'korschet' => $bankform->korschet,
			'bik' => $bankform->bik
		));
$smarty->display(dirname(__FILE__).'/'.'form.tpl');

?>