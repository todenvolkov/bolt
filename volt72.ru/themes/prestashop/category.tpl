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
*  @author PrestaShop SA <contact@prestashop.com> *  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14008 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<h1>{$category->name|escape:'htmlall':'UTF-8'}</h1>
{include file="$tpl_dir./errors.tpl"}
<div class="container_switch">
  <div style="float: right;"> <a href="#" class="switch_thumb"></a> {include file="$tpl_dir./product-sort.tpl"} </div>
  {include file="$tpl_dir./breadcrumb.tpl"} </div>
{if isset($subcategories)} 
<!-- Subcategories -->
<div id="subcategories">
  <h3 style="margin: 0 0 0.3em; display:none;">{l s='Subcategories'}</h3>
</div>
{/if}


{if isset($category)}
	{if $category->id AND $category->active}

	

		

		{if $category->description}
        <div class="cat_desc">{$category->description}</div>
{/if}
		{if isset($subcategories)} 
<!-- Subcategories -->
<div id="subcategories"> {*
  <h3>{l s='Subcategories'}</h3>
  *}
  <ul class="inline_list">
    {foreach from=$subcategories item=subcategory}
    <li> <a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" style="position: absolute; opacity: 0;"> <img src="/img/white.png" style="margin-left: -15px; margin-top: -15px; width: 214px; height: 291px;"> </a>
      <h5><a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}">{$subcategory.name|escape:'htmlall':'UTF-8'}</a></h5>
      <a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}" class="product_image"> {if $subcategory.id_image} <img src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'home')}"/> {else} <img src="{$img_cat_dir}default-medium.jpg" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" /> {/if} </a> </li>
    {/foreach}
  </ul>
  <br class="clear"/>
</div>
{/if}
<link rel="stylesheet" type="text/css" href="css/jquery.switch.css" />
<div id="categ">
  <div class="container_switch"> {*
    <div style="float: right;"> <a href="#" class="switch_thumb"></a> {include file="$tpl_dir./product-sort.tpl"} </div>
    {include file="$tpl_dir./breadcrumb.tpl"} 
    *}
    
    {if $products}
    
  {include file="$tpl_dir./product-list.tpl" products=$products}
  </div>
  {include file="$tpl_dir./pagination.tpl"}
  {include file="$tpl_dir./product-compare.tpl"}
  
  {elseif !isset($subcategories)}<br class="clear">
  <p class="warning">{l s='There are no products in this category.'}</p>
  {/if}
  {elseif $category->id}
  <p class="warning">{l s='This category is currently unavailable.'}</p>
  {/if}
  {/if} </div>
