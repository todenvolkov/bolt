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
{literal}
<script type="text/javascript">
    $(document).ready(function () {


        var cookie = $.cookie('type_display');

        if (cookie == 'thumb_view') {
            $("a.switch_thumb").addClass("swap");

            $("a.switch_thumb").toggle(function () {
                        $(this).removeClass("swap");
                        $("ul.thumb_view").fadeOut("fast", function () {
                            $(this).fadeIn("fast").removeClass("thumb_view");
                            $(this).fadeIn("fast").addClass("display");
                            $.cookie("type_display", "display", {expires:7, path:'/', domain:'volt72.ru'});
                        });

                    },
                    function () {
                        $(this).addClass("swap");
                        $("ul.display").fadeOut("fast", function () {
                            $(this).fadeIn("fast").addClass("thumb_view");
                            $(this).fadeIn("fast").removeClass("display");
                            $.cookie("type_display", "thumb_view", {expires:7, path:'/', domain:'volt72.ru'});
                        });

                    });

        }
        else if (cookie == 'display') {
            $("a.switch_thumb").removeClass("swap");

            $("a.switch_thumb").toggle(function () {
                        $(this).addClass("swap");
                        $("ul.display").fadeOut("fast", function () {
                            $(this).fadeIn("fast").addClass("thumb_view");
                            $(this).fadeIn("fast").removeClass("display");
                            $.cookie("type_display", "thumb_view", {expires:7, path:'/', domain:'volt72.ru'});
                        });
                    },
                    function () {
                        $(this).removeClass("swap");
                        $("ul.thumb_view").fadeOut("fast", function () {
                            $(this).fadeIn("fast").removeClass("thumb_view");
                            $(this).fadeIn("fast").addClass("display");
                            $.cookie("type_display", "display", {expires:7, path:'/', domain:'volt72.ru'});
                        });
                    });

        }
        else {
            $("a.switch_thumb").toggle(function () {
                        $(this).addClass("swap");
                        $("ul.display").fadeOut("fast", function () {
                            $(this).fadeIn("fast").addClass("thumb_view");
                            $(this).fadeIn("fast").removeClass("display");
                            $.cookie("type_display", "thumb_view", {expires:7, path:'/', domain:'volt72.ru'});
                        });
                    },
                    function () {
                        $(this).removeClass("swap");
                        $("ul.thumb_view").fadeOut("fast", function () {
                            $(this).fadeIn("fast").removeClass("thumb_view");
                            $(this).fadeIn("fast").addClass("display");
                            $.cookie("type_display", "display", {expires:7, path:'/', domain:'volt72.ru'});
                        });
                    });

        }
    });
</script>
{/literal}

<div id="product_list">
    <ul style="display: block;"
        class="{if isset($smarty.cookies.type_display)}{$smarty.cookies.type_display}{else}display{/if}">
        <li>
            <div class="content_block">

                <!-- MODULE Home Featured Products -->


            {if isset($products) AND $products}
                {literal}
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('.thickbox').fancybox({
                                'hideOnContentClick':true,
                                'transitionIn':'elastic',
                                'transitionOut':'elastic'
                            });
                        });
                    </script>
                {/literal}
                {assign var='liHeight' value=342}
                {assign var='nbItemsPerLine' value=3}
                {assign var='nbLi' value=$products|@count}
                {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
                {math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
                <ul id="product_list">
                    {foreach from=$products item=product name=homeFeaturedProducts}
                        {include file="$tpl_dir./product-card.tpl"}
                    {/foreach}
                </ul>

                {else}
                <p>{l s='No featured products'}</p>


            {/if}


            </div>
        </li>
    </ul>

</div>

