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

{if $PS_CATALOG_MODE}
	<h2 id="cart_title">{l s='Your shopping cart' mod='onepagecheckout'}</h2>
	<p class="warning">{l s='This store has not accepted your new order.' mod='onepagecheckout'}</p>
{else}
<script type="text/javascript">
	// <![CDATA[
	var baseDir = '{$base_dir_ssl}';
	var imgDir = '{$img_dir}';
	var authenticationUrl = '{$link->getPageLink("authentication.php", true)}';
	var orderOpcUrl = '{$opckt_script}';
	var historyUrl = '{$link->getPageLink("history.php", true)}';
	var guestTrackingUrl = '{$link->getPageLink("guest-tracking.php", true)}';
	var addressUrl = '{$link->getPageLink("address.php", true)}';
	var orderProcess = 'order-opc';
	var guestCheckoutEnabled = {$PS_GUEST_CHECKOUT_ENABLED|intval};
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var displayPrice = {$priceDisplay};
	var taxEnabled = {$use_taxes};
	var conditionEnabled = {$conditions|intval};
	var countries = new Array();
	var countriesNeedIDNumber = new Array();
	var countriesNeedZipCode = new Array();
	var vat_management = {$vat_management|intval};
	
	var txtWithTax = "{l s='(tax incl.)' mod='onepagecheckout'}";
	var txtWithoutTax = "{l s='(tax excl.)' mod='onepagecheckout'}";
	var txtHasBeenSelected = "{l s='has been selected' mod='onepagecheckout'}";
	var txtNoCarrierIsSelected = "{l s='No carrier has been selected' mod='onepagecheckout'}";
	var txtNoCarrierIsNeeded = "{l s='No carrier is needed for this order' mod='onepagecheckout'}";
	var txtConditionsIsNotNeeded = "{l s='No terms of service must be accepted' mod='onepagecheckout'}";
	var txtTOSIsAccepted = "{l s='Terms of service is accepted' mod='onepagecheckout'}";
	var txtTOSIsNotAccepted = "{l s='Terms of service have not been accepted' mod='onepagecheckout'}";
	var txtThereis = "{l s='There is' mod='onepagecheckout'}";
	var txtErrors = "{l s='error(s)' mod='onepagecheckout'}";
	var txtDeliveryAddress = "{l s='Delivery address' mod='onepagecheckout'}";
	var txtInvoiceAddress = "{l s='Invoice address' mod='onepagecheckout'}";
	var txtModifyMyAddress = "{l s='Modify my address' mod='onepagecheckout'}";
	var txtInstantCheckout = "{l s='Instant checkout' mod='onepagecheckout'}";
	var errorCarrier = "{$errorCarrier}";
	var errorTOS = "{$errorTOS}";
        var errorPayment = "{l s='Please select payment method.' mod='onepagecheckout'}";
	var checkedCarrier = "{if isset($checked)}{$checked}{else}0{/if}";

	var addresses = new Array();
	var isLogged = {$isLogged|intval};
	var isGuest = {$isGuest|intval};
	var isVirtualCart = {$isVirtualCart|intval};
	var isPaymentStep = {$isPaymentStep|intval};

	{*{foreach from=$countries item=v}acz({$v.id_country},{$v.id_zone});{/foreach}*}
        
        var opc_scroll_cart = "{$opc_config.scroll_cart && $productNumber}";
        {*var opc_scroll_info = "{$opc_config.scroll_info}";*}
        var opc_scroll_info = "1"; {*allways turned on-configurable option removed; if scrolling would be turned off, any addStuff module would be sufficient*}
        var opc_scroll_summary = "{$opc_config.scroll_summary && $productNumber}";
        var opc_page_fading = "{$opc_config.page_fading && $productNumber}";
        var opc_fading_duration = "{$opc_config.fading_duration}";
        var opc_fading_opacity = "{$opc_config.fading_opacity}";
        var opc_sample_values = "{$opc_config.sample_values}";
        var opc_inline_validation = "{$opc_config.inline_validation}";
        var opc_validation_checkboxes = "{$opc_config.validation_checkboxes}";
        var opc_display_info_block = '{$opc_config.display_info_block}';
        var opc_info_block_content = '{$info_block_content|regex_replace:"/[\r\t\n]/":" "|regex_replace:"/\'/":"\\'"}';
        var opc_before_info_element = '{$opc_config.before_info_element}'; 
        var opc_check_number_in_address = '{$opc_config.check_number_in_address}'; 
        var opc_capitalize_fields = '{$opc_config.capitalize_fields}'; 
        var opc_relay_update = '{$opc_config.update_payments_relay}'; 
        var opc_hide_carrier = '{$opc_config.hide_carrier}'; 
        var opc_hide_payment = '{$opc_config.hide_payment}'; 

	// Some more translations
	var ntf_close = "{l s='Close' mod='onepagecheckout'}";
	var ntf_number_in_address_missing = "{l s='Number in address is missing, are you sure you don\'t have one?' mod='onepagecheckout'}";
	//]]>
</script>
<h1><b>{l s='your' mod='onepagecheckout'}</b> {l s='Shopping cart summary' mod='onepagecheckout'}</h1>
<div id="cart">
	{if $productNumber}
		<!-- Shopping Cart -->
{*		<div style="border: 1px solid gray">{include file="shopping-cart.tpl"}</div> *}
                {include file="shopping-cart.tpl"}
		<!-- END Shopping Cart -->
		{*{if $isLogged AND !$isGuest}*}
			<!-- Create account / Guest account / Login block -->
			{*<div style="border: 1px solid gray">{include file="order-opc-new-account.tpl"}</div>*}
                        {include file="order-opc-new-account.tpl"}
			<!-- END Create account / Guest account / Login block -->
		<!-- Carrier -->
		{*<div style="border: 1px solid gray">{include file="order-carrier.tpl"}</div>*}
                {include file="order-carrier.tpl"}
		<!-- END Carrier -->
	
		<!-- Payment -->
		{*<div style="border: 1px solid gray">{include file="order-payment.tpl"}</div>*}
                {include file="order-payment.tpl"}
               
           <div id="cartfooter">
          
					<div id="totalcart">{l s='Total products:' mod='onepagecheckout'}
					<span id="total_product">{displayPrice price=$total_products}</span>
				     </div>
            <input type="button" class="cartbutton" title="{l s='I confirm my order' mod='onepagecheckout'}" value="" onclick="paymentModuleConfirm();" /> 
            <a href="{if (isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order.php')) || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index.php')}{else}{$smarty.server.HTTP_REFERER|escape:'htmlall':'UTF-8'|secureReferrer}{/if}" class="button_large" title="{l s='Continue shopping'}"></a>
     
           </div>
		<!-- END Payment -->
	{else}
		<h2>{l s='Your shopping cart' mod='onepagecheckout'}</h2>
		<p class="warning">{l s='Your shopping cart is empty.' mod='onepagecheckout'}</p>
	{/if}
{/if}</div>
