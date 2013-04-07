{if isset($payment_methods)}
    <table id="paymentMethodsTable">
        
            {foreach from=$payment_methods item=payment_method name=myLoop}
                <tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
                    <td class="payment_action radio">
                        <input type="radio" name="id_payment_method" value="{$payment_method.link}" id="payment_{$payment_method.link}" {if ($payment_methods|@count == 1)}checked="checked"{/if} />
                    </td>
                    <td class="payment_description">{$payment_method.desc}</td>
               </tr>
            {/foreach}
       </tbody>
     </table>
{/if}
