В themes\prestashop\history.tpl после

				{if ($order.invoice AND $order.invoice_number) AND $invoiceAllowed}
					<a href="{$base_dir}pdf-invoice.php?id_order={$order.id_order|intval}" title="{l s='Invoice'} {$order.name|escape:'htmlall':'UTF-8'}"><img src="{$img_dir}icon/pdf.gif" alt="{l s='Invoice'} {$order.name|escape:'htmlall':'UTF-8'}" class="icon" /></a>
					<a href="{$base_dir}pdf-invoice.php?id_order={$order.id_order|intval}" title="{l s='Invoice'} {$order.name|escape:'htmlall':'UTF-8'}">{l s='PDF'}</a>
				{else}-{/if}

вставить

					<a href="{$base_dir}modules/bankform/form.php?id_order={$order.id_order|intval}" title="{l s='bank'} {$order.name|escape:'htmlall':'UTF-8'}"><img src="{$base_dir}modules/bankform/logo.gif" alt="{l s='bank'} {$order.name|escape:'htmlall':'UTF-8'}" class="icon" /></a>
					<a href="{$base_dir}modules/bankform/form.php?id_order={$order.id_order|intval}" title="{l s='bank'} {$order.name|escape:'htmlall':'UTF-8'}">{l s='bank'}</a>


В modules\bankwire\payment_return.tpl вместо

		{l s='Please send us a bank wire with:' mod='bankwire'}
		<br /><br />- {l s='an amout of' mod='bankwire'} <span class="price">{$total_to_pay}</span>
		<br /><br />- {l s='to the account owner of' mod='bankwire'} <span class="bold">{if $bankwireOwner}{$bankwireOwner}{else}___________{/if}</span>
		<br /><br />- {l s='with theses details' mod='bankwire'} <span class="bold">{if $bankwireDetails}{$bankwireDetails}{else}___________{/if}</span>
		<br /><br />- {l s='to this bank' mod='bankwire'} <span class="bold">{if $bankwireAddress}{$bankwireAddress}{else}___________{/if}</span>
		<br /><br />- {l s='Do not forget to insert your order #' mod='bankwire'} <span class="bold">{$id_order}</span> {l s='in the subjet of your bank wire' mod='bankwire'}

вставить

		<a href="{$base_dir}modules/bankform/form.php?id_order={$id_order}" title=""><img src="{$base_dir}modules/bankform/logo.gif" alt="" class="icon" /> Скачать квитанцию на оплату</a>

В mails\ru\bankwire.html и mails\ru\bankwire.txt вставить ссылку на оплату
в текстовом виде
{shop_url}modules/bankform/form.php?id_order={$id_order}
в html
<a href="{shop_url}modules/bankform/form.php?id_order={$id_order}" title="">Скачать квитанцию на оплату</a>

В admin\tabs\AdminOrders.php после

' - <a href="javascript:window.print()"><img src="../img/admin/printer.gif" alt="'.$this->l('Print order').'" title="'.$this->l('Print order').'" /></a>';

добавить

echo ' - <a href="'._PS_BASE_URL_.'modules/bankform/form.php?id_order='.$order->id.'"><img src="'._PS_BASE_URL_.'modules/bankform/logo.gif" alt="'.$this->l('Bank').'" title="'.$this->l('Bank').'" /></a>';