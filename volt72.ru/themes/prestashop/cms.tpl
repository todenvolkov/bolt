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
<script type="text/javascript" src="/js/zeroclipboard/ZeroClipboard.js"></script>
<style>
#center_column p {
margin: 0.2em 0;
padding-top: 0.9em;
}
</style>



{if isset($cms) && !isset($category)}
	{if !$cms->active}
		<br />
		<div id="admin-action-cms">
			<p>{l s='This CMS page is not visible to your customers.'}
			<input type="hidden" id="admin-action-cms-id" value="{$cms->id}" />
			<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishCMS('{$base_dir}{$smarty.get.ad}', 0)"/>			
			<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishCMS('{$base_dir}{$smarty.get.ad}', 1)"/>			
			</p>
			<div class="clear" ></div>
			<p id="admin-action-result"></p>
			</p>
		</div>
	{/if}
    <h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">{$cms->meta_title}</h1>
{if isset($cms) && ($content_only == 0)}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}
	<div class="cmspage {if $content_only} content_only{/if}">
		{$cms->content}
	</div>
	
{elseif isset($category)}
	<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">{$category->name|escape:'htmlall':'UTF-8'}</h1>
    {include file="$tpl_dir./breadcrumb.tpl"}
	<div class="cmspage">
		
        {if $category->id eq 2}<p style="text-align: center;" align="center"><span style="color: #3366ff; font-size: 12pt;">{/if}{$category->description|escape:'htmlall':'UTF-8'}{if $category->id eq 2}</span>{/if}</p><br/>
		{* {if isset($sub_category) & !empty($sub_category)}	
			<h4>{l s='List of sub categories in '}{$category->name}{l s=':'}</h4>
			<ul class="bullet">
				{foreach from=$sub_category item=subcategory}
					<li>
						<a href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}">{$subcategory.name|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if} *}
		{if isset($cms_pages) & !empty($cms_pages)}
		
		{foreach from=$cms_pages item=cmspages}
        	{if $cmspages.id_cms eq 3}
            	{$cmspages.content}
            {/if}
        {/foreach}<!--<div id="content_inf">
            <table style="height: 50px; width: 680px; padding: 10px;" border="0" cellspacing="0" cellpadding="1">
                <tbody>
                    <tr>
                    <td>
                    </td>
                    <td style="text-align: justify; padding: 30px;"><span style="font-size: 10pt;">&nbsp;GLOBAL – ведущий мировой производитель биметаллических и алюминиевых радиаторов отопления, уже более 30 лет являющихся синонимом качества и надежности.</span></td>
                    </tr>
                    <tr>
                    <td>&nbsp;</td>
                    <td style="padding-left: 30px;" align="left"><span style="font-size: 10pt;"><a href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'htmlall':'UTF-8'}">Подробнее</a></span></td>
                    </tr>
                </tbody>
            </table>
        </div>-->
		{/if}
	</div>
{else}
	{l s='This page does not exist.'}
{/if}
<br />

<script type="text/javascript">
    ZeroClipboard.setMoviePath('/js/zeroclipboard/ZeroClipboard.swf');
    var clip = new ZeroClipboard.Client(); 
    clip.setText(''); 
    clip.addEventListener( 'complete', function(client, text) { 
		alert("Номер ICQ добавлен в буфер обмена"); 
	} );
	
    clip.setText('259-583-841');
    clip.glue('copy_button_1');
	
	var clip2 = new ZeroClipboard.Client(); 
    clip2.setText(''); 
    clip2.addEventListener( 'complete', function(client, text) { 
		alert("Номер ICQ добавлен в буфер обмена"); 
	} );
	
    clip2.setText('646-545-889');
    clip2.glue('copy_button_2');
</script>