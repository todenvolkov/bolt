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


{if (trim($smarty.capture.password_checkbox) != '') && (!isset($opc_config.payment_radio_buttons) || !$opc_config.payment_radio_buttons && (!isset($opc_config.offer_password_top) || !$opc_config.offer_password_top))}
  <form class="std" action="#" id="offer_password">
  <fieldset>
  {if !isset($opc_config.offer_password_top) || !$opc_config.offer_password_top}{$smarty.capture.password_checkbox}{/if}
  {if !isset($opc_config.offer_password_top) || !$opc_config.offer_password_top}{$smarty.capture.password_field}{/if}
  </fieldset>
  </form>
{/if}
<div id="lpayment">
{if isset($opc_config.payment_radio_buttons) && $opc_config.payment_radio_buttons}
   <form action="#" id="payments_section">
        
            <div class="h3">{l s='Choose your payment method' mod='onepagecheckout'}<sup>*</sup></div>
{else}
    <div class="h3">{l s='Choose your payment method' mod='onepagecheckout'}<sup>*</sup></div>
{/if}

<div id="opc_payment_methods" class="opc-main-block">
    <div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>

    <div id="opc_payment_errors" class="error" style="display: none;"></div>
    <div id="HOOK_TOP_PAYMENT">{$HOOK_TOP_PAYMENT}</div>

    {if isset($HOOK_PAYMENT.parsed_content) && $HOOK_PAYMENT.parsed_content && isset($opc_config.payment_radio_buttons) && $opc_config.payment_radio_buttons}
        <div id="opc_payment_methods-parsed-content">
            <div id="HOOK_PAYMENT_PARSED">{$HOOK_PAYMENT.parsed_content}</div>
        </div>
    {else}
        <div id="HOOK_PAYMENT_PARSED" style="display:none;"></div>
    {/if}
    
    {* don't display HOOK_PAYMENT here due to <form> tags colision, display it at the end. *}
    {if !isset($opc_config.payment_radio_buttons) || !$opc_config.payment_radio_buttons}
        {if isset($HOOK_PAYMENT.orig_hook) && $HOOK_PAYMENT.orig_hook}
            <div id="opc_payment_methods-content">
                <div id="HOOK_PAYMENT">{$HOOK_PAYMENT.orig_hook}</div>
            </div>
        {else}
            <p class="warning">{l s='No payment modules have been installed.' mod='onepagecheckout'}</p>
        {/if}
    {/if}

</div>
{if isset($opc_config.payment_radio_buttons) && $opc_config.payment_radio_buttons}
        
    </form>

</div>

  {if (trim($smarty.capture.password_checkbox) != '') && ((!isset($opc_config.offer_password_top) || !$opc_config.offer_password_top))}
    <form class="std" action="#" id="offer_password">
    <fieldset>
    {if !isset($opc_config.offer_password_top) || !$opc_config.offer_password_top}{$smarty.capture.password_checkbox}{/if}
    {if !isset($opc_config.offer_password_top) || !$opc_config.offer_password_top}{$smarty.capture.password_field}{/if}
    </fieldset>
    </form>
  {/if}




{/if}

{if isset($opc_config.payment_radio_buttons) && $opc_config.payment_radio_buttons}
    {if isset($HOOK_PAYMENT.orig_hook) && $HOOK_PAYMENT.orig_hook}
        <div id="opc_payment_methods-content" style="display: none;">
            <div id="HOOK_PAYMENT" style="display:none;">{$HOOK_PAYMENT.orig_hook}</div>
        </div>
    {else}
        <p class="warning">{l s='No payment modules have been installed.' mod='onepagecheckout'}</p>
    {/if}
{/if}


{if $add_extra_div}</div>{/if}

