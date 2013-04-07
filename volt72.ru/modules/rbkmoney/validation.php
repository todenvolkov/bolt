<?php

include(dirname(__FILE__).'/../../config/config.inc.php');

$data = array(
   'eshop_id'   => Tools::GetValue('eshopId'),
   'order_id'   => Tools::GetValue('orderId'),
   'srv_name'	 => Tools::GetValue('serviceName'),
   'eshop_acc'  => Tools::GetValue('eshopAccount'),
   'amount'     => Tools::GetValue('recipientAmount'),
   'currency'	 => Tools::GetValue('recipientCurrency'),
   'pay_status' => Tools::GetValue('paymentStatus'),
   'uname'		 => Tools::GetValue('userName'),
   'uemail'	 => Tools::GetValue('userEmail'),
   'pay_date'	 => Tools::GetValue('paymentData'),
   'skey'	 => Configuration::get('rbkmoney_KEY')
);


// eshopId::orderId::serviceName::eshopAccount::recipientAmount::recipientCurrency::paymentStatus::userName::userEmail::paymentData::secretKey

if(md5(implode('::', $data)) != Tools::GetValue('hash'))
{
    die('hash error');
};
if($data['eshop_id'] != Configuration::get('rbkmoney_PURSE'))
{
    die('purce error');
};
$order= new Order((int)$data['order_id'] );

if (!Validate::isLoadedObject($order))
	die('order not exist');
if($data['recipientAmount'] != number_format($order->total_paid, 2, '.', ''))
{
    die('total_paid error');
};

$currency = new Currency(intval($order->id_currency));
if ($currency->iso_code=='RUB')
	$iso_code='RUR';
else
	$iso_code=$currency->iso_code;

if($data['recipientCurrency'] != $iso_code)
{
    die('purce error');
};

if ($data['pay_status']==5){
	$paymentn=$order->id;
	$history = new OrderHistory();
	$history->id_order = $paymentn;
	$history->changeIdOrderState(_PS_OS_PAYMENT_, $paymentn);
	$history->addWithemail(true);
}

print("OK");
?>