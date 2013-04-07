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
*  @version  Release: $Revision: 14514 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}{$product->specificPrice.reduction}{else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};
var ipa_default = {if isset($ipa_default)}{$ipa_default}{else}0{/if};

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
	{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
	customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
	{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
		combinationImages[{$combinationId}] = new Array();
		{foreach from=$combination item='image' name='f_combinationImage'}
			combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
		{/foreach}
	{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
	{foreach from=$images item='image' name='f_defaultImages'}
		combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
	{/foreach}
{/if}

// Translations
var doesntExist = '{l s='The product does not exist in this model. Please choose another.' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please wait...' js=1}';
var fieldRequired = '{l s='Please fill in all required fields, then save the customization.' js=1}';

{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity});
	{/foreach}
	// Colors
	{if $colors|@count > 0}
		{if $product->id_color_default}var id_color_default = {$product->id_color_default|intval};{/if}
	{/if}
{/if}
//]]>
</script>


{if $product->on_sale}
						{* <img src="{$img_dir}onsale_{$lang_iso}.gif" alt="{l s='On sale'}" class="on_sale_img"/> *}

						<span class="on_sale" style="position: absolute; margin-top: 80px; margin-left: 90px; color: #da0f00; text-transform: uppercase; font-weight: bold; float: right; font-size: 14px;">{l s='On sale!'}</span>
{/if}
{include file="$tpl_dir./breadcrumb.tpl"}
{*<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: none; margin-bottom: 5px;">{$product->name|escape:'htmlall':'UTF-8'}</h1>*}

<div id="primary_block" class="clearfix">
	

	{if isset($adminActionDisplay) && $adminActionDisplay}
	<div id="admin-action">
		<p>{l s='This product is not visible to your customers.'}
		<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
		<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad}', 0)"/>
		<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad}', 1)"/>
		</p>
		<div class="clear" ></div>
		<p id="admin-action-result"></p>
		</p>
	</div>
	{/if}





	{if isset($confirmation) && $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
	{/if}

	<!-- right infos-->
	<div id="pb-right-column">
		<!-- product img-->
		<div id="image-block">
          <span class="lupa"></span>
		{if $have_image}
		{*
		<a id="example1" href="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox')}" title="{$product->name|escape:html:'UTF-8'}" class="product_image thickbox">
			<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large')}"
				{if $jqZoomEnabled}class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox')}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" {/if} id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}" />
		</a>
		*}
		<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large')}" {if $jqZoomEnabled}class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox')}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" {/if} id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}" />
		{else}
			<img src="{$img_prod_dir}{$lang_iso}-default-large.jpg" id="bigpic" alt="" title="{$cover.legend|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}" />
		{/if}
		</div>

		{if isset($images) && count($images) > 0}
		<!-- thumbnails -->
		<div id="views_block" {if isset($images) && count($images) < 2}class="hidden"{/if}>
				<div id="thumbs_list">
			<ul>
				{if isset($images)}
					{foreach from=$images item=image name=thumbnails}
					{assign var=imageIds value="`$product->id`-`$image.id_image`"}
					<li id="thumbnail_{$image.id_image}">
						{* <a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox')}" rel="other-views" class="thickbox {if (isset($image.cover) AND $image.cover == 1) OR (!isset($image.cover) AND $smarty.foreach.thumbnails.first)}shown{/if}" title="{$image.legend|htmlspecialchars}"> *}
						<a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox')}" rel="other-views" class="thickbox {if (isset($image.cover) AND $image.cover == 1) OR (!isset($image.cover) AND $smarty.foreach.thumbnails.first)}shown{/if}" title="{$image.legend|htmlspecialchars}">
     						<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium')}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" />
						</a>
					</li>
					{/foreach}
				{/if}
			</ul>
		</div>
				</div>
		{/if}
		{if isset($images) && count($images) > 1}<p class="align_center clear"><span id="wrapResetImages" style="display: none;"><img src="{$img_dir}icon/cancel_16x18.gif" alt="{l s='Cancel'}" width="16" height="18"/> <a id="resetImages" href="{$link->getProductLink($product)}" onclick="$('span#wrapResetImages').hide('slow');return (false);">{l s='Display all pictures'}</a></span></p>{/if}
			</div>

	<!-- left infos-->
	<div id="pb-left-column">
          <h1>{$product->name|escape:'htmlall':'UTF-8'}</h1>
		

		{if isset($colors) && $colors}
		<!-- colors -->
		<div id="color_picker">
			<p>{l s='Pick a color:' js=1}</p>
			<div class="clear"></div>
			<ul id="color_to_pick_list">
			{foreach from=$colors key='id_attribute' item='color'}
				<li><a id="color_{$id_attribute|intval}" class="color_pick" style="background: {$color.value};" onclick="updateColorSelect({$id_attribute|intval});$('#wrapResetImages').show('slow');" title="{$color.name}">{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$color.name}" width="20" height="20" />{/if}</a></li>
			{/foreach}
			</ul>
			<div class="clear"></div>
		</div>
		{/if}

		{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference OR (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
		<!-- add to cart form-->
		<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart.php')}" method="post">

			<!-- hidden datas -->
			<p class="hidden">
				<input type="hidden" name="token" value="{$static_token}" />
				<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
			</p>

			<!-- prices -->
			{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
				
				{if $product->specificPrice AND $product->specificPrice.reduction}
					<p id="old_price"><span class="bold">
					{if $priceDisplay >= 0 && $priceDisplay <= 2}
						{if $productPriceWithoutRedution > $productPrice}
							<span id="old_price_display">{convertPrice price=$productPriceWithoutRedution}</span>
								{if $tax_enabled && $display_tax_label == 1}
									{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
								{/if}
						{/if}
					{/if}
					</span>
					</p>

				{/if}
				{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}
					<p id="reduction_percent">{l s='(price reduced by'} <span id="reduction_percent_display">{$product->specificPrice.reduction*100}</span> %{l s=')'}</p>
				{/if}
				{if $packItems|@count}
					<p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
					<br class="clear" />
				{/if}
				{if $product->ecotax != 0}
					<p class="price-ecotax">{l s='include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for green tax'}
						{if $product->specificPrice AND $product->specificPrice.reduction}
						<br />{l s='(not impacted by the discount)'}
						{/if}
					</p>
				{/if}
				{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
				    {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
					<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
				{/if}
				{*close if for show price*}
			{/if}

			{if isset($groups)}
			<!-- attributes -->
			<div id="attributes">
			{foreach from=$groups key=id_attribute_group item=group}
			{if $group.attributes|@count}
			<p>
				<label for="group_{$id_attribute_group|intval}">{$group.name|escape:'htmlall':'UTF-8'} :</label>
				{assign var="groupName" value="group_$id_attribute_group"}
				<select name="{$groupName}" id="group_{$id_attribute_group|intval}" onchange="javascript:findCombination();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if};">
					{foreach from=$group.attributes key=id_attribute item=group_attribute}
						<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</p>
			{/if}
			{/foreach}
			</div>
			{/if}

			<p id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}><label for="product_reference">{l s='Reference :'}{$product->reference|escape:'htmlall':'UTF-8'}</label></p>
        <!-- короткое описание -->
		{*	
			{if $product->description_short OR $packItems|@count > 0}
		<div id="short_description_block">
			{if $product->description_short}
				<div id="short_description_content">{$product->description_short}</div>
			{/if}
						{if $packItems|@count > 0}
				<h3>{l s='Pack content'}</h3>
				{foreach from=$packItems item=packItem}
					<div class="pack_content">
						{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
						<p>{$packItem.description_short}</p>
					</div>
				{/foreach}
			{/if}
		</div>
		{/if}
		*} <!-- короткое описание -->	
			<!-- quantity wanted -->
			<p id="quantity_wanted_p" style="display: none;">
				<label>{l s='Quantity :'}</label>
				<input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" size="2" maxlength="3" {if $product->minimal_quantity > 1}onkeyup="checkMinimalQuantity({$product->minimal_quantity});"{/if} />
			</p>

			<!-- minimal quantity wanted -->
			<p id="minimal_quantity_wanted_p"{if $product->minimal_quantity <= 1 OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>{l s='You must add '} <b id="minimal_quantity_label">{$product->minimal_quantity}</b> {l s=' as a minimum quantity to buy this product.'}</p>
			{if $product->minimal_quantity > 1}
			<script type="text/javascript">
				checkMinimalQuantity();
			</script>
			{/if}

			<!-- availability -->
			<p id="availability_statut"{if ($product->quantity <= 0 && !$product->available_later && $allow_oosp) OR ($product->quantity > 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<span id="availability_label">{l s='Availability:'}</span>
				<span id="availability_value"{if $product->quantity <= 0} class="warning_inline"{/if}>
					{if $product->quantity <= 0}{if $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{else}{$product->available_now}{/if}
				</span>
			</p>

			<!-- number of item in stock -->
			{if ($display_qties == 1 && !$PS_CATALOG_MODE && $product->available_for_order)}
			<p id="pQuantityAvailable"{if $product->quantity <= 0} style="display: none;"{/if}>
				<span id="quantityAvailable">{$product->quantity|intval}</span>
				<span {if $product->quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='item in stock'}</span>
				<span {if $product->quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='items in stock'}</span>
			</p>
			{/if}
			<!-- Out of stock hook -->
			{if !$allow_oosp}
			<p id="oosHook"{if $product->quantity > 0} style="display: none;"{/if}>
				{$HOOK_PRODUCT_OOS}
			</p>
			{/if}

			<p class="warning_inline" id="last_quantities"{if ($product->quantity > $last_qties OR $product->quantity <= 0) OR $allow_oosp OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if} >{l s='Warning: Last items in stock!'}</p>

			{if $product->online_only}
				<p>{l s='Online only'}</p>
			{/if}
			
			<p>
			

<div id="files" style="margin-bottom: 20px;">
               {foreach from=$attachments item=attachment}
			<li>
			<a href="{$link->getPageLink('attachment.php', true)}?id_attachment={$attachment.id_attachment}" style="font-size: 12px;">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
		    {/foreach}
  </div> 

			
			{literal}
<script type="text/javascript">
$(document).ready(function($) {

    $('#quantity_up{/literal}_{$smarty.get.id_product}{literal}').click(function(){
	    var quantity = $('.cart_quantity_input{/literal}_{$smarty.get.id_product}{literal}').val();
		if(quantity > 0)
		{
		    var count = parseInt(quantity) + 1;
		    $('.cart_quantity_input{/literal}_{$smarty.get.id_product}{literal}').val(count);
			$("#ajax_id_product{/literal}_{$smarty.get.id_product}{literal}").attr({ href: '{/literal}{$link->getPageLink('cart.php')}?qty=' + count + '&id_product={$smarty.get.id_product}&token={$static_token}&add{literal}' });
		}	
	});
	
	$('#quantity_down{/literal}_{$smarty.get.id_product}{literal}').click(function(){
	    var quantity = $('.cart_quantity_input{/literal}_{$smarty.get.id_product}{literal}').val();
        if(quantity > 1)
		{
		    var count = parseInt(quantity) - 1;
		    $('.cart_quantity_input{/literal}_{$smarty.get.id_product}{literal}').val(count);
			$("#ajax_id_product{/literal}_{$smarty.get.id_product}{literal}").attr({ href: '{/literal}{$link->getPageLink('cart.php')}?qty=' + count + '&id_product={$smarty.get.id_product}&token={$static_token}&add{literal}' });
		}
	
	});
});
</script>
{/literal}
             <div class="clear"></div>
        {if $features}
          <div id="feature">
           <div class="teh">{l s='teh'}</div>
           {foreach from=$features item=feature}
			<p>{$feature.name|escape:'htmlall':'UTF-8'}: <span>{$feature.value|escape:'htmlall':'UTF-8'}</span></p>
		{/foreach}
          </div>
        {/if}
			 <div class="cart_quantity2" style="margin-left: 0px;">		
    <span class="cart_quantity">Количество:</span>	
    <a rel="nofollow" class="cart_quantity_down_{$smarty.get.id_product}" id="quantity_down_{$smarty.get.id_product}" href="javascript:void(0);" title="{l s='Уменьшить'}"><img src="{$img_dir}minus.png" alt="{l s='Subtract'}" width="14" height="20" /></a>
		<input size="5" type="text" class="cart_quantity_input_{$smarty.get.id_product}" value='1'  name="qty"/>
    <a rel="nofollow" class="cart_quantity_up_{$smarty.get.id_product}" id="quantity_up_{$smarty.get.id_product}" href="javascript:void(0);" title="{l s='Увеличить'}"><img src="{$img_dir}plus.png" alt="{l s='Add'}" width="14" height="20" /></a>
</div>   


			<p{if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE} style="display: none;"{/if} id="add_to_cart" class="buttons_bottom_block buy_item_product" style="clear: none;">
		
			<P>&nbsp;</P>
			<span class="_price" style="text-align: left; font-size: 14px; padding: 0; color: green; font-family: Trebuchet MS; font-weight: bold; ">
			{if !$priceDisplay || $priceDisplay == 2}
						{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 2)}
						{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
					{elseif $priceDisplay == 1}
						{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 2)}
						{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
					{/if}
					
					{if $product->specificPrice AND $product->specificPrice.reduction AND $productPriceWithoutRedution > $productPrice}
						<span class="discount">{l s='Reduced price!'}</span>
					{/if}
				
					<span class="our_price_display" style="text-align: right; width: 80px;">
					{if $priceDisplay >= 0 && $priceDisplay <= 2}
						<span id="our_price_display">{convertPrice price=$productPrice}</span>
							{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
								{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
							{/if}
					{/if}
					</span>
					{if $priceDisplay == 2}
						<br />
						<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL, 2)}</span>&nbsp;{l s='tax excl.'}</span>   
					{/if}
			</span>
			
			<a class="exclusive2 ajax_add_to_cart_button" id="ajax_id_product_{$product->id}" rel="ajax_id_product_{$product->id}" style="margin-left: 10px; border-right: none;" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product->id}&amp;token={$static_token}&amp;add" title="{l s='Add to cart'}" style="margin-left: 15px; margin-top: -12px; border-right: none;"></a>
		   {* <input type="submit" name="Submit" value="" class="exclusive ajax_add_to_cart_button" id="ajax_id_product_{$product->id_product}" rel="ajax_id_product_{$product->id_product}" style="margin-left: 100px; margin-top: 10px; border-right: none;"/> *}

			</p>

			
			
			<div class="add_ok" id="add_ok" style="margin-left: -8px;">{l s='Товар добавлен в корзину'}
<div class="go_to_cart">{l s='Check Your Cart'}</div>
</div>

			
			{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}
			{*
             <div id="files" style="margin-left: 50px;">
               {foreach from=$attachments item=attachment}
			<li>
			<a href="{$link->getPageLink('attachment.php', true)}?id_attachment={$attachment.id_attachment}" style="font-size: 14px;">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
		    {/foreach}
  </div> 
  *}

  
			  

			<div class="clear"></div>
			
			
			
			
			
        {*
             {if $product->description_short OR $packItems|@count > 0}
		<div id="short_description_block">
			{if $product->description_short}
				<div id="short_description_content">{$product->description_short}</div>
			{/if}
						{if $packItems|@count > 0}
				<h3>{l s='Pack content'}</h3>
				{foreach from=$packItems item=packItem}
					<div class="pack_content">
						{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
						<p>{$packItem.description_short}</p>
					</div>
				{/foreach}
			{/if}
		</div>
		{/if}
		*}
        

		</form>
		{/if}
		{if $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}
	</div>

	
	
	


<link rel="stylesheet" href="/themes/prestashop/card.css" type="text/css" />

{*<script type="text/javascript">*}
{*$(document).ready(function($) {*}
    {*$('#m1').click(function(){*}
	    {*$("#fdm_0").attr({ class: 'act' });*}
		{*$("#fdm_1").attr({ class: 'n-act' });	*}
		{**}
		{*$("#fd_0").show();*}
		{*$("#fd_1").hide();*}
	{*});*}
	{**}
	{*$('#m2').click(function(){*}
	    {*$("#fdm_0").attr({ class: 'n-act' });*}
		{*$("#fdm_1").attr({ class: 'act' });	*}
		{**}
		{*$("#fd_0").hide();*}
		{*$("#fd_1").show();*}
	{*});*}
{*});*}
{*</script>*}

<!--- start menu -->
<h3>Характеристики {$product->name}</h3>
<div id="short_description_content">{$product->description_short}</div>
<div id="short_description_content">{$product->description}</div>

{*<div class="good_center_block">   		*}
		{*<div id="card_submenu">*}
			{*<a name="spec"></a>*}
		{**}
			{*<div align="left" class="block_header">*}
			   	{*<div id="fdm_0" class="act">*}
			         {*<b class="block_line">*}
			            {*<b class="block block_left"></b>				            *}
                        {*<b class="block_header_text"><a href="javascript:void(0);" id="m1">Обзор</a></b>*}
			            {*<b class="block block_right"></b>			                    *}
			         {*</b>*}
		        {*</div>	*}
{*{if $product->description}				*}
		        {*<div id="fdm_1" onclick="display_submenu(1);" class="n-act">*}
			         {*<b class="block_line">*}
			            {*<b class="block block_left"></b>*}
			            {*<b class="block_header_text"><a href="javascript:void(0);" id="m2">Полное описание</a></b>*}
			            {*<b class="block block_right"></b>	            *}
			         {*</b>*}
		         {*</div>*}
{*{/if}		         *}
		    {*</div>*}
		{*</div>	*}
		{*<div class="rbn">*}
			{*<b class="t_rbn"><b class="block block_r_rbn"></b></b>*}
			{*<div class="content_rbn">*}
			    {*<div id="fd_table" style="display: block;"></div>								*}
				{*<table cellspacing="0" cellpadding="0" border="0" class="w100">*}
				{*<tbody>*}
				{*<tr>*}
					{*<td valign="top">*}
					{**}
						{*<div class="pad15" id="fd_0" style="display: block;">*}
								{*<h2 class="size_11 b">Основные характеристики</h2>*}
								{*{if $product->description_short}*}
				{*<div id="short_description_content">{$product->description_short}</div>*}
			{*{/if}*}
								{**}
						{*</div>*}
			{*{if $product->description}								*}
						{*<div id="fd_1" class="pad15" style="display: none;">		*}
						   		{*<h2 class="size_11 b">Подробное описание</h2>*}
						   		{**}
				{*<div id="short_description_content">{$product->description}</div>*}
			{**}
						         				        {**}
					    {*</div>	 *}
            {*{/if}						*}
					{*</td>*}
				{*</tr>*}
				{*</tbody>*}
				{*</table> *}
			{*</div>*}
         {*<b class="b_rbn"><b class="block block_l_rbn"></b><b class="block block_r_rbn"></b></b>*}
		{*</div>									*}
	{*</div>*}
<!-- end menu -->	
 <!-- ///// -->

{if $quantity_discounts}
<!-- quantity discount -->
<ul class="idTabs">
	<li><a style="cursor: pointer" class="selected">{l s='Quantity discount'}</a></li>
</ul>
<div id="quantityDiscount">
	<table class="std">
		<tr>
			{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
				<th>{$quantity_discount.quantity|intval}
				{if $quantity_discount.quantity|intval > 1}
					{l s='quantities'}
				{else}
					{l s='quantity'}
				{/if}
				</th>
			{/foreach}
		</tr>
		<tr>
			{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
				<td>
				{if $quantity_discount.price != 0 OR $quantity_discount.reduction_type == 'amount'}
					-{convertPrice price=$quantity_discount.real_value|floatval}
				{else}
					-{$quantity_discount.real_value|floatval}%
				{/if}
				</td>
			{/foreach}
		</tr>
	</table>
</div>
{/if}

{if isset($accessories) AND $accessories}
<div class="block products_block2" style="padding-top: 15px; margin-bottom: -25px;">
<h2>Аксесcуары</h2>

<div id="product_list">
<ul style="display: block;" class="display"> 
   <li> 		
   <div class="content_block"> 

<!-- MODULE Home Featured Products -->


{literal}
<script type="text/javascript">
$(document).ready(function() {
$('.thickbox').fancybox({
		'hideOnContentClick': true,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
});
</script>
{/literal}	

			<ul id="product_list" style="margin: -44px 0px 0px -20px;">
			{assign var='liHeight' value=342}
			{assign var='nbItemsPerLine' value=3}
			{assign var='nbLi' value=$accessories|@count}
			{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
			{math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
			{foreach from=$accessories item=product name=accessories_list}
				<li class="ajax_block_product {if $smarty.foreach.accessories_list.first}first_item{elseif $smarty.foreach.accessories_list.last}last_item{else}item{/if} {if $smarty.foreach.accessories_list.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.accessories_list.iteration%$nbItemsPerLine == 1}clear{/if} {if $smarty.foreach.accessories_list.iteration > ($smarty.foreach.accessories_list.total - ($smarty.foreach.accessories_list.total % $nbItemsPerLine))}last_line{/if}" style="margin-right: 8px;">
					
					{if $product.on_sale}<h5 class="display" style="position: absolute; margin-left: 40px!important; margin-top: -15px!important; font-size: 14px; color: rgb(233, 0, 0);">РАСПРОДАЖА!</h5>{/if}
					<h5 class="display"><a href="{$product.link}" title="{$product.name|truncate:232:'...'|escape:'htmlall':'UTF-8'}" style="font-size: 13px;">{$product.name|truncate:47:'...'|escape:'htmlall':'UTF-8'}</a></h5>
	                <p class="display" id="product_reference" style="text-align: center; margin-top: -23px;">{if $product.reference}<label for="product_reference">Артикул: </label><span class="editable">{$product.reference}</span>{/if}</p>
	
						
						 <a id="example1" href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image _thickbox"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}" /></a> 
                        <a id="example1" href="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox')}" title="{$product.name|escape:html:'UTF-8'}" class="product_image2 thickbox"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'list')}"/></a>

				  
							
                              <div class="priceblock">
                           {*	{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<p class="price_container"><span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></p>{else}<div style="height:21px;"></div>{/if} *}
						{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
							{if ($product.quantity > 0 OR $product.allow_oosp)}
							
{literal}
<script type="text/javascript">
$(document).ready(function($) {

    $('#quantity_up{/literal}_{$product.id_product}{literal}').click(function(){
	    var quantity = $('.cart_quantity_input{/literal}_{$product.id_product}{literal}').val();
		if(quantity > 0)
		{
		    var count = parseInt(quantity) + 1;
		    $('.cart_quantity_input{/literal}_{$product.id_product}{literal}').val(count);
			$("#ajax_id_product{/literal}_{$product.id_product}{literal}").attr({ href: '{/literal}{$link->getPageLink('cart.php')}?qty=' + count + '&id_product={$product.id_product}&token={$static_token}&add{literal}' });
		}	
	});
	
	$('#quantity_down{/literal}_{$product.id_product}{literal}').click(function(){
	    var quantity = $('.cart_quantity_input{/literal}_{$product.id_product}{literal}').val();
        if(quantity > 1)
		{
		    var count = parseInt(quantity) - 1;
		    $('.cart_quantity_input{/literal}_{$product.id_product}{literal}').val(count);
			$("#ajax_id_product{/literal}_{$product.id_product}{literal}").attr({ href: '{/literal}{$link->getPageLink('cart.php')}?qty=' + count + '&id_product={$product.id_product}&token={$static_token}&add{literal}' });
		}
	
	});
});
</script>
{/literal}
<div class="cart_quantity">		
    <span class="cart_quantity">Количество:</span>	
    <a rel="nofollow" class="cart_quantity_down_{$product.id_product}" id="quantity_down_{$product.id_product}" href="javascript:void(0);" title="{l s='Уменьшить'}"><img src="{$img_dir}minus.png" alt="{l s='Subtract'}" width="14" height="20" /></a>
		<input size="5" type="text" class="cart_quantity_input_{$product.id_product}" value='1'  name="quantity"/> 
    <a rel="nofollow" class="cart_quantity_up_{$product.id_product}" id="quantity_up_{$product.id_product}" href="javascript:void(0);" title="{l s='Увеличить'}"><img src="{$img_dir}plus.png" alt="{l s='Add'}" width="14" height="20" /></a>
</div>							
                           	{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<p class="price_container" style=""><span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></p>{else}<div style="height:21px;"></div>{/if}
							
							<a class="exclusive ajax_add_to_cart_button" id="ajax_id_product_{$product.id_product}" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart'}"></a>
							{else}
							<div style="height:32px;"></div>
							{/if}
						{else}
							<div style="height:32px;"></div>
						{/if}
<div class="add_ok">{l s='Товар добавлен в корзину'}
<div class="go_to_cart">{l s='Check Your Cart'}</div>
</div>
</div>
				
				</li>
				
				
			{/foreach}
			</ul>
		
	
	




</div>
</li>
</ul>
</div>


</div>
{/if}


{$HOOK_PRODUCT_FOOTER}
</div>

{*
<!-- description and features -->
{if $accessories || $HOOK_PRODUCT_TAB}
<div id="more_info_block" class="clear">
	<ul id="more_info_tabs" class="idTabs idTabsShort">
		
		{if isset($accessories) AND $accessories}<li><a href="#idTab4">{l s='Accessories'}</a></li>{/if}
		{$HOOK_PRODUCT_TAB}
	</ul>
	<div id="more_info_sheets" class="sheets align_justify">
	{if $product->description}
		<!-- full description -->
		<div id="idTab1" class="rte">{$product->description}</div>
	{/if}
			{if isset($accessories) AND $accessories}
		<!-- accessories -->
		<ul id="idTab4" class="bullet">
			<div class="block products_block accessories_block clearfix">
				<div class="block_content">
					<ul>
					{foreach from=$accessories item=accessory name=accessories_list}
						{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
						<li class="ajax_block_product {if $smarty.foreach.accessories_list.first}first_item{elseif $smarty.foreach.accessories_list.last}last_item{else}item{/if} product_accessories_description">
							<h5><a href="{$accessoryLink|escape:'htmlall':'UTF-8'}">{$accessory.name|truncate:22:'...':true|escape:'htmlall':'UTF-8'}</a></h5>
							<div class="product_desc">
								<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{$accessory.legend|escape:'htmlall':'UTF-8'}" class="product_image"><img src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'medium')}" alt="{$accessory.legend|escape:'htmlall':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" /></a>
								<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='More'}" class="product_description">{$accessory.description_short|strip_tags|truncate:70:'...'}</a>
							</div>
							<p class="product_accessories_price">
								{if $accessory.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<span class="price">{if $priceDisplay != 1}{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}{/if}</span>{/if}
								<a class="button" href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='View'}">{l s='View'}</a>
								{if ($accessory.allow_oosp || $accessory.quantity > 0) AND $accessory.available_for_order AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
									<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add" rel="ajax_id_product_{$accessory.id_product|intval}" title="{l s='Add to cart'}">{l s='Add to cart'}</a>
								{else}
									<span class="exclusive">{l s='Add to cart'}</span>
									<span class="availability">{if (isset($accessory.quantity_all_versions) && $accessory.quantity_all_versions > 0)}{l s='Product available with different options'}{else}{l s='Out of stock'}{/if}</span>
								{/if}
							</p>
						</li>

					{/foreach}
					</ul>
				</div>
			</div>
		</ul>
	{/if}
	{$HOOK_PRODUCT_TAB_CONTENT}
	</div>
</div>
{/if}
*}










<!-- Customizable products -->
{if $product->customizable}
	<ul class="idTabs">
		<li><a style="cursor: pointer">{l s='Product customization'}</a></li>
	</ul>
	<div class="customization_block">
		<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm">
			<p>
				<img src="{$img_dir}icon/infos.gif" alt="Informations" />
				{l s='After saving your customized product, remember to add it to your cart.'}
				{if $product->uploadable_files}<br />{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
			</p>
			{if $product->uploadable_files|intval}
			<h2>{l s='Pictures'}</h2>
			<ul id="uploadable_files">
				{counter start=0 assign='customizationField'}
				{foreach from=$customizationFields item='field' name='customizationFields'}
					{if $field.type == 0}
						<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
							{if isset($pictures.$key)}<div class="customizationUploadBrowse">
									<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
									<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)}" title="{l s='Delete'}" >
										<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
									</a>
								</div>{/if}
							<div class="customizationUploadBrowse"><input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="customization_block_input {if isset($pictures.$key)}filled{/if}" />{if $field.required}<sup>*</sup>{/if}
							<div class="customizationUploadBrowseDescription">{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your hard drive'}{/if}</div></div>
						</li>
						{counter}
					{/if}
				{/foreach}
			</ul>
			{/if}
			<div class="clear"></div>
			{if $product->text_fields|intval}
			<h2>{l s='Texts'}</h2>
			<ul id="text_fields">
				{counter start=0 assign='customizationField'}
				{foreach from=$customizationFields item='field' name='customizationFields'}
					{if $field.type == 1}
						<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
							{if !empty($field.name)}{$field.name}{/if}{if $field.required}<sup>*</sup>{/if}<textarea type="text" name="textField{$field.id_customization_field}" id="textField{$customizationField}" rows="1" cols="40" class="customization_block_input" />{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if}</textarea>
						</li>
						{counter}
					{/if}
				{/foreach}
			</ul>
			{/if}
			<p style="clear: left;" id="customizedDatas">
				<input type="hidden" name="ipa_customization" id="ipa_customization" value="{$ipa_customization}" />
				<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
				<input type="hidden" name="submitCustomizedDatas" value="1" />
				<input type="button" class="button" value="{l s='Save'}" onclick="javascript:saveCustomization()" />
				<span id="ajax-loader" style="display:none"><img src="{$img_ps_dir}loader.gif" alt="loader" /></span>
			</p>
		</form>
		<p class="clear required"><sup>*</sup> {l s='required fields'}</p>
	</div>
{/if}

{if $packItems|@count > 0}
	<div>
		<h2>Рекомендуемые товары {* {l s='Pack content'} *}</h2>
		{include file="$tpl_dir./product-list.tpl" products=$packItems}
	</div>
{/if}

{/if}

