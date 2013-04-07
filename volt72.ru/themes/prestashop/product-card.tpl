{strip}
<li class="ajax_block_product {if $smarty.foreach.homeFeaturedProducts.first}first_item{elseif $smarty.foreach.homeFeaturedProducts.last}last_item{else}item{/if} {if $smarty.foreach.homeFeaturedProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.homeFeaturedProducts.iteration%$nbItemsPerLine == 1}clear{/if} {if $smarty.foreach.homeFeaturedProducts.iteration > ($smarty.foreach.homeFeaturedProducts.total - ($smarty.foreach.homeFeaturedProducts.total % $nbItemsPerLine))}last_line{/if}">
    {if $product.on_sale}
        <h5 class="display"
            style="position: absolute; margin-left: 40px!important; margin-top: -15px!important; font-size: 14px; color: rgb(233, 0, 0);">
            <a href="{$product.link}"
               style="position: absolute; margin-left: 10px!important; font-size: 14px; color: rgb(233, 0, 0); font-weight: bold;">РАСПРОДАЖА!</a>
        </h5>
    {/if}
    <div itemscope itemtype="http://data-vocabulary.org/Product">
        <h5 class="display"><a href="{$product.link}"
                               title="{$product.name|truncate:500:'...'|escape:'htmlall':'UTF-8'}"><span
                itemprop="name">{$product.name|truncate:500:'...'|escape:'htmlall':'UTF-8'}</span></a>
        </h5>


        <a id="example1" href="{$product.link}"
           title="{$product.name|escape:html:'UTF-8'} купить" class="product_image _thickbox">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}"
                 height="{$homeSize.height}" width="{$homeSize.width}"
                 alt="{$product.name|escape:html:'UTF-8'} фото"/> </a>
        <a id="example1"
           href="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox')}"
           title="{$product.name|escape:html:'UTF-8'} фото" class="product_image2 thickbox">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'list')}"
                 alt="{$product.name|escape:html:'UTF-8'} фото"/>
        </a>

        <div class="display" id="product_reference"
             style="text-align: left; margin-top: 0px;">{if $product.reference}<label
                for="product_reference">Артикул: <span itemprop="identifier"
                                                       content="mpn:{$product.reference}">{$product.reference}</span></label>{/if}
        </div>
  <div style="display:block;width:100%;   max-height: 77px;
      min-height: 77px;"><p>{$product.description_short|strip_tags|truncate:180:'...'|escape:'htmlall':'UTF-8'}</p></div>
        {if $product.on_sale}<h5 class="thumb"
                                 style="position: absolute; margin-left: 280px!important; margin-top: -11px!important; font-size: 14px; color: rgb(233, 0, 0);">
            РАСПРОДАЖА!</h5>{/if}



        <h5 class="thumb"><a href="{$product.link}"
                             title="{$product.name|truncate:500:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:500:'...'|escape:'htmlall':'UTF-8'}</a>
        </h5>

        <div id="feature2">
            <p id="product_reference">{if $product.reference}<label
                    for="product_reference">Артикул: {$product.reference}</label>{/if}
            </p>
        </div>
        {if $product.description_short}
            <div id="feature2" style="height: 90px;">
                <span itemprop="description">{$product.description_short|truncate:350:'...'|strip_tags}</span>
            </div>
            <div id="feature2" style="margin-left: 572px; margin-bottom: -16px; z-index: 1000; position: absolute;">
                <a href="{$product.link}" style="color: #0071B7;" title="Подробнее"><b>Подробнее...</b></a>
            </div>
            {else}
            <div id="feature2" style="margin-top: 85px;"></div>
        {/if}
        <div class="priceblock">
            {if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
                {if ($product.quantity > 0 OR $product.allow_oosp)}
                {*//TODO - remove JS from every product!*}

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
                        <a rel="nofollow" class="cart_quantity_down_{$product.id_product}"
                           id="quantity_down_{$product.id_product}" href="javascript:void(0);"
                           title="{l s='Уменьшить'}"><img src="{$img_dir}minus.png"
                                                          alt="{l s='Subtract'}"
                                                          width="14" height="20"/></a>
                        <input size="5" type="text"
                               class="cart_quantity_input_{$product.id_product}"
                               value='1' name="quantity"/>
                        <a rel="nofollow" class="cart_quantity_up_{$product.id_product}"
                           id="quantity_up_{$product.id_product}" href="javascript:void(0);"
                           title="{l s='Увеличить'}"><img src="{$img_dir}plus.png"
                                                          alt="{l s='Add'}"
                                                          width="14" height="20"/></a>
                    </div>
                    {if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                    <span itemprop="offerDetails">
                        <meta itemprop="currency" content="RUB"/>
                        <p class="price_container"><span
                                class="price"><span
                                itemprop="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></span>

                        </p>{else}
                        <div style="height:21px;"></div>{/if}
                    <meta itemprop="condition" content="new"/>
                    <meta itemprop="availability" content="in_stock"/>


                    <a class="exclusive ajax_add_to_cart_button"
                       id="ajax_id_product_{$product.id_product}"
                       rel="ajax_id_product_{$product.id_product}"
                       href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add"
                       title="{l s='Add to cart' mod='homefeatured'}"></a>
                </span>
                    {else}
                    <div style="height:32px;"></div>
                {/if}
                {else}
                <div style="height:32px;"></div>
            {/if}
            <div class="add_ok">{l s='Product Added to Cart'}
                <div class="go_to_cart">{l s='Check Your Cart'}</div>
            </div>
        </div>
    </div>
</li>
{/strip}
