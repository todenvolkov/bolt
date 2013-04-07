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
{strip}
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}">
	<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'} купить Тюмень в магазине Вольт72</title>
		
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'} купить Тюмень в магазине Вольт72" />
{else}
        <meta name="description" content="Вольт72 - Интернет магазин электротоваров от кабеля до светильников,выключателей, розеток,лампочек купить с быстрой доставкой по Тюмени">
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{else}
    <meta name="keywords" content="Светильники, фонари, розетки, кабель, выключатели, электрик, электротовары, купить, тюмень, интернет магазин" />
{/if}
        <meta http-equiv="X-UA-Compatible"  content="IT=edge,chrome=IE8">
		<meta charset='utf-8'>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,follow" />
        <meta name="viewport" content="width=1024" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$img_ps_dir}favicon.ico?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$img_ps_dir}favicon.ico?{$img_update_time}" />
        <meta name='yandex-verification' content='5745ec60f83517ba' />
        <meta name="google-site-verification" content="2QCXdw-8KbrAABMtvOGYAzGfZ8aOZYW9vAitdTlAhqA" />
		<!--[if lt IE 9]>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script>
        <![endif]-->
		<script type="text/javascript">
			var baseDir = '{$content_dir}';
			var static_token = '{$static_token}';
			var token = '{$token}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
{if isset($js_files)}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri}"></script>
	{/foreach}
{/if}
		{$HOOK_HEADER}
{if $page_name == 'index' || $page_name == 'category' || $page_name == 'search'}
	<script type="text/javascript" src="/js/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-1.3.4.css" media="screen" />
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
{/if}
<script type="text/javascript" src="/js/jquery/jquery.cookie.js"></script>
	</head>
	
	<body {if $page_name}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if}>
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}
		<div id="page">

			<!-- Header -->
			<header>
				<a id="header_logo" href="{$link->getPageLink('index.php')}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
					<img class="logo" src="{$img_ps_dir}logo.png?{$img_update_time}" alt="{$shop_name|escape:'htmlall':'UTF-8'}"/>
				</a>
				<div id="header_right">

					{$HOOK_TOP}

				</div>

			</header>

			<div id="columns">
				<!-- Left -->
				<div id="left_column" class="column"> 
				
				
					{$HOOK_LEFT_COLUMN}
				</div>

				<!-- Center -->
        {$HOOK_RIGHT_COLUMN}
				<div id="center_column" {if $page_name == 'category' || $page_name == 'search'}style="margin: 33px 0px 30px 0px;"{/if} {if $page_name == 'product' || $page_name == 'password' || $page_name == 'authentication'}style="margin: 4px 0px 30px 0px;"{/if} {if $page_name == 'order' || $page_name == 'module-bankwire-payment' || $page_name == 'order-confirmation' || $page_name == 'history' || $page_name == 'identity' || $page_name == 'addresses' || $page_name == 'address' || $page_name == 'order-slip' || $page_name == 'cms' || $page_name == 'my-account' || $page_name == 'contact-form'}style="margin: 7px 0px 30px 0px;"{/if}>


    {/if}
{/strip}