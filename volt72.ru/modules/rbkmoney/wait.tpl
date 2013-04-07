<script type="text/javascript">
<!--
	var baseDir = '{$base_dir_ssl}';
-->
</script>


<h2>{l s='Ожидание платежа' mod='rbkmoney'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

<p>{l s='На данный момент информация о платеже еще не поступила. Как только она будет получена вы сможете увидить ваш заказ в личном кабинете' mod='rbkmoney'}</p>
<p>{l s='Если вы не получите уведомление о платеже сообщите его номер' mod='rbkmoney'} {$paymentn} {l s='администратору' mod='rbkmoney'} {$admin}</p>
