
{capture name=path}{l s='Your shopping cart'}{/capture}




{assign var='current_step' value='summary'}

{include file="$tpl_dir./errors.tpl"}

{if isset($empty)}
	<p class="warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
	<p class="warning">{l s='This store has not accepted your new order.'}</p>
{else}
	<script type="text/javascript">
	// <![CDATA[
	var baseDir = '{$base_dir_ssl}';
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product'}";
	var txtProducts = "{l s='products'}";
	// ]]>
	</script>
	<p style="display:none" id="emptyCartWarning" class="warning">{l s='Your shopping cart is empty.'}</p>
<div class="qty">{l s='Your shopping cart contains' mod='onepagecheckout'} <span>{$productNumber} {if $productNumber == 1}{l s='product' mod='onepagecheckout'}{else}{l s='products' mod='onepagecheckout'}{/if}</span></div>
<div class="login"><a href="#" id="openLoginFormBlock">{l s='Click here' mod='onepagecheckout'}</a> {l s='Already registered?' mod='onepagecheckout'} </div>
<div id="login_form_content" style="display:none;">
<form action="{$link->getPageLink('authentication.php', true)}?back=order-opc.php" method="post" id="login_form" class="std" {if ($isLogged && !$isGuest)}style="display:none;"{/if}>

                <!-- Error return block -->
                <div id="opc_login_errors" class="error" style="display:none;"></div>
                <!-- END Error return block -->

                <p class="required text">
                    <label for="login_email">{l s='E-mail address' mod='onepagecheckout'}</label>
                    <input type="text" class="text" id="login_email" name="email" value="" />
                </p>
                <p class="required text">
                    <label for="login_passwd">{l s='Password' mod='onepagecheckout'}</label>
                    <input type="password" class="text" id="login_passwd" name="login_passwd" value="" />
                </p>

                <p class="submit">
                {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
                <input type="submit" id="SubmitLoginOpc" name="SubmitLogin" class="button" value="{l s='Log in' mod='onepagecheckout'}" />
            </p>
	    <p class="lost_password"><a href="{$link->getPageLink('password.php', true)}">{l s='Forgot your password?' mod='onepagecheckout'}</a></p>
</form>
        </div>



<div id="order-detail-content" class="table_block">
   <ul class="thead">
     <li class="cart_product first_item">{l s='Product'}</li>
	<li class="cart_description item">{l s='Description'}</li>
	<li class="cart_unit item">{l s='Unit price'}</li>
	<li class="cart_quantity item">{l s='Qty'}</li>
	<li class="cart_total item">{l s='Total'}</li>
     <li class="delete last_item"><img src="{$img_dir}icon/delete.gif"></li>
    </ul>


	<table id="cart_summary" class="std">
		
		<tfoot>
						<tr class="cart_total_voucher" {if $total_discounts == 0}style="display: none;"{/if}>
				<td colspan="6" style="color:#424242">
				{if $use_taxes}
					{if $priceDisplay}
						{l s='Total vouchers'}{if $display_tax_label} {l s='(tax excl.)'}{/if}{l s=':'}
					{else}
						{l s='Total vouchers'}{if $display_tax_label} {l s='(tax incl.)'}{/if}{l s=':'}
					{/if}
				{else}
					{l s='Total vouchers:'}
				{/if}
				</td>
				<td class="price-discount" id="total_discount" style="font-weight: bold;">
				{if $use_taxes}
					{if $priceDisplay}
						{displayPrice price=$total_discounts_tax_exc}
					{else}
						{displayPrice price=$total_discounts}
					{/if}
				{else}
					{displayPrice price=$total_discounts_tax_exc}
				{/if}
				</td>
			</tr>
			<tr class="cart_total_voucher" {if $total_wrapping == 0}style="display: none;"{/if}>
				<td colspan="6">
				{if $use_taxes}
					{if $priceDisplay}
						{l s='Total gift-wrapping'}{if $display_tax_label} {l s='(tax excl.)'}{/if}{l s=':'}
					{else}
						{l s='Total gift-wrapping'}{if $display_tax_label} {l s='(tax incl.)'}{/if}{l s=':'}
					{/if}
				{else}
					{l s='Total gift-wrapping:'}
				{/if}
				</td>
				<td class="price-discount" id="total_wrapping">
				{if $use_taxes}
					{if $priceDisplay}
						{displayPrice price=$total_wrapping_tax_exc}
					{else}
						{displayPrice price=$total_wrapping}
					{/if}
				{else}
					{displayPrice price=$total_wrapping_tax_exc}
				{/if}
				</td>
			</tr>
			

			
			<tr class="cart_free_shipping" {if $free_ship <= 0 || $isVirtualCart} style="display: none;" {/if}>
					<td colspan="6" style="white-space: normal;">{l s='Remaining amount to be added to your cart in order to obtain free shipping:'}</td>
					<td id="free_shipping" class="price">{displayPrice price=$free_ship}</td>
				</tr>
		</tfoot>
		
		{foreach from=$products item=product name=productLoop}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{* Display the product line *}
			{include file="$tpl_dir./shopping-cart-product-line.tpl"} 
                
			{* Then the customized datas ones*}
			{if isset($customizedDatas.$productId.$productAttributeId)}
				{foreach from=$customizedDatas.$productId.$productAttributeId key='id_customization' item='customization'}
					<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" class="alternate_item cart_item">
						<td colspan="5">
							{foreach from=$customization.datas key='type' item='datas'}
								{if $type == $CUSTOMIZE_FILE}
									<div class="customizationUploaded">
										<ul class="customizationUploaded">
											{foreach from=$datas item='picture'}<li><img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" /></li>{/foreach}
										</ul>
									</div>
								{elseif $type == $CUSTOMIZE_TEXTFIELD}
									<ul class="typedText">
										{foreach from=$datas item='textField' name='typedText'}<li>{if $textField.name}{$textField.name}{else}{l s='Text #'}{$smarty.foreach.typedText.index+1}{/if}{l s=':'} {$textField.value}</li>{/foreach}
									</ul>
								{/if}
							{/foreach}
						</td>
						<td class="cart_quantity">
							<div style="float:right">
								<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart.php', true)}?delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" title="{l s='Delete this customization'}" width="11" height="13" class="icon" /></a>
							</div>
							<div id="cart_quantity_button" style="float:left">
							<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart.php', true)}?add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="14" height="9" /></a><br />
							{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
							<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart.php', true)}?add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}" title="{l s='Subtract'}">
								<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
							</a>
							{else}
							<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="#" title="{l s='Subtract'}">
								<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
							</a>
							{/if}
							</div>
							<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_hidden"/>
							<input size="2" type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}"/>
						</td>
						<td class="cart_total"></td>
					</tr>
					{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
				{/foreach}
				{* If it exists also some uncustomized products *}
				{if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl"}{/if}
			{/if}
		{/foreach}
		<div id="shoppingcart"> </div>
	{if sizeof($discounts)}
		<tbody>
		{foreach from=$discounts item=discount name=discountLoop}
			<tr class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}" style="display: none;">
				<td class="cart_discount_name" colspan="2">{$discount.name}</td>
				<td class="cart_discount_description" colspan="3">{$discount.description}</td>
				<td class="cart_discount_delete"><a href="{if $opc}{$link->getPageLink('order-opc.php', true)}{else}{$link->getPageLink('order.php', true)}{/if}?deleteDiscount={$discount.id_discount}" title="{l s='Delete'}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="icon" width="11" height="13" /></a></td>
				<td class="cart_discount_price"><span class="price-discount">
					{if $discount.value_real > 0}
						{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
					{/if}
				</span></td>
			</tr>
		{/foreach}
		</tbody>
	{/if}
	</table>
</div>

{if $voucherAllowed}
<div id="cart_voucher" class="table_block">
	{if isset($errors_discount) && $errors_discount}
		<ul class="error">
		{foreach from=$errors_discount key=k item=error}
			<li>{$error|escape:'htmlall':'UTF-8'}</li>
		{/foreach}
		</ul>
	{/if}
	<form action="{if $opc}{$link->getPageLink('order-opc.php', true)}{else}{$link->getPageLink('order.php', true)}{/if}" method="post" id="voucher">
		
		
			<p>
				<label for="discount_name">{l s='Code:'}</label>
				<input type="text" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" class="voucher"/>
			</p>
			<p class="submit"><input type="hidden" name="submitDiscount" /><input type="submit" name="submitAddDiscount" value="{l s='Add'}" class="button" /></p>
		{if $displayVouchers}
			<h4>{l s='Take advantage of our offers:'}</h4>
			<div id="display_cart_vouchers">
			{foreach from=$displayVouchers item=voucher}
				<span onclick="$('#discount_name').val('{$voucher.name}');return false;" class="voucher_name">{$voucher.name}</span> - {$voucher.description} <br />
			{/foreach}
			</div>
		{/if}
		
	</form>
</div>
{/if}

{if (($carrier->id AND !isset($virtualCart)) OR $delivery->id OR $invoice->id) AND !$opc}
<div class="order_delivery">
	{if $delivery->id}
	<ul id="delivery_address" class="address item">
		<li class="address_title">{l s='Delivery address'}</li>
		{if $delivery->company}<li class="address_company">{$delivery->company|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_name">{$delivery->firstname|escape:'htmlall':'UTF-8'} {$delivery->lastname|escape:'htmlall':'UTF-8'}</li>
		<li class="address_address1">{$delivery->address1|escape:'htmlall':'UTF-8'}</li>
		{if $delivery->address2}<li class="address_address2">{$delivery->address2|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_city">{$delivery->postcode|escape:'htmlall':'UTF-8'} {$delivery->city|escape:'htmlall':'UTF-8'}</li>
		<li class="address_country">{$delivery->country|escape:'htmlall':'UTF-8'} {if $delivery_state}({$delivery_state|escape:'htmlall':'UTF-8'}){/if}</li>
	</ul>
	{/if}
	{if $invoice->id}
	<ul id="invoice_address" class="address alternate_item">
		<li class="address_title">{l s='Invoice address'}</li>
		{if $invoice->company}<li class="address_company">{$invoice->company|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_name">{$invoice->firstname|escape:'htmlall':'UTF-8'} {$invoice->lastname|escape:'htmlall':'UTF-8'}</li>
		<li class="address_address1">{$invoice->address1|escape:'htmlall':'UTF-8'}</li>
		{if $invoice->address2}<li class="address_address2">{$invoice->address2|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_city">{$invoice->postcode|escape:'htmlall':'UTF-8'} {$invoice->city|escape:'htmlall':'UTF-8'}</li>
		<li class="address_country">{$invoice->country|escape:'htmlall':'UTF-8'} {if $invoice_state}({$invoice_state|escape:'htmlall':'UTF-8'}){/if}</li>
	</ul>
	{/if}
	{if $carrier->id AND !isset($virtualCart)}
	<div id="order_carrier">
		<h4>{l s='Carrier:'}</h4>
		{if isset($carrierPicture)}<img src="{$img_ship_dir}{$carrier->id}.jpg" alt="{l s='Carrier'}" />{/if}
		<span>{$carrier->name|escape:'htmlall':'UTF-8'}</span>
	</div>
	{/if}
</div>
{/if}


<br class="clear">
<p class="cart_navigation_extra">
	<span id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA}</span>
</p>
{/if}


