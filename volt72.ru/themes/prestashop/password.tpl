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
*  @version  Release: $Revision: 14008 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: none; margin-bottom: 11px;">ВОССТАНОВЛЕНИЕ ПАРОЛЯ</h1>
{capture name=path}{l s='Forgot your password'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}


<div id="cart">

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation == 1}
<p class="success">{l s='Your password has been successfully reset and has been sent to your e-mail address:'} {$email|escape:'htmlall':'UTF-8'}</p>
{elseif isset($confirmation) && $confirmation == 2}
<p class="success">{l s='A confirmation e-mail has been sent to your address:'} {$email|escape:'htmlall':'UTF-8'}</p>
{else}
<p>{l s='Please enter the e-mail address used to register. We will e-mail you your new password.'}</p>
<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std">
	<br>
		<p class="text">
			<label for="email" style="margin-right: 5px;">{l s='E-mail:'}</label>
			<input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
		</p>
		
			<input type="submit" class="button" value="{l s='Retrieve Password'}" style="margin: 0px; padding: 0px;"/>
		
	
</form>
{/if}
</div>
