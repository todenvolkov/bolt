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
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block user information module HEADER -->
<div id="phones">
    {$shop_phone|escape:'htmlall':'UTF-8'}
</div>
<noindex   style="  clear: both;
    display: block;
    float: left;
    height: 105px;
    width: 100%;">
<div id="header_user">
    <div id="cart_block"></div>

    <ul id="header_nav">

        <li id="shopping_cart"><div class="thecart">
            <div>В корзине:
                <span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
                <span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='product' mod='blockuserinfo'}</span>
                <span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='products' mod='blockuserinfo'}</span>
                <span class="ajax_cart_product_txt_v hidden">{l s='productv' mod='blockuserinfo'}</span>
                <span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='0 product' mod='blockuserinfo'}</span>
                <span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='0 rub' mod='blockuserinfo'}</span>
            </div>


            <div>
                {if $cart_qties >= 0}


                    <span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='on' mod='blockuserinfo'}</span>
                    <span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='on' mod='blockuserinfo'}</span>
                    <span class="ajax_cart_product_txt_v hidden">{l s='on' mod='blockuserinfo'}</span>
				<span class="ajax_cart_total{if $cart_qties == 0} hidden{/if}">
                    {if $priceDisplay == 1}
						{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
						{convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
					{else}
						{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
						{convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
					{/if}
                </span>
                {/if}
            </div>
            </div>
            <div>
                <a href="{$base_dir}order.php" class="order">Оформить заказ</a>
                </div>
        </li>


    </ul>
</div>
<div id="header_user2">
    <div class="block_content">
        <div id="auth_block" style="padding: 10px;">
        {if $logged AND !$smarty.get.c == 1}
            <span>Добро пожаловать, <b>{$username}</b>!</span>

            <p class="logout" style="padding-top: 5px;"><a href="/my-account" title="Личный кабинет">Личный кабинет</a>
                <span style="margin-left: 47px;"><a href="/?mylogout" title="Выйти">Выйти</a></span></p>
        {/if}
        {if !$logged OR $smarty.get.c == 1}
            <a href="/authentication" style="text-decoration:none;">Войти</a><BR/>
            <a href="/authentication?register=true" style="text-decoration:none;">Регистрация</a>
        {/if}
        </div>
    </div>
</div>
</noindex>
