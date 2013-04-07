<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>redirect</title>
</head>
<body>
{l s='Ожидание перенаправления' mod='rbkmoney'}

<form action="https://rbkmoney.ru/acceptpurchase.aspx" name="pay" method="post" accept-charset="windows-1251">
	<input type="hidden" name="eshopId" value="{$purse}" /> 
	<input type="hidden" name="orderId" value="{$id_order}" /> 
	<input type="hidden" name="serviceName" value="{l s='Оплата заказа №' mod='lendshop'}{$id_order}" />
	<input type="hidden" name="user_email " value="{$email}" />
	<input type="hidden" name="version  " value="2" />
	<input type="hidden" name="recipientAmount" value="{$total}" /> 
	<input type="hidden" name="recipientCurrency" value="{$currency_iso}" /> 
	<input type="hidden" name="successUrl" value="{$base_dir_ssl}modules/rbkmoney/success.php" /> 
	<input type="hidden" name="failUrl" value="{$base_dir_ssl}cart.php" /> 
	<input type="submit"/>
</form>

<script type="text/javascript">
<!--
	document.pay.submit();
-->
</script>
</body></html>