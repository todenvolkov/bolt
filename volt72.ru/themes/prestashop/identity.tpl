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

<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">{l s='Your personal information'}</h1>
{capture name=path}<a href="{$link->getPageLink('my-account.php', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Your personal information'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<a href="{$link->getPageLink('history.php', true)}" title="{l s='История заказов'}"><img src="{$img_dir}icon/order.gif" alt="{l s='История заказов'}" class="icon" /></a><a href="{$link->getPageLink('history.php', true)}" title="{l s='История заказов'}">{l s='История заказов'}</a>&nbsp;&nbsp;
	{if $returnAllowed}
		<a href="{$link->getPageLink('order-follow.php', true)}" title="{l s='Merchandise returns'}"><img src="{$img_dir}icon/return.gif" alt="{l s='Merchandise returns'}" class="icon" /></a><a href="{$link->getPageLink('order-follow.php', true)}" title="{l s='Merchandise returns'}">{l s='My merchandise returns'}</a>&nbsp;&nbsp;
	{/if}
	<a href="{$link->getPageLink('order-slip.php', true)}" title="{l s='Кредитная история'}"><img src="{$img_dir}icon/slip.gif" alt="{l s='Кредитная история'}" class="icon" /></a><a href="{$link->getPageLink('order-slip.php', true)}" title="{l s='Кредитная история'}">{l s='Кредитная история'}</a>&nbsp;&nbsp;
	<a href="{$link->getPageLink('addresses.php', true)}" title="{l s='Мои адреса'}"><img src="{$img_dir}icon/addrbook.gif" alt="{l s='Мои адреса'}" class="icon" /></a><a href="{$link->getPageLink('addresses.php', true)}" title="{l s='Мои адреса'}">{l s='Мои адреса'}</a>&nbsp;&nbsp;
	<a href="{$link->getPageLink('identity.php', true)}" title="{l s='Личные данные'}"><img src="{$img_dir}icon/userinfo.gif" alt="{l s='Личные данные'}" class="icon" /></a><a href="{$link->getPageLink('identity.php', true)}" title="{l s='Личные данные'}">{l s='Личные данные'}</a>&nbsp;&nbsp;
	{if $voucherAllowed}
		<a href="{$link->getPageLink('discount.php', true)}" title="{l s='Мои ваучеры'}"><img src="{$img_dir}icon/voucher.gif" alt="{l s='Мои ваучеры'}" class="icon" /></a><a href="{$link->getPageLink('discount.php', true)}" title="{l s='Мои ваучеры'}">{l s='Мои ваучеры'}</a>&nbsp;&nbsp;
	{/if}
	{$HOOK_CUSTOMER_ACCOUNT}
<BR /><BR />

<div id="cart">

{* <h1>{l s='Your personal information'}</h1> *}

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation}
	<p class="success">
		{l s='Your personal information has been successfully updated.'}
		{if isset($pwd_changed)}<br />{l s='Your password has been sent to your e-mail:'} {$email|escape:'htmlall':'UTF-8'}{/if}
	</p>
{else}
	<h3>{l s='Please do not hesitate to update your personal information if it has changed.'}</h3>
	<p class="required"><sup>*</sup>{l s='Required field'}</p>
	<form action="{$link->getPageLink('identity.php', true)}" method="post" class="std">
		<fieldset>
			<p class="radio">
				<span>{l s='Title'}</span>
				<input type="radio" id="id_gender1" name="id_gender" value="1" {if $smarty.post.id_gender == 1 OR !$smarty.post.id_gender}checked="checked"{/if} />
				<label for="id_gender1">{l s='Mr.'}</label>
				<input type="radio" id="id_gender2" name="id_gender" value="2" {if $smarty.post.id_gender == 2}checked="checked"{/if} />
				<label for="id_gender2">{l s='Ms.'}</label>
			</p>
			<p class="required text">
				<label for="firstname">{l s='First name'}</label>
				<input type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}" /> <sup>*</sup>
			</p>
			<p class="required text">
				<label for="lastname">{l s='Last name'}</label>
				<input type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}" /> <sup>*</sup>
			</p>
			<p class="required text">
				<label for="email">{l s='E-mail'}</label>
				<input type="text" name="email" id="email" value="{$smarty.post.email}" /> <sup>*</sup>
			</p>
			<p class="required text">
				<label for="old_passwd">{l s='Current Password'}</label>
				<input type="password" name="old_passwd" id="old_passwd" /> <sup>*</sup>
			</p>
			<p class="password">
				<label for="passwd">{l s='New Password'}</label>
				<input type="password" name="passwd" id="passwd" />
			</p>
			<p class="password">
				<label for="confirmation">{l s='Confirmation'}</label>
				<input type="password" name="confirmation" id="confirmation" />
			</p>
			<p class="select">
				<label>{l s='Date of Birth'}</label>
				<select name="days" id="days">
					<option value="">-</option>
					{foreach from=$days item=v}
						<option value="{$v|escape:'htmlall':'UTF-8'}" {if ($sl_day == $v)}selected="selected"{/if}>{$v|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
					{/foreach}
				</select>
				{*
					{l s='January'}
					{l s='February'}
					{l s='March'}
					{l s='April'}
					{l s='May'}
					{l s='June'}
					{l s='July'}
					{l s='August'}
					{l s='September'}
					{l s='October'}
					{l s='November'}
					{l s='December'}
				*}
				<select id="months" name="months">
					<option value="">-</option>
					{foreach from=$months key=k item=v}
						<option value="{$k|escape:'htmlall':'UTF-8'}" {if ($sl_month == $k)}selected="selected"{/if}>{l s="$v"}&nbsp;</option>
					{/foreach}
				</select>
				<select id="years" name="years">
					<option value="">-</option>
					{foreach from=$years item=v}
						<option value="{$v|escape:'htmlall':'UTF-8'}" {if ($sl_year == $v)}selected="selected"{/if}>{$v|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
					{/foreach}
				</select>
			</p>
			<p class="text">
            <label for="job_mobile">Рабочий телефон</label>
            <input type="text" class="text" id="job_mobile" name="job_mobile" value="{if isset($smarty.post.job_mobile)}{$smarty.post.job_mobile}{/if}" />
        </p>
		<p class="text">
            <label for="phone">Домашний телефон</label>
            <input type="text" class="text" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
        </p>
		<p class="text">
            <label for="phone_mobile">Мобильный телефон</label>
            <input type="text" class="text" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
        </p>
			{if $newsletter}
			<p class="checkbox">
				<input type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if} />
				<label for="newsletter">{l s='Sign up for our newsletter'}</label>
			</p>
			<p class="checkbox">
				<input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if} />
				<label for="optin">{l s='Receive special offers from our partners'}</label>
			</p>
			{/if}
			<p class="submit">
				<input type="submit" class="button" name="submitIdentity" value="{l s='Save'}" />
			</p>
		</fieldset>
	</form>
	<p id="security_informations">
		{l s='[Insert customer data privacy clause or law here, if applicable]'}
	</p>
{/if}

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account.php', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account.php', true)}">{l s='Back to Your Account'}</a></li>
	<li><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>

</div>