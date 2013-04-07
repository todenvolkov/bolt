{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14008 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">{l s='Order history'}</h1>
{capture name=path}<a href="{$link->getPageLink('my-account.php', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Order history'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{include file="$tpl_dir./errors.tpl"}

<a href="{$link->getPageLink('history.php', true)}" title="{l s='История заказов'}"><img src="{$img_dir}icon/order.gif" alt="{l s='История заказов'}" class="icon" /></a><a href="{$link->getPageLink('history.php', true)}" title="{l s='История заказов'}">{l s='История заказов'}</a>&nbsp;&nbsp;
	{if $returnAllowed}
		<a href="{$link->getPageLink('order-follow.php', true)}" title="{l s='Merchandise returns'}"><img src="{$img_dir}icon/return.gif" alt="{l s='Merchandise returns'}" class="icon" /></a><a href="{$link->getPageLink('order-follow.php', true)}" title="{l s='Merchandise returns'}">{l s='My merchandise returns'}</a>&nbsp;&nbsp;
	{/if}
	<a href="{$link->getPageLink('order-slip.php', true)}" title="{l s='Кредитная история'}"><img src="{$img_dir}icon/slip.gif" alt="{l s='Кредитная история'}" class="icon" /></a><a href="{$link->getPageLink('order-slip.php', true)}" title="{l s='Кредитная история'}">{l s='Кредитная история'}</a>&nbsp;&nbsp;
	<a href="{$link->getPageLink('addresses.php', true)}" title="{l s='Мои адреса'}"><img src="{$img_dir}icon/addrbook.gif" alt="{l s='Мои адреса'}" class="icon" /></a><a href="{$link->getPageLink('addresses.php', true)}" title="{l s='Мои адреса'}">{l s='Мои адреса'}</a>&nbsp;&nbsp;
	<a href="{$link->getPageLink('identity.php', true)}" title="{l s='Личные данные'}"><img src="{$img_dir}icon/userinfo.gif" alt="{l s='Личные данные'}" class="icon" /></a><a href="{$link->getPageLink('identity.php', true)}" title="{l s='Личные данные'}">{l s='Личные данные'}</a>&nbsp;&nbsp;
	{if $voucherAllowed}
		<a href="{$link->getPageLink('discount.php', true)}" title="{l s='Мои ваучеры'}"><img src="{$img_dir}icon/voucher.gif" alt="{l s='Мои ваучеры'}" class="icon" /></a><a href="{$link->getPageLink('discount.php', true)}" title="{l s='Мои ваучеры'}">{l s='Мои ваучеры'}</a>&nbsp;&nbsp;
	{/if}
	{$HOOK_CUSTOMER_ACCOUNT}
<BR /><BR />

<div id="cart">
<p>{l s='Here are the orders you have placed since the creation of your account'}.</p>

{if $slowValidation}<p class="warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh the page if your order is missing.'}</p>{/if}

<div class="block-center" id="block-history">
	{if $orders && count($orders)}
	<table id="order-list" class="std">
		<thead>
			<tr>
				<th class="first_item">{l s='Order'}</th>
				<th class="item">{l s='Date'}</th>
				<th class="item">{l s='Total price'}</th>
				<th class="item">{l s='Payment'}</th>
				<th class="item">{l s='Status'}</th>
				<th class="item">{l s='Invoice'}</th>
				<th class="last_item" style="width:65px">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$orders item=order name=myLoop}
			<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
				<td class="history_link bold">
					{if isset($order.invoice) && $order.invoice && isset($order.virtual) && $order.virtual}<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Products to download'}" title="{l s='Products to download'}" />{/if}
					<a class="color-myaccount" href="javascript:showOrder(1, {$order.id_order|intval}, 'order-detail');">{l s='#'}{$order.id_order|string_format:"%06d"}</a>
				</td>
				<td class="history_date bold">{dateFormat date=$order.date_add full=0}</td>
				<td class="history_price"><span class="price">{displayPrice price=$order.total_paid_real currency=$order.id_currency no_utf8=false convert=false}</span></td>
				<td class="history_method">{$order.payment|escape:'htmlall':'UTF-8'}</td>
				<td class="history_state">{if isset($order.order_state)}{$order.order_state|escape:'htmlall':'UTF-8'}{/if}</td>
				<td class="history_invoice">
				{if (isset($order.invoice) && $order.invoice && isset($order.invoice_number) && $order.invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
					<a href="{$link->getPageLink('pdf-invoice.php', true)}?id_order={$order.id_order|intval}" title="{l s='Invoice'}"><img src="{$img_dir}icon/pdf.gif" alt="{l s='Invoice'}" class="icon" /></a>
					<a href="{$link->getPageLink('pdf-invoice.php', true)}?id_order={$order.id_order|intval}" title="{l s='Invoice'}">{l s='PDF'}</a>
				{else}-{/if}
				</td>
				<td class="history_detail">
					<a class="color-myaccount" href="javascript:showOrder(1, {$order.id_order|intval}, 'order-detail');">{l s='details'}</a>
					<a href="{if isset($opc) && $opc}{$link->getPageLink('order-opc.php', true)}{else}{$link->getPageLink('order.php', true)}{/if}?submitReorder&id_order={$order.id_order|intval}" title="{l s='Reorder'}">
						<img src="{$img_dir}arrow_rotate_anticlockwise.png" alt="{l s='Reorder'}" title="{l s='Reorder'}" class="icon" />
					</a>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="hidden">&nbsp;</div>
	{else}
		<p class="warning">{l s='You have not placed any orders.'}</p>
	{/if}
</div>

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account.php', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account.php', true)}">{l s='Back to Your Account'}</a></li>
	<li><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>
</div>