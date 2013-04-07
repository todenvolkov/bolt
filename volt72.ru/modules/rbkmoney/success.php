<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/rbkmoney.php');

$order=new Order(intval(Tools::GetValue('orderId')));
$order_id=$order->id;
if ($order->hasBeenPaid())
{
	$rbkmoney = new rbkmoney();
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$order->id_cart.'&id_module='.$rbkmoney->id.'&id_order='.$order_id.'&key='.$order->secure_key);
}else{

	include(dirname(__FILE__).'/../../header.php');
	$smarty->assign(array(
		'paymentn' => $order_id,
		'admin'=>Configuration::get('PS_SHOP_EMAIL')));
	
	$smarty->display(dirname(__FILE__).'/wait.tpl');
	
	include(dirname(__FILE__).'/../../footer.php');

}

?>