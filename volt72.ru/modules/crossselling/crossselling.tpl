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

{if isset($orderProducts) && count($orderProducts)}
<div id="crossselling" class="block products_block" style="padding-top: 15px; margin-bottom: -25px; ">
	<h2>{l s='Customers who bought this product also bought...' mod='crossselling'}</h2>
<div class="content_block">
	
			<ul>
				{foreach from=$orderProducts item='orderProduct' name=orderProduct}
				<li style="width: 125px; height: 200px; margin-right: 9px;">
                 <a href="{$orderProduct.link}" style="position: absolute; opacity: 0;">
					    <img src="/img/white.png" style="margin-left: -15px; margin-top: -15px; width: 154px; height: 231px;">
					</a>  
				
                           <h5 style="margin-top: -10px!important;"><a href="{$orderProduct.link}" title="{$orderProduct.name|htmlspecialchars}" style="font-size: 12px;">
					{$orderProduct.name|truncate:55:'...'|escape:'htmlall':'UTF-8'}
					</a>	</h5>	
<p class="display" id="product_reference" style="text-align: center; margin-top: 0px; margin-bottom: 5px;"><label for="product_reference">Артикул: </label><span class="editable">{$orderProduct.reference}</span></p>
						
                           <a href="{$orderProduct.link}" title="{$orderProduct.name|htmlspecialchars}" class="product_image" style="width: 130px; height: 120px; margin-top: 15px;">
						<img src="{$orderProduct.image}" height="120" width="130" alt="{$orderProduct.name|htmlspecialchars}" />
					</a>
				     
				



					
				</li>
				{/foreach}
			</ul>
		</div>
		
</div>
{/if}
