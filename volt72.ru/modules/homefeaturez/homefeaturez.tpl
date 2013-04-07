<!-- MODULE Home Featurez Products -->
<div id="featured-products_block_center" class="block products_block">
	<h4>{l s='Featured products' mod='homefeaturez'}</h4>
	{if isset($products) AND $products}
		<div class="block_content">
			{* if you have problems with your theme layout you should to edit these height values*}
			{if $conf.HOME_FEATURED_TITLE}{assign var='TitleHeight' value=38}{else}{assign var='TitleHeight' value=0}{/if}	{* product title *}
			{if $conf.HOME_FEATURED_DESCR}{assign var='DescrHeight' value=99}{else}{assign var='DescrHeight' value=0}{/if}		{* product description *}
			{if $conf.HOME_FEATURED_VIEW}{assign var='ViewHeight' value=26}{else}{assign var='ViewHeight' value=0}{/if}		{* product "view" button *}
			{if $conf.HOME_FEATURED_CART}{assign var='CartHeight' value=26}{else}{assign var='CartHeight' value=0}{/if}		{* product "add to cart" button *}
			{if $conf.HOME_FEATURED_PRICE}{assign var='PriceHeight' value=21}{else}{assign var='PriceHeight' value=0}{/if}	{* product price *}
			
			{* Use FaultHeight parametr to adjust the liHeight *}			
			{math equation="TitleHeight+DescrHeight+ViewHeight+CartHeight+PriceHeight+ImageHeight+FaultHeight"
				TitleHeight=$TitleHeight
				DescrHeight=$DescrHeight
				ViewHeight=$ViewHeight
				CartHeight=$CartHeight
				PriceHeight=$PriceHeight
				ImageHeight=$homeSize.height+3
				FaultHeight=0		
				assign=liHeight}

			{assign var='nbItemsPerLine' value=4}
			{assign var='nbLi' value=$products|@count}
			{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
			{math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
			<ul style="height:{$ulHeight}px;">
			{foreach from=$products item=product name=homefeaturezProducts}
				<li class="ajax_block_product {if $smarty.foreach.homefeaturezProducts.first}first_item{elseif $smarty.foreach.homefeaturezProducts.last}last_item{else}item{/if} {if $smarty.foreach.homefeaturezProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.homefeaturezProducts.iteration%$nbItemsPerLine == 1}clear{/if} {if $smarty.foreach.homefeaturezProducts.iteration > ($smarty.foreach.homefeaturezProducts.total - ($smarty.foreach.homefeaturezProducts.total % $nbItemsPerLine))}last_line{/if}">
					{if $conf.HOME_FEATURED_TITLE}
						<h5><a href="{$product.link}" title="{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:27:'...'|escape:'htmlall':'UTF-8'}</a></h5>
					{/if}
					{if $conf.HOME_FEATURED_DESCR}
						<div class="product_desc"><a href="{$product.link}" title="{l s='More' mod='homefeaturez'}">{$product.description_short|strip_tags|truncate:130:'...'}</a></div>
					{/if}
					<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
					<div>
						{if $product.show_price AND !isset($restricted_country_mode) AND (!$PS_CATALOG_MODE && $conf.HOME_FEATURED_PRICE)}<p class="price_container"><span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></p>{else}{*<div style="height:21px;"></div>*}{/if}
						{if $conf.HOME_FEATURED_VIEW}
							<a class="button" href="{$product.link}" title="{l s='View' mod='homefeaturez'}">{l s='View' mod='homefeaturez'}</a>
						{/if}
						{if ($product.id_product_attribute == 0 OR $add_prod_display == 1) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND (!$PS_CATALOG_MODE && $conf.HOME_FEATURED_CART)}
							{if ($product.quantity > 0 OR $product.allow_oosp)}
							<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='homefeaturez'}">{l s='Add to cart' mod='homefeaturez'}</a>
							{else}
							<span class="exclusive">{l s='Add to cart' mod='homefeaturez'}</span>
							{/if}
						{else}
							{*<div style="height:23px;"></div>*}
						{/if}
					</div>
				</li>
			{/foreach}
			</ul>
		</div>
	{else}
		<p>{l s='No featured products' mod='homefeaturez'}</p>
	{/if}
</div>
<!-- /MODULE Home Featurez Products -->
