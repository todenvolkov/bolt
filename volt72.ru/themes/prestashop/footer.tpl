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

		{if !$content_only}
				</div>

<!-- Right -->
				<div id="right_column" class="column">
				<!-- removed 	HOOK_RIGHT_COLUMN from here -->
				</div>
			</div>

<!-- Footer -->
			<footer>
                <div class="create">
                    <a href="http://www.neo-systems.ru" target="blank">Создание сайта N-Systems</a>
                </div>
                <div class="footer">

                </div>
                {$HOOK_FOOTER}
            </footer>
		</div>
	{/if}
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

{if isset($js_files)}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri}"></script>
	{/foreach}
{/if}
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
{literal}
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter20802151 = new Ya.Metrika({id:20802151,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/20802151" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
{/literal}
	</body>
</html>
