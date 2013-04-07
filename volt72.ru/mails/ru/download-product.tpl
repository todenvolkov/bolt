<ul>
{foreach from=$virtualProducts item=product}
	<li>
		<a href="{$product.link}">{$product.name}</a>
		{if isset($product.deadline)}
			окончание {$product.deadline}
		{/if}
		{if isset($product.downloadable)}
			скачать {$product.downloadable} раз
		{/if}
	</li>
{/foreach}
</ul>