<div id="opc_new_account" class="opc-main-block">
    <div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
    
        
<div id="opc_account_form">
<form action="#" method="post" id="new_account_form" class="std">
    
        


        {* TODO: remove; reuse strings "Create your account today ..."
        <div id="opc_account_choice">
        <div class="opc_float">
        <h4>{l s='Instant Checkout' mod='onepagecheckout'}</h4>
        <p>
        <input type="button" class="exclusive_large" id="opc_guestCheckout" value="{l s='Checkout as guest' mod='onepagecheckout'}" />
        </p>
        </div>
        
        <div class="opc_float">
        <h4>{l s='Create your account today and enjoy:' mod='onepagecheckout'}</h4>
        <ul class="bullet">
        <li>{l s='Personalized and secure access' mod='onepagecheckout'}</li>
        <li>{l s='Fast and easy check out' mod='onepagecheckout'}</li>
        </ul>
        <p>
        <input type="button" class="button_large" id="opc_createAccount" value="{l s='Create an account' mod='onepagecheckout'}" />
        </p>
        </div>
        <div class="clear"></div>
        </div>
        *}

        
            <script type="text/javascript">
            // <![CDATA[
            idSelectedCountry = {if isset($guestInformations) && $guestInformations.id_state}{$guestInformations.id_state|intval}{else}{if ($def_state>0)}{$def_state}{else}false{/if}{/if};
            idSelectedCountry_invoice = {if isset($guestInformations) && isset($guestInformations.id_state_invoice)}{$guestInformations.id_state_invoice|intval}{else}{if ($def_state_invoice>0)}{$def_state_invoice}{else}false{/if}{/if};
                {if isset($countries)}
                    {foreach from=$countries item='country'}
                        {if isset($country.states) && $country.contains_states}
                                    countries[{$country.id_country|intval}] = new Array();
                            {foreach from=$country.states item='state' name='states'}
                                            countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'name' : '{$state.name|escape:'htmlall':'UTF-8'}'{rdelim});
                            {/foreach}
                        {/if}
                        {if $country.need_identification_number}
                                    countriesNeedIDNumber.push({$country.id_country|intval});
                        {/if}	
                        {if isset($country.need_zip_code)}
                                    countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
                        {/if}
                    {/foreach}
                {/if}
            //]]>
                {*if $vat_management}
                {literal}
                function vat_number()
                {
                if ($('#company').val() != '')
                $('#vat_number_block').show();
                else
                $('#vat_number_block').hide();
                }
                function vat_number_invoice()
                {
                if ($('#company_invoice').val() != '')
                $('#vat_number_block_invoice').show();
                else
                $('#vat_number_block_invoice').hide();
                }
					
                $(document).ready(function() {
                $('#company').blur(function(){
                vat_number();
                });
                $('#company_invoice').blur(function(){
                vat_number_invoice();
                });
                vat_number();
                vat_number_invoice();
                });
                {/literal}
                {/if*}
                {literal}
            function toggle_password_box() {
              if ($('#is_new_customer').val() == 0) {
                $('p.password').slideDown('slow'); 
                $('#is_new_customer').val(1);
              } else {
                $('p.password').slideUp('slow'); 
                $('#is_new_customer').val(0);
              }
            }//toggle_password_box()
                {/literal}
            </script>
            <!-- Error return block -->
            <div id="opc_account_errors" class="error" style="display:none;"></div>
            <!-- END Error return block -->

	     {capture name=password_checkbox}
              {if !isset($guestInformations) || !$guestInformations.id_customer}
                
              {/if}
	     {/capture}
	    


            <!-- Account -->
            <input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
            <input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
            <input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
            <input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
 <div class="h3">{l s='Forma' mod='onepagecheckout'}<sup>*</sup></div>
         <p class="radio required" style="padding: 0px; margin-left: -6px; margin-bottom: 16px;">
                
                <input type="radio" name="id_gender" id="id_gender1" value="1" {if isset($guestInformations) && $guestInformations.id_gender == 1}checked="checked"{/if} />
                <label for="id_gender1" class="top">{l s='Mr.' mod='onepagecheckout'}</label>
                <input type="radio" name="id_gender" id="id_gender2" value="2" {if isset($guestInformations) && $guestInformations.id_gender == 2}checked="checked"{/if} />
                <label for="id_gender2" class="top">{l s='Ms.' mod='onepagecheckout'}</label>
            </p>
         <div id="leftblock">
            <div class="h3">{l s='Your Data' mod='onepagecheckout'}</div>
            <p class="required text">
                <label for="firstname">{l s='First name' mod='onepagecheckout'}</label>
                
                <input type="text" class="text" id="firstname" name="firstname" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" /> 
           <sup>*</sup> </p>
           <p class="text is_customer_param">
                <label for="phone">{l s='Mobile phone' mod='onepagecheckout'}</label>
                <input type="text" class="text" name="phone" id="phone" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{/if}" /> 
