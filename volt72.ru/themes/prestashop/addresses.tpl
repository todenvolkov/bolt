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

{*
** Retro compatibility for PrestaShop version < 1.4.2.5 with a recent theme
*}

{* Two variable are necessaries to display the address with the new layout system *}
{* Will be deleted for 1.5 version and more *}
{if !isset($multipleAddresses)}
	{$ignoreList.0 = "id_address"}
	{$ignoreList.1 = "id_country"}
	{$ignoreList.2 = "id_state"}
	{$ignoreList.3 = "id_customer"}
	{$ignoreList.4 = "id_manufacturer"}
	{$ignoreList.5 = "id_supplier"}
	{$ignoreList.6 = "date_add"}
	{$ignoreList.7 = "date_upd"}
	{$ignoreList.8 = "active"}
	{$ignoreList.9 = "deleted"}	
	
	{* PrestaShop < 1.4.2 compatibility *}
	{if isset($addresses)}
		{$address_number = 0}
		{foreach from=$addresses key=k item=address}
			{counter start=0 skip=1 assign=address_key_number}
			{foreach from=$address key=address_key item=address_content}
				{if !in_array($address_key, $ignoreList)}
					{$multipleAddresses.$address_number.ordered.$address_key_number = $address_key}
					{$multipleAddresses.$address_number.formated.$address_key = $address_content}
					{counter}
				{/if}
			{/foreach}
		{$multipleAddresses.$address_number.object = $address}
		{$address_number = $address_number  + 1}
		{/foreach}
	{/if}
{/if}

{* Define the style if it doesn't exist in the PrestaShop version*}
{* Will be deleted for 1.5 version and more *}
{if !isset($addresses_style)}
	{$addresses_style.company = 'address_company'}
	{$addresses_style.vat_number = 'address_company'}
	{$addresses_style.firstname = 'address_name'}
	{$addresses_style.lastname = 'address_name'}
	{$addresses_style.address1 = 'address_address1'}
	{$addresses_style.address2 = 'address_address2'}
	{$addresses_style.city = 'address_city'}
	{$addresses_style.country = 'address_country'}
	{$addresses_style.phone = 'address_phone'}
	{$addresses_style.phone_mobile = 'address_phone_mobile'}
	{$addresses_style.alias = 'address_title'}
{/if}

<script type="text/javascript">
//<![CDATA[
	{literal}
	$(document).ready(function()
	{
		resizeAddressesBox();
	});
	{/literal}
//]]>
</script>
<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">{l s='My addresses'}</h1>
{capture name=path}<a href="{$link->getPageLink('my-account.php', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='My addresses'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

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


<p>{l s='Please configure the desired billing and delivery addresses to be preselected when placing an order. You may also add additional addresses, useful for sending gifts or receiving your order at the office.'}</p>

{if isset($multipleAddresses) && $multipleAddresses}
<div class="addresses">
	<h3>{l s='Your addresses are listed below.'}</h3>
	<p>{l s='Be sure to update them if they have changed.'}</p>
	{assign var="adrs_style" value=$addresses_style}
	{foreach from=$multipleAddresses item=address name=myLoop}
		<ul class="address {if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
			<li class="address_title">{$address.object.alias}</li>
			{foreach from=$address.ordered name=adr_loop item=pattern}
				{assign var=addressKey value=" "|explode:$pattern}
				<li>
				{foreach from=$addressKey item=key name="word_loop"}
				
				
				{if $key != 'Customer:phone' && $key != 'Customer:job_mobile' && $key != 'Customer:phone_mobile'}
					<span class="{if isset($addresses_style[$key])}{$addresses_style[$key]}{/if}">
						{$address.formated[$key]|escape:'htmlall':'UTF-8'}
					</span>
				{/if}
				{/foreach}
				</li>
			{/foreach}
			<li class="address_update"><a href="{$link->getPageLink('address.php', true)}?id_address={$address.object.id|intval}" title="{l s='Update'}">{l s='Update'}</a></li>
			<li class="address_delete"><a href="{$link->getPageLink('address.php', true)}?id_address={$address.object.id|intval}&amp;delete" onclick="return confirm('{l s='Are you sure?'}');" title="{l s='Delete'}">{l s='Delete'}</a></li>
		</ul>
	{/foreach}
	<p class="clear" />
</div>
{else}
	<p class="warning">{l s='No addresses available.'}&nbsp;<a href="{$link->getPageLink('address.php', true)}">{l s='Add new address'}</a></p>
{/if}

<div class="clear address_add"><a href="{$link->getPageLink('address.php', true)}" title="{l s='Add an address'}" class="button_large">{l s='Add an address'}</a></div>

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account.php', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account.php', true)}">{l s='Back to Your Account'}</a></li>
	<li><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>

</div>