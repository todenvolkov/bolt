{if $menu.items|@count > 0}
    
    <!-- MODULE JBX_MENU -->
    <nav id="menu">
        <ul>
         <li class="first"><a href="{$base_dir}" {if $page_name == 'index'}style="color:#82817f;"{/if}>{l s='main' mod='jbx_menu'}</a></li>
          {foreach from=$menu.items item=item name=menuTree}
              {include file=$menu_tpl_tree}
          {/foreach}
                  </ul>
        <!-- /MODULE JBX_MENU -->
   </nav>
{/if}
