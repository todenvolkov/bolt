<p>{l s='Оплата успешно получена' mod='rbkmoney'}</p>
<h2>{l s='Список оплаченых товаров' mod='rbkmoney'}</h2>
<ul>
{foreach from=$products item=product}
	<li>{if $product.download_hash}
		<a href="{$base_dir}get-file.php?key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}" title="{l s='download this product'}">
			<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Download product'}" />
		</a>
		<a href="{$base_dir}get-file.php?key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}" title="{l s='download this product'}">
			{l s='Скачать' mod='rbkmoney'} {$product.product_name|escape:'htmlall':'UTF-8'}
		</a>
		{else}
			{$product.product_name|escape:'htmlall':'UTF-8'}
		{/if}
	</li>
{/foreach}
</ul>