<sup>*</sup> </p>
          
            <p class="required text">
                <label for="email">{l s='E-mail' mod='onepagecheckout'}</label>
                <input type="text" class="text" id="email" name="email" value="{if isset($guestInformations) && $guestInformations.email}{$guestInformations.email}{/if}" /> 
            <sup>*</sup></p>
		<div style="text-align: center; display: none;" id="existing_email_msg">{l s='This email is already registered, you can either' mod='onepagecheckout'} <a href="#" id="existing_email_login">{l s='log-in' mod='onepagecheckout'}</a> {l s='or just fill in details below.' mod='onepagecheckout'}</div>
            
	{capture name="password_field"}
            <p class="required password is_customer_param" style="display: none;">
                <label for="passwd">{l s='Password' mod='onepagecheckout'}<sup>*</sup></label>
                <input type="password" class="text" name="passwd" id="passwd" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}<span class="sample_text ex_blur">&nbsp;&nbsp;{l s='(5 characters min.)' mod='onepagecheckout'}</span>
            </p>
	{/capture}

	{if isset($opc_config.offer_password_top) && $opc_config.offer_password_top}{$smarty.capture.password_field}{/if}
<p class="required postcode text">
                <label for="postcode">{l s='Zip / Postal code' mod='onepagecheckout'}</label>
                <input type="text" class="text" name="postcode" id="postcode" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />             <sup>*</sup></p>
            <p class="required text">
                <label for="city">{l s='City' mod='onepagecheckout'}</label>
                <input type="text" class="text" name="city" id="city" value="{if isset($guestInformations) && $guestInformations.city}{$guestInformations.city}{/if}" />             <sup>*</sup></p>


            

                        <p class="checkbox" {if !isset($opc_config.newsletter) || !$opc_config.newsletter}style="display: none;"{/if}>
                <input type="checkbox" name="newsletter" id="newsletter" value="1" {if (isset($guestInformations) && $guestInformations.newsletter) || (!isset($guestInformations) && isset($opc_config.newsletter_checked) && $opc_config.newsletter_checked)}checked="checked"{/if} />
                <label for="newsletter">{l s='Sign up for our newsletter' mod='onepagecheckout'}</label>
            </p>
            <p class="checkbox" {if !isset($opc_config.special_offers) || !$opc_config.special_offers}style="display: none;"{/if}>
                <input type="checkbox"name="optin" id="optin" value="1" {if (isset($guestInformations) && $guestInformations.optin) || (!isset($guestInformations) && isset($opc_config.special_offers_checked) && $opc_config.special_offers_checked)}checked="checked"{/if} />
                <label for="optin">{l s='Receive special offers from our partners' mod='onepagecheckout'}</label>
            </p>






            <p class="text" {if !isset($opc_config.company_delivery) || !$opc_config.company_delivery}style="display: none;"{/if}>
                <label for="company">{l s='Company' mod='onepagecheckout'}<sup>&nbsp;&nbsp;</sup></label>
                <input type="text" class="text" id="company" name="company" value="{if isset($guestInformations) && $guestInformations.company}{$guestInformations.company}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Google, Inc.' mod='onepagecheckout'})</span>{/if}
            </p>
            <div id="vat_number_block" style="display:none;">
                <p class="text">
                    <label for="vat_number">{l s='VAT number' mod='onepagecheckout'}</label>
                    <input type="text" class="text" name="vat_number" id="vat_number" value="{if isset($guestInformations) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='FR101202303' mod='onepagecheckout'})</span>{/if}
                </p>
            </div>
            <p class="required text dni">
                <label for="dni">{l s='Identification number' mod='onepagecheckout'}<sup>*</sup></label>
                <input type="text" class="text" name="dni" id="dni" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;{l s='DNI / NIF / NIE' mod='onepagecheckout'}</span>{/if}
            </p>
           
            
                
               
             <p class="required text">
                <label for="address1">{l s='Address' mod='onepagecheckout'}</label>
                <input type="text" class="text" name="address1" id="address1" value="{if isset($guestInformations) && $guestInformations.address1}{$guestInformations.address1}{/if}" /> 
            <sup>*</sup></p>
            <p class="text is_customer_param" id="p_address2" {if !isset($opc_config.address2_delivery) || !$opc_config.address2_delivery}style="display: none;"{/if}>
                <label for="address2">{l s='Address (Line 2)' mod='onepagecheckout'}<sup>&nbsp;&nbsp;</sup></label>
                <input type="text" class="text" name="address2" id="address2" value="{if isset($guestInformations) && $guestInformations.address2}{$guestInformations.address2}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Room no.304' mod='onepagecheckout'})</span>{/if}
            </p>
            
            <p style="display: none;" class="required select" {if !isset($opc_config.country_delivery) || !$opc_config.country_delivery}style="display: none;"{/if}>
                <label for="id_country">{l s='Country' mod='onepagecheckout'}</label>
                <select name="id_country" id="id_country">
                    <option value="">-</option>
                    {foreach from=$countries item=v}
                        <option value="{$v.id_country}" {if (isset($guestInformations) AND $guestInformations.id_country == $v.id_country) OR ($def_country == $v.id_country ) OR (!isset($guestInformations) && ($def_country==0) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select> <sup>*</sup> </p>
            <p class="required id_state select">
                <label for="id_state">{l s='State' mod='onepagecheckout'}<sup>*</sup></label>
                <select name="id_state" id="id_state">
                    <option value="">-</option>
                </select>{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}
            </p>

            
            <p class="text" {if !isset($opc_config.phone_delivery) || !$opc_config.phone_delivery}style="display: none;"{/if}>
                <label for="phone">{l s='Home phone' mod='onepagecheckout'}</label>
                <input type="text" class="text" name="phone" id="phone" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='555-100200' mod='onepagecheckout'})</span>{/if}
           <sup>*</sup> </p>
            
            <input type="hidden" name="alias" id="alias" value="{if isset($guestInformations) && $guestInformations.alias}{$guestInformations.alias}{else}{l s='My address' mod='onepagecheckout'}{/if}" />

                   
            {if !isset($opc_config.compact_form) || !$opc_config.compact_form}
             <p style="clear: both;">
                <sup>*</sup>{l s='Required field' mod='onepagecheckout'}
            </p>
            {/if}
             <input type="text" id="lastname" name="lastname" value="noname{if isset($guestInformations) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" style="border: none; width: 0px; height: 0px;"/> 
                       
    </div>

          <div id="cartarea">
                <div class="h3">{l s='Additional information' mod='onepagecheckout'}</div>
                <textarea name="other" id="other" cols="26" rows="3">{if isset($guestInformations) && $guestInformations.other}{$guestInformations.other}{/if}</textarea>
            </div>
            <div id="opc_invoice_address" class="is_customer_param" style="display: {if (isset($guestInformations) && $guestInformations.use_another_invoice_address) OR (!isset($guestInformations) && $def_different_billing == 1)}block{else}none{/if}">
                
                <h3>
                    <div id="inv_addresses_div" style="float: right;{if !isset($addresses) || $addresses|@count == 0}display:none;{elseif $addresses|@count == 1}display:none;{else}display:block;{/if}"> 
                        <span style="font-size: 0.7em;">{l s='Choose another address' mod='onepagecheckout'}:</span>
                        <select id="inv_addresses" style="width: 100px; margin-left: 0px;" onchange="updateAddressSelection_1();">
                            {foreach from=$addresses item=address}
                                <option value="{$address.id_address}" {if $address.id_address == $cart->id_address_invoice}selected="selected"{/if}>{$address.alias}</option>
                            {/foreach}
                        </select>
                    </div>{l s='Invoice address' mod='onepagecheckout'}
                </h3>
            <!-- Error return block -->
            <div id="opc_account_errors_invoice" class="error" style="display:none;"></div>
            <!-- END Error return block -->
                <p class="text is_customer_param" {if !isset($opc_config.company_invoice) || !$opc_config.company_invoice}style="display: none;"{/if}>
                    <label for="company_invoice">{l s='Company' mod='onepagecheckout'}<sup>&nbsp;&nbsp;</sup></label>
                    <input type="text" class="text" id="company_invoice" name="company_invoice" value="{if isset($guestInformations) && isset($guestInformations.company_invoice)}{$guestInformations.company_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Google, Inc.' mod='onepagecheckout'})</span>{/if}
                </p>
                <div id="vat_number_block_invoice" class="is_customer_param" style="display:{if isset($guestInformations) && isset($guestInformations.allow_eu_vat_invoice) && $guestInformations.allow_eu_vat_invoice == 1}block{else}none{/if};">
                    <p class="text">
                        <label for="vat_number_invoice">{l s='VAT number' mod='onepagecheckout'}</label>
                        <input type="text" class="text" id="vat_number_invoice" name="vat_number_invoice" value="{if isset($guestInformations) && isset($guestInformations.vat_number_invoice)}{$guestInformations.vat_number_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='FR101202303' mod='onepagecheckout'})</span>{/if}
                    </p>
                </div>
                <p class="required text dni_invoice">
                    <label for="dni">{l s='Identification number' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" name="dni_invoice" id="dni_invoice" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice)}{$guestInformations.dni_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;{l s='DNI / NIF / NIE' mod='onepagecheckout'}</span>{/if}
                </p>
                <p class="required text">
                    <label for="firstname_invoice">{l s='First name' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" id="firstname_invoice" name="firstname_invoice" value="{if isset($guestInformations) && isset($guestInformations.firstname_invoice)}{$guestInformations.firstname_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Jack' mod='onepagecheckout'})</span>{/if}
                </p>
                <p class="required text">
                    <label for="lastname_invoice">{l s='Last name' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" id="lastname_invoice" name="lastname_invoice" value="{if isset($guestInformations) && isset($guestInformations.lastname_invoice)}{$guestInformations.lastname_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Thompson' mod='onepagecheckout'})</span>{/if}
                </p>
                <p class="required text">
                    <label for="address1_invoice">{l s='Address' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" name="address1_invoice" id="address1_invoice" value="{if isset($guestInformations) && isset($guestInformations.address1_invoice)}{$guestInformations.address1_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='15 High Street' mod='onepagecheckout'})</span>{/if}
                </p>
                <p class="text is_customer_param" id="p_address2_invoice" {if !isset($opc_config.address2_invoice) || !$opc_config.address2_invoice}style="display: none;"{/if}>
                    <label for="address2_invoice">{l s='Address (Line 2)' mod='onepagecheckout'}<sup>&nbsp;&nbsp;</sup></label>
                    <input type="text" class="text" name="address2_invoice" id="address2_invoice" value="{if isset($guestInformations) && isset($guestInformations.address2_invoice)}{$guestInformations.address2_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Room no.304' mod='onepagecheckout'})</span>{/if}
                </p>
                <p class="required postcode text">
                    <label for="postcode_invoice">{l s='Zip / Postal Code' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" name="postcode_invoice" id="postcode_invoice" value="{if isset($guestInformations) && isset($guestInformations.postcode_invoice)}{$guestInformations.postcode_invoice}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='90104' mod='onepagecheckout'})</span>{/if}
                </p>
                <p class="required text">
                    <label for="city_invoice">{l s='City' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" name="city_invoice" id="city_invoice" value="{if isset($guestInformations) && isset($guestInformations.city_invoice)}{$guestInformations.city_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='Paris' mod='onepagecheckout'})</span>{/if}
                </p>
                <p class="required select" {if !isset($opc_config.country_invoice) || !$opc_config.country_invoice}style="display: none;"{/if}>
                    <label for="id_country_invoice">{l s='Country' mod='onepagecheckout'}<sup>*</sup></label>
                    <select name="id_country_invoice" id="id_country_invoice">
                        <option value="">-</option>
                        {foreach from=$countries item=v}
                            <option value="{$v.id_country}" {if (isset($guestInformations) AND isset($guestInformations.id_country_invoice) AND $guestInformations.id_country_invoice == $v.id_country) OR ($def_country_invoice == $v.id_country ) OR (!isset($guestInformations) && ($def_country_invoice==0) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}
                </p>
                <p class="required id_state_invoice select" style="display:none;">
                    <label for="id_state_invoice">{l s='State' mod='onepagecheckout'}<sup>*</sup></label>
                    <select name="id_state_invoice" id="id_state_invoice">
                        <option value="">-</option>
                    </select>{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}
                </p>
                <p class="text is_customer_param">
                    <label for="phone_mobile_invoice">{l s='Mobile phone' mod='onepagecheckout'}<sup>*</sup></label>
                    <input type="text" class="text" name="phone_mobile_invoice" id="phone_mobile_invoice" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile_invoice)}{$guestInformations.phone_mobile_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='555-100200' mod='onepagecheckout'})</span>{/if}
                </p>                  
                <p class="text" {if !isset($opc_config.phone_invoice) || !$opc_config.phone_invoice}style="display: none;"{/if}>
                    <label for="phone_invoice">{l s='Mobile phone' mod='onepagecheckout'}<sup>&nbsp;&nbsp;</sup></label>
                    <input type="text" class="text" name="phone_invoice" id="phone_invoice" value="{if isset($guestInformations) && isset($guestInformations.phone_invoice)}{$guestInformations.phone_invoice}{/if}" />{if isset($opc_config.validation_checkboxes) && $opc_config.validation_checkboxes}<span class="validity valid_blank"></span>{/if}{if isset($opc_config.sample_values) && $opc_config.sample_values}<span class="sample_text ex_blur">&nbsp;&nbsp;({l s='ex.' mod='onepagecheckout'} {l s='555-100200' mod='onepagecheckout'})</span>{/if}                    
                </p>                
                <p class="textarea is_customer_param" {if !isset($opc_config.additional_info_invoice) || !$opc_config.additional_info_invoice}style="display: none;"{/if}>
                    <label for="other_invoice">{l s='Additional information' mod='onepagecheckout'}</label>
                    <textarea name="other_invoice" id="other_invoice" cols="26" rows="3">{if isset($guestInformations) && isset($guestInformations.other_invoice)}{$guestInformations.other_invoice}{/if}</textarea>
                </p>
                            {if !isset($opc_config.compact_form) || !$opc_config.compact_form}
             <p style="clear: both;">
                <sup>*</sup>{l s='Required field' mod='onepagecheckout'}
            </p>
            {/if}
                <input type="hidden" name="alias_invoice" id="alias_invoice" value="{if isset($guestInformations) && isset($guestInformations.alias_invoice)}{$guestInformations.alias_invoice}{else}{l s='My Invoice address' mod='onepagecheckout'}{/if}" />
         
            </div>
            <!-- END Account -->
    
</form>
</div> <!-- END div#opc_account_form -->
<div class="clear"></div>
</div>
