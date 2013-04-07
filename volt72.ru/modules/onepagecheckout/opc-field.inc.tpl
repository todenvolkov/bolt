{* parameters: $fields - comma separated list of keys in $opc_fields *}     
{* concept discontinued *}
<div style="border: 1px solid red;">
    {foreach from=$fields item=field_key}
        {assign var=field value=$field_key|trim}
        {if isset($opc_fields.$field)}
            {assign var="desc" value=$opc_fields.$field[0]}
            {assign var="sample" value=$opc_fields.$field[1]}
            {assign var="req" value=$opc_fields.$field[2]}
            {assign var="display" value=$opc_fields.$field[3]}
            <p class="required text" {if !$display}style="display:none;"{/if}>
                <label for="{$field}">{l s=$desc}</label>
                <input type="text" class="text" id="{$field}" name="{$field}" value="{if isset($guestInformations) && $guestInformations.$field}{$guestInformations.$field}{/if}" />
                {if $req}<sup>*</sup>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values && $sample|trim != ""}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s=$sample mod='onepagecheckout'})</span>{/if}
            </p>
        {/if}

    {/foreach}
</div>

