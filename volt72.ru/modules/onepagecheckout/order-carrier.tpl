{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{* TODO: remove - povodne urcene len non-opc
{if !$opc}
	<script type="text/javascript">
	<!--
		var baseDir = '{$base_dir_ssl}';
	-->
	</script>
	<script type="text/javascript">
	var msg = "{l s='You must agree to the terms of service before continuing.' mod='onepagecheckout' js=1}";
	{literal}
	function acceptCGV()
	{
		if ($('#cgv').length && !$('input#cgv:checked').length)
		{
			alert(msg);
			return false;
		}
		else
			return true;
	}
	{/literal}
	</script>
{/if}
*}

{* TODO: remove; kontrola je v order-opc.js; if !$virtual_cart && $giftAllowed && $cart->gift == 1}
<script type="text/javascript">
{literal}
// <![CDATA[
    $('document').ready( function(){
        $('#gift_div').toggle('slow');
    });
//]]>
{/literal}
</script>
{/if*}

{if isset($opc_config) && isset($opc_config.hide_carrier)}
  {assign var=singleCarrier value=$opc_config.hide_carrier=="1" && isset($carriers) && $carriers && count($carriers)==1}
{else}
  {assign var=singleCarrier value=isset($carriers) && $carriers && count($carriers)==1}
{/if}
  

{assign var=displayForm value=(!$singleCarrier && (!isset($isVirtualCart) || !$isVirtualCart)) || (isset($opc_config.order_msg) && $opc_config.order_msg) || ($conditions AND $cms_id)}
<div id="rcarrier">

<form id="carriers_section" class="std" action="#" {if !$displayForm}style="display:none"{/if}>
    

{if !isset($isVirtualCart) || !$isVirtualCart}
<div class="h3">{l s='Choose your delivery method' mod='onepagecheckout'}<sup>*</sup></div>
{/if}

<div id="opc_delivery_methods" class="opc-main-block">
	<div id="opc_delivery_methods-overlay" class="opc-overlay" style="display: none;"></div>

{if $virtual_cart}
	<input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
{else}
	<div id="HOOK_BEFORECARRIER">{if isset($carriers)}{$HOOK_BEFORECARRIER}{/if}</div>
	{if isset($isVirtualCart) && $isVirtualCart}
{*	<p class="warning">{l s='No carrier needed for this order' mod='onepagecheckout'}</p> *}
	{else}

	<p class="warning" id="noCarrierWarning" {if isset($carriers) && $carriers && count($carriers)}style="display:none;"{/if}>{l s='There are no carriers available that deliver to this address.' mod='onepagecheckout'}</p>
	<table id="carrierTable" {if !isset($carriers) || !$carriers || !count($carriers) || $singleCarrier}style="display:none;"{/if}>
	
	
		{if isset($carriers)}
			{foreach from=$carriers item=carrier name=myLoop}
				<tr style="float: left;">
					<td class="carrier_action radio">
						<input type="radio" name="id_carrier" value="{$carrier.id_carrier|intval}" id="id_carrier{$carrier.id_carrier|intval}"  onclick="updateCarrierSelectionAndGift();" {if $carrier.id_carrier == $checked || $carriers|@count == 1}checked="checked"{/if} />
					</td>
					<td class="carrier_name">
						<label for="id_carrier{$carrier.id_carrier|intval}">
							{if $carrier.img}<img src="{$carrier.img|escape:'htmlall':'UTF-8'}" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" />{else}{$carrier.name|escape:'htmlall':'UTF-8'}{/if}
						</label>
					</td>
				</tr>
			{/foreach}
			<tr id="HOOK_EXTRACARRIER">{$HOOK_EXTRACARRIER}</tr>
		{/if}
		
	</table>
	<div style="display: none;" id="extra_carrier"></div>
	
        
        	{if $recyclablePackAllowed}
	<p class="checkbox">
		<input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} />
		<label for="recyclable">{l s='I agree to receive my order in recycled packaging' mod='onepagecheckout'}.</label>
	</p>
	{/if}
		{if $giftAllowed}
		{if !isset($opc_config.compact_form) || !$opc_config.compact_form}<h4 class="gift_title">{l s='Gift' mod='onepagecheckout'}</h4>{/if}
		<p class="checkbox">
			<input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} onclick="$('#gift_div').toggle('slow');" />
			<label for="gift">{l s='I would like the order to be gift-wrapped.' mod='onepagecheckout'}</label>
			{if $gift_wrapping_price > 0}
				({l s='Additional cost of' mod='onepagecheckout'}
				<strong class="price" id="gift-price">
					{if $priceDisplay == 1}{convertPrice price=$total_wrapping_tax_exc_cost}{else}{convertPrice price=$total_wrapping_cost}{/if}
				</strong>
				{if $use_taxes}{if $priceDisplay == 1} {l s='(tax excl.)' mod='onepagecheckout'}{else} {l s='(tax incl.)' mod='onepagecheckout'}{/if}{/if})
			{/if}
		</p>
				{/if}
	{/if}
{/if}



{if $conditions AND $cms_id}
    {if !isset($opc_config.compact_form) || !$opc_config.compact_form}
	<h4 class="condition_title">{l s='Terms of service' mod='onepagecheckout'}</h4>
        {/if}
	<div id="opc_tos_errors" class="error" style="display: none;"></div>
	<p class="checkbox">
		<input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
                <label for="cgv">{l s='I agree to the terms of service and adhere to them unconditionally.' mod='onepagecheckout'}</label> <a href="{$link_conditions}" class="iframe">{l s='(read)' mod='onepagecheckout'}</a> <sup>*</sup>{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_{if $checkedTOS}ok{else}nok{/if}"></span>{/if}
	</p>
	<script type="text/javascript">$('a.iframe').fancybox();</script>
{/if}

</div>
</fieldset>
</form>
</div>