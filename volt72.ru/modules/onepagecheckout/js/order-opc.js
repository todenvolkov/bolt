/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


jQuery.fn.idle = function(time){
    return this.each(function(){
        var i = $(this);
        i.queue(function(){
            setTimeout(function(){
                i.dequeue();
            }, time);
        });
    });
};


function updateCarrierList(json)
{
    var carriers = json.carriers;
	
    /* contains all carrier available for this address */
    if (carriers.length == 0)
    {
        checkedCarrier = 0;
        $('input[name=id_carrier]:checked').attr('checked', false);
        $('#noCarrierWarning').show();
        $('#extra_carrier').hide();
        $('#recyclable_block').hide();
        $('table#carrierTable:visible').hide();
    }
    else
    {
        checkedCarrier = json.checked;
        var html = '';
        for (i=0;i<carriers.length; i++)
        {
            var itemType = '';
			
            if (i == 0)
                itemType = 'first_item ';
            else if (i == carriers.length-1)
                itemType = 'last_item ';
            if (i % 2)
                itemType = itemType + 'alternate_item';
            else
                itemType = itemType + 'item';
			
            var name = carriers[i].name;
            if (carriers[i].img != '')
                name = '<img src="'+carriers[i].img+'" alt="" />';
				
	    // OPCKT - extraHtml added in PS 1.4.2.5; changed a bit
	    //if (!(carriers[i].is_module && !isLogged))
	    //	var extraHtml = 'disabled="disabled"';
	    //else 
	    var extraHtml = '';
            if (checkedCarrier == carriers[i].id_carrier || carriers.length == 1)
                extraHtml = 'checked="checked"';
		
	     html = html + 
		'<tr class="'+itemType+'">'+
			'<td class="carrier_action radio"><input type="radio" name="id_carrier" value="'+carriers[i].id_carrier+'" id="id_carrier'+carriers[i].id_carrier+'"  onclick="updateCarrierSelectionAndGift();" '+extraHtml+' /></td>'+
			'<td class="carrier_name"><label for="id_carrier'+carriers[i].id_carrier+'">'+name+'</label></td>';
			
	     if (taxEnabled && displayPrice == 0)
		html = html + ' ' + txtWithTax;
	     else
		html = html + ' ' + txtWithoutTax;
		html = html + '</td>'+
		'</tr>';

        }
        if (json.HOOK_EXTRACARRIER !== null && json.HOOK_EXTRACARRIER != undefined) html += json.HOOK_EXTRACARRIER;
        $('#noCarrierWarning').hide();
        $('#extra_carrier:hidden').show();
        $('table#carrierTable tbody').html(html);
	if (carriers.length == 1 && opc_hide_carrier == "1") {
	  $('h3#choose_delivery').hide();
          $('table#carrierTable:visible').hide();
	  if (!($('input#recyclable').length > 0 || $('input#gift').length > 0 || $('textarea#message').length > 0 || $('input#cgv').length > 0))
		$('form#carriers_section').hide();	
	}
	else {
	  $('h3#choose_delivery:hidden').show();
          $('table#carrierTable:hidden').show();
	  $('form#carriers_section:hidden').show();	
	}
        $('#recyclable_block:hidden').show();
    }
	
    /* update hooks for carrier module */
    $('#HOOK_BEFORECARRIER').html(json.HOOK_BEFORECARRIER);
}

function updatePaymentMethods(json)
{
    $('#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
                
    var isPaymentParsing = $('input[name=id_payment_method]').length;
    if (isPaymentParsing) {
        var link_id_1 = $('input[name=id_payment_method]:checked').val();
    }
    
    $('#opc_payment_methods-content div#HOOK_PAYMENT').html(json.HOOK_PAYMENT.orig_hook);
    $('#opc_payment_methods-parsed-content div#HOOK_PAYMENT_PARSED').html(json.HOOK_PAYMENT.parsed_content);
    
    // select lastly used (just by its order ID)
    if (isPaymentParsing && $('input[value='+link_id_1+']').length) {
        $('input[value='+link_id_1+']').attr("checked", "checked");
    }  
   
    // if free order or (single payment method + config option turned on), hide whole block
    var paymentStr = json.HOOK_PAYMENT.orig_hook;
    if (paymentStr.indexOf("javascript:confirmFreeOrder") > 0 || 
	(paymentStr.indexOf("opc_pid_0") > 0 && paymentStr.indexOf("opc_pid_1") < 0 && opc_hide_payment == "1"))
      $('#payments_section:visible').hide();
    else
      $('#payments_section:hidden').show();
 
    setPaymentModuleHandler();
}

function setField(fieldname, value) {
    $(fieldname).val(value);
}

function fadeAndSet(fieldname,duration,opacity,value) {
    $(fieldname).fadeTo(duration, opacity, function () {
        $(fieldname).val(value);
	if (opc_inline_validation == "1" && value != "")
          validateFieldAndDisplayInline($(this));
    });
}

function animateDlvAddress(address, dont_pre_save_address) {
    var start = 600;
    var inc = 70;
    var i=0;
    var opacity = 0.2;
    var fields = ["#company", "#vat_number", "#dni", "#firstname", "#lastname", "#address1", "#address2", "#postcode", 
    "#city", "#id_country", "#id_state", "#other", "#phone", "#phone_mobile"];

    for (var j=0; j<fields.length; j++) 
        if ($(fields[j]).is(":visible"))
            fadeAndSet(fields[j], start+inc*i++, opacity, address[fields[j].substring(1)]);

    // slower refresh for country / state to give a bit more time to ajax request
    if (!dont_pre_save_address) {
        $('#id_country').fadeTo(start+inc*i++, opacity, function () {
            $('#id_country').change();
        });
        
        $('#id_state').fadeTo(start+inc*i++, opacity, function () {
            $('#id_state').change();
        });
    } 

    $(fields.join(",")).fadeTo(300, 1);

    if (dont_pre_save_address) {
        // wait callback, so that state combo shows
        $('#id_country').fadeTo(300,1,function() {
            idSelectedCountry = address['id_state'];
            updateState(); // USA states
            updateNeedIDNumber(); // Spanish DNI
	    if ($('#dni').is(":visible"))
	      fadeAndSet('#dni', 0, 1, address['dni']);
	    if ($('#vat_number').is(":visible"))
	      fadeAndSet('#vat_number', 0, 1, address['vat_number']);
            updateZipCode();
        });
    }

    if (address['address2'] != '') {
        $('#p_address2').show();
    }
    $('#alias').val(address['alias']);

    return false;

}//animateDlvAddress()

function animateInvAddress(address, dont_pre_save_address) {
    var start = 1000;
    var inc = 70;
    var i=0;
    var opacity = 0.2;
    var fields = ["#company", "#vat_number", "#dni", "#firstname", "#lastname", "#address1", "#address2", "#postcode", 
    "#city", "#id_country", "#id_state", "#other", "#phone", "#phone_mobile"];

    for (var j=0; j<fields.length; j++) 
        if ($(fields[j]).is(":visible"))
            fadeAndSet(fields[j]+"_invoice", start+inc*i++, opacity, address[fields[j].substring(1)]);

    if (!dont_pre_save_address) {
        $('#id_country_invoice').fadeTo(start+inc*i++, opacity, function () {
            $('#id_country_invoice').change();
        });

        $('#id_state_invoice').fadeTo(start+inc*i++, opacity, function () {
            $('#id_state_invoice').change();
        });
    } 

    var fields_invoice = [];
    $.each(fields, function(i,f) { 
        fields_invoice.push(f+"_invoice");
    });

    $(fields_invoice.join(",")).fadeTo(300, 1);
    if (dont_pre_save_address) {
        // wait callback, so that state combo shows
        $('#id_country_invoice').fadeTo(300,1,function() {
            idSelectedCountry_invoice = address['id_state'];
            updateState('invoice'); // USA states
            updateNeedIDNumber('invoice'); // Spanish DNI
  	    if ($('#dni_invoice').is(":visible"))
              fadeAndSet('#dni_invoice', 0, 1, address['dni']);
	    if ($('#vat_number_invoice').is(":visible"))
	      fadeAndSet('#vat_number_invoice', 0, 1, address['vat_number']);
            updateZipCode('invoice');
        });
    }

    if (address['address2'] != '') {
        $('#p_address2_invoice').show();
    }
    $('#alias_invoice').val(address['alias']);
    return false;


}//animateInvAddress()

var nextTimeAnimateInv = false;
// dont_pre_save_address = called from choose another address and in that case we don't need to pre_saveAddress as it's already set properly
function updateAddressesForms(json, dont_pre_save_address) {
    if (typeof(dont_pre_save_address) == undefined)
        dont_pre_save_address = false;

    if ($('#opc_id_address_delivery').val() != json.summary.delivery.id)
        animateDlvAddress(json.summary.delivery, dont_pre_save_address);


    if (json.summary.delivery.id==json.summary.invoice.id)
    {
        $('#invoice_address').removeAttr('checked');
        $('#opc_invoice_address').slideUp();
	if ($('#dlv_addresses:visible').length > 0) nextTimeAnimateInv = true;	
    }else {
        // update also invoice address form
        if ($('#opc_id_address_invoice').val() != json.summary.invoice.id || nextTimeAnimateInv)
	  if (json.summary.invoice.lastname != 'dummyvalue' && json.summary.invoice.city != 'dummyvalue')
            animateInvAddress(json.summary.invoice, dont_pre_save_address);
	nextTimeAnimateInv = false;	
    }
    $('#opc_id_address_delivery').val(json.summary.delivery.id);
    $('#opc_id_address_invoice').val(json.summary.invoice.id);
    if (dont_pre_save_address) {
        updateEuVatField(json);
    }
}




function setAddressFields(address, type) {
	
    var fields = ["#company", "#vat_number", "#dni", "#firstname", "#lastname", "#address1", "#address2", "#postcode",
    "#city", "#id_country", "#other", "#phone", "#phone_mobile"];

    var suffix = '';
    if (type == 'invoice')
        suffix = '_invoice';
	  
    var field_val = "";
    for (var j=0; j<fields.length; j++)
        if ($(fields[j]).is(":visible")) {
	    field_val = address[fields[j].substring(1)];
            $(fields[j]+suffix).val(field_val);
	    if (opc_inline_validation == "1" && field_val != "")
              validateFieldAndDisplayInline($(fields[j]));
	    field_val = ""; // reset temp var
	}

    if (address['address2'] != '') {
        $('#p_address2'+suffix).show();
    }
    $('#alias'+suffix).val(address['alias']);

    if (type == 'invoice') {
      // just a little more time to allow country / vat / dni fields to show up
      $('#id_country_invoice').fadeTo(300,1,function() {
        updateState('invoice'); // USA states
        updateNeedIDNumber('invoice'); // Spanish DNI
	if ($('#dni_invoice').is(":visible"))
           fadeAndSet('#dni_invoice', 0, 1, address['dni']);
        if ($('#vat_number_invoice').is(":visible"))
           fadeAndSet('#vat_number_invoice', 0, 1, address['vat_number']);
        updateZipCode('invoice');
      });
    } else {
      $('#id_country').fadeTo(300,1,function() {
        updateState(); // USA states
        updateNeedIDNumber(); // Spanish DNI
	if ($('#dni').is(":visible"))
           fadeAndSet('#dni', 0, 1, address['dni']);
        if ($('#vat_number').is(":visible"))
           fadeAndSet('#vat_number', 0, 1, address['vat_number']);
        updateZipCode();
      });
    }

    $('#id_state'+suffix).fadeTo(400,1,function() {
      $('#id_state'+suffix).val(address['id_state']); // because we don't have it in template and updateState would overwrite it otherwise if it was called earlier
    });
	
}

function updateChooseAnotherAddress(addresses, dlv_address_id, inv_address_id) {
    if (addresses.length > 1) 
    {
        $('select#dlv_addresses').empty();	
        $('select#inv_addresses').empty();	
        $(addresses).each(function (key, item){
            $('select#dlv_addresses').append('<option value="'+item.id_address+'"'+ (dlv_address_id == item.id ? ' selected="selected' : '') + '">'+item.alias+'</option>');
            $('select#inv_addresses').append('<option value="'+item.id_address+'"'+ (inv_address_id == item.id ? ' selected="selected' : '') + '">'+item.alias+'</option>');
        });

        $('div#dlv_addresses_div').slideDown('slow');
        $('div#inv_addresses_div').slideDown('slow');
    }
    else {
        $('div#dlv_addresses_div').slideUp('fast');
        $('div#inv_addresses_div').slideUp('fast');
    }
}

function updateEuVatField(json) {
    if (json.allow_eu_vat_delivery == 1)
        $('#vat_number_block').show();
    else
        $('#vat_number_block').hide();

    if (json.allow_eu_vat_invoice == 1)
        $('#vat_number_block_invoice').show();
    else
        $('#vat_number_block_invoice').hide();
}

function updateCustomerInfo(customer_info) {
    // email
    $("#opc_account_form input#email").val(customer_info.email);

    // birthday
    if (customer_info.birthday != "") {
        var birthdayArr = customer_info.birthday.split("-");
        if (birthdayArr[0] !== undefined)
            $("#opc_account_form select#years").val(parseInt(birthdayArr[0]));
        if (birthdayArr[1] !== undefined)
            $("#opc_account_form select#months").val(parseInt(birthdayArr[1]));
        if (birthdayArr[2] !== undefined)
            $("#opc_account_form select#days").val(parseInt(birthdayArr[2]));
    } else {
        $("#opc_account_form select#years").val('');
        $("#opc_account_form select#months").val('');
        $("#opc_account_form select#days").val('');
    }

    // optin - special offers
    if (customer_info.optin == 1)
        $("#opc_account_form input#optin").attr('checked', 'checked');
    else
        $("#opc_account_form input#optin").removeAttr('checked');
	  
    // newsletter
    if (customer_info.newsletter == 1)
        $("#opc_account_form input#newsletter").attr('checked', 'checked');
    else
        $("#opc_account_form input#newsletter").removeAttr('checked');

    // gender
    if (customer_info.id_gender == 1)
        $("input[name=id_gender]").filter("[value=1]").attr("checked","checked");
    else if (customer_info.id_gender == 2)
        $("input[name=id_gender]").filter("[value=2]").attr("checked","checked");
    else
        $("input[name=id_gender]").removeAttr("checked");

    // opc_id_customer
    $('input#opc_id_customer').val(customer_info.id);

    // "password" definition checkbox
    if (customer_info.is_guest == 0)
        $("p#p_registerme, form#offer_password").hide();
    else
        $("p#p_registerme, form#offer_password").show();
	
}

function updateFormsAfterLogin(json) {
    $('form#login_form').hide();
    updateCustomerInfo(json.customer_info);
    setAddressFields(json.summary.delivery, 'delivery');
    setAddressFields(json.summary.invoice, 'invoice');
    updateChooseAnotherAddress(json.customer_addresses, json.summary.delivery.id, json.summary.invoice.id);

    updateEuVatField(json);
}

function updateAddressSelection_1() {
    var idAddress_delivery = $('select#dlv_addresses').val();
    var idAddress_invoice = ($('input[type=checkbox]#invoice_address:checked').length == 0 ? idAddress_delivery : ($('select#inv_addresses').length == 1 ? $('select#inv_addresses').val() : idAddress_delivery));
    updateAddressSelection(idAddress_delivery, idAddress_invoice);

}
function updateAddressSelection(idAddress_delivery, idAddress_invoice)
{

	
    $('#opc_account-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $('#opc_payment_methods-overlay').fadeIn('slow');
	
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updateAddressesSelected&id_address_delivery=' + idAddress_delivery + '&id_address_invoice=' + idAddress_invoice + '&token=' + static_token,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                alert(errors);
            }
            else
            {
                updateCarrierList(jsonData);
                updatePaymentMethods(jsonData);
                updateCartSummary(jsonData.summary);
                updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
                updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
                // OPCKT added
                var dont_pre_save_address = 1;
                updateAddressesForms(jsonData, dont_pre_save_address);
                if ($('#gift-price').length == 1)
                    $('#gift-price').html(jsonData.gift_price);
                $('#opc_account-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                $('#opc_payment_methods-overlay').fadeOut('slow');
                setPaymentModuleHandler();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function getCarrierListAndUpdate()
{
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getCarrierList&token=' + static_token,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                alert(errors);
            }
            else
                updateCarrierList(jsonData);
            $('#opc_delivery_methods-overlay').fadeOut('slow');
        }
    });
}

function updateCarrierSelectionAndGift()
{
    var recyclablePackage = 0;
    var gift = 0;
    var giftMessage = '';
    var idCarrier = 0;

    if ($('input#recyclable:checked').length)
        recyclablePackage = 1;
    if ($('input#gift:checked').length)
    {
        gift = 1;
        giftMessage = encodeURIComponent($('textarea#gift_message').val());
    }
	
    if ($('input[name=id_carrier]:checked').length)
    {
        idCarrier = $('input[name=id_carrier]:checked').val();
        checkedCarrier = idCarrier;
    }
	
    $('#opc_delivery_methods-overlay').show();//fadeIn('fast');
    $('#opc_payment_methods-overlay').fadeIn();
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: false,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updateCarrierAndGetPayments&id_carrier=' + idCarrier + '&recyclable=' + recyclablePackage + '&gift=' + gift + '&gift_message=' + giftMessage + '&token=' + static_token ,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                alert(errors);
            }
            else
            {
                updateCartSummary(jsonData.summary);
                updatePaymentMethods(jsonData);
                updateHookShoppingCart(jsonData.summary.HOOK_SHOPPING_CART);
                updateHookShoppingCartExtra(jsonData.summary.HOOK_SHOPPING_CART_EXTRA);
                $('#opc_payment_methods-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                setPaymentModuleHandler();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to save carrier \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function confirmFreeOrder()
{
    if ($('#opc_new_account-overlay').length != 0)
        $('#opc_new_account-overlay').fadeIn('slow');
    else
        $('#opc_account-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $('#opc_payment_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "html",
        data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
        success: function(html)
        {
            var array_split = html.split(':');
            if (array_split[0] === 'freeorder')
            {
                if (isGuest)
                    document.location.href = guestTrackingUrl+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
                else
                    document.location.href = historyUrl;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to confirm the order \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function submitDiscount(method, id_discount)
{
    var req_str = '';
    if (method == 'add')
        req_str = '&submitAddDiscount=1&submitDiscount=1';
    else // method == 'delete'
        req_str = '&deleteDiscount='+id_discount;
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'+req_str+'&discount_name='+$('input#discount_name').val()+'&token=' + static_token ,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var tmp = '';
                var i = 0;
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                    {
                        i = i+1;
                        tmp += '<li>'+jsonData.errors[error]+'</li>';
                    }
                tmp += '</ol>';
                var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
                $('#opc_voucher_errors').html(errors).slideDown('slow');
               // $.scrollTo('#opc_voucher_errors', 800);
                result = false;
            } else {
                $('#opc_voucher_errors').slideUp('slow');
                if (jsonData.last_discount) {
                    var last_discount = jsonData.last_discount;
                    // cart summary (checkout page)
                    $('table#cart_summary > tbody:last').append(
                        '<tr id="cart_discount_'+last_discount["id"]+'" class="cart_discount last_item">' +
                        '<td class="cart_discount_name" colspan="2">'+last_discount["name"]+'</td>' +
                        '<td class="cart_discount_description" colspan="3">'+last_discount["description"][jsonData.id_lang]+'</td>' +
                        '<td class="cart_discount_delete">' +
                        '<a title="Delete" href="'+baseDir+'modules/onepagecheckout/order-opc.php?deleteDiscount='+last_discount["id"]+'">' +
                        '<img class="icon" width="11" height="13" alt="Delete" src="'+imgDir+'icon/delete.gif">' +
                        '</a>' +
                        '</td>' +
                        '<td class="cart_discount_price">' +
                        '<span class="price-discount">'+(last_discount["value_real"] * -1)+'</span>' +
                        '</td>' +
                        '</tr>'
                        );
                    // blockcart
                    if (window.ajaxCart !== undefined)
                    {
                        if ($('#cart_block_list table#vouchers').length == 0) {
                            // first append table definition
                            $('#cart_block_list > dl').append(
                                '<table id="vouchers"><tbody></tbody></table>'
                                );
                        }
                        $('#cart_block_list table#vouchers > tbody:last').append(
                            '<tr id="bloc_cart_voucher_'+last_discount["id"]+'" class="bloc_cart_voucher">' +
                            '<td class="name" title="'+last_discount["description"][jsonData.id_lang]+'">'+last_discount["name"]+' : '+last_discount["description"][jsonData.id_lang]+'</td>' +
                            '<td class="price">'+(last_discount["value_real"] * -1)+'</td>' +
                            '<td class="delete">' +
                            '<a title="Delete" href="'+baseDir+'modules/onepagecheckout/order-opc.php?deleteDiscount='+last_discount["id"]+'">' +
                            '<img class="icon" width="11" height="13" alt="Delete" src="'+imgDir+'icon/delete.gif">' +
                            '</a>' +
                            '</td>' +
                            '</tr>'
                            );
                    }
                    overrideDeleteDiscount();
                }
                
                updateCartSummary(jsonData.summary);
		updateCarrierSelectionAndGift();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to confirm the order \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function saveAddress(type)
{
    if (type != 'delivery' && type != 'invoice')
        return false;
	
    var params = 'firstname='+encodeURIComponent($('#firstname'+(type == 'invoice' ? '_invoice' : '')).val())+'&lastname='+encodeURIComponent($('#lastname'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'company='+encodeURIComponent($('#company'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'vat_number='+encodeURIComponent($('#vat_number'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'dni='+encodeURIComponent($('#dni'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'address1='+encodeURIComponent($('#address1'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'address2='+encodeURIComponent($('#address2'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'postcode='+encodeURIComponent($('#postcode'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'city='+encodeURIComponent($('#city'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'id_country='+encodeURIComponent($('#id_country'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'id_state='+encodeURIComponent($('#id_state'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'other='+encodeURIComponent($('#other'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'phone='+encodeURIComponent($('#phone'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'phone_mobile='+encodeURIComponent($('#phone_mobile'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'alias='+encodeURIComponent($('#alias'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    // Clean the last &
    params = params.substr(0, params.length-1);

    var result = false;
	
    $.ajax({
        type: 'POST',
        url: addressUrl,
        async: false,
        cache: false,
        dataType : "json",
        data: 'ajax=true&submitAddress=true&type='+type+'&'+params+'&token=' + static_token,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var tmp = '';
                var i = 0;
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                    {
                        i = i+1;
                        tmp += '<li>'+jsonData.errors[error]+'</li>';
                    }
                tmp += '</ol>';
                var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
		if (type == "invoice")
		{
                  $('#opc_account_errors_invoice').html(errors).slideDown('slow');

    		  var scroll_pos = $('#opc_account_errors_invoice').offset().top;
    		  if (isFixedSummary)
      		    scroll_pos -= $('#tfoot_static').height();
    		  $.scrollTo(scroll_pos-5, 800);	
                  //$.scrollTo('#opc_account_errors_invoice', 800);
		} else {
                  $('#opc_account_errors').html(errors).slideDown('slow');
    		  var scroll_pos = $('#opc_account_errors').offset().top;
    		  if (isFixedSummary)
      		    scroll_pos -= $('#tfoot_static').height();
    		  $.scrollTo(scroll_pos-5, 800);	
                  //$.scrollTo('#opc_account_errors', 800);
		}
                $('#opc_new_account-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                $('#opc_payment_methods-overlay').fadeOut('slow');
                setPaymentModuleHandler();
                result = false;
            }
            else
            {
                // update addresses id
                $('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
                $('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
	
                result = true;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });

    return result;
}

// OPCKT pre-save address (weak save, due to dynamic carrier / tax / payment behavior)
function pre_saveAddress(type)
{
    if (type != 'delivery' && type != 'invoice')
        return false;
	
    var params = 'postcode='+encodeURIComponent($('#postcode'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'id_country='+encodeURIComponent($('#id_country'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'id_state='+encodeURIComponent($('#id_state'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    params += 'type='+encodeURIComponent(type)+'&';
    if ($('#invoice_address').is(':checked'))
        params += 'invoice_address=1'+'&';
    else
        params += 'invoice_address=0'+'&';


    //	params += 'id_country_invoice='+encodeURIComponent($('#id_country_invoice'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    //params += 'id_state_invoice='+encodeURIComponent($('#id_state_invoice'+(type == 'invoice' ? '_invoice' : '')).val())+'&';
    // Clean the last &
    params = params.substr(0, params.length-1);

    var result = false;
	
    $.ajax({
        type: 'POST',
        url: addressUrl,
        async: false,
        cache: false,
        dataType : "json",
        data: 'ajax=true&partialSubmitAddress=true&type='+type+'&'+params+'&token=' + static_token,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var tmp = '';
                var i = 0;
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                    {
                        i = i+1;
                        tmp += '<li>'+jsonData.errors[error]+'</li>';
                    }
                tmp += '</ol>';
                var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
		if (type == "invoice")
		{
                  $('#opc_account_errors_invoice').html(errors).slideDown('slow');
    		  var scroll_pos = $('#opc_account_errors_invoice').offset().top;
    		  if (isFixedSummary)
      		    scroll_pos -= $('#tfoot_static').height();
    		  $.scrollTo(scroll_pos-5, 800);	
                  //$.scrollTo('#opc_account_errors_invoice', 800);
		} else {
                  $('#opc_account_errors').html(errors).slideDown('slow');
    		  var scroll_pos = $('#opc_account_errors').offset().top;
    		  if (isFixedSummary)
      		    scroll_pos -= $('#tfoot_static').height();
    		  $.scrollTo(scroll_pos-5, 800);	
                  //$.scrollTo('#opc_account_errors', 800);
		}
                $('#opc_new_account-overlay').fadeOut('slow');
                $('#opc_delivery_methods-overlay').fadeOut('slow');
                $('#opc_payment_methods-overlay').fadeOut('slow');
                setPaymentModuleHandler();
                result = false;
            }
            else
            {
                // update addresses id
                $('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
                $('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
		// update choose another address
		if (type == 'invoice') {
		  $('#inv_addresses').val(jsonData.id_address_invoice);
		}
		  
                updateAddressSelection(jsonData.id_address_delivery, jsonData.id_address_invoice);
                // EU VAT management
                if (type == 'delivery')
                    if (jsonData.allow_eu_vat == 1)
                        $('#vat_number_block').show();
                    else
                        $('#vat_number_block').hide();

                if (type == 'invoice')
                    if (jsonData.allow_eu_vat == 1)
                        $('#vat_number_block_invoice').show();
                    else
                        $('#vat_number_block_invoice').hide();

	
                result = true;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });

    return result;
}

function updateNewAccountToAddressBlock()
{
    $('#opc_new_account-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $('#opc_payment_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getAddressBlockAndCarriersAndPayments&token=' + static_token ,
        success: function(jsonData)
        {
            // update block user info
            if (jsonData.block_user_info != '' && $('#header_user').length == 1)
            {
                $('#header_user').fadeOut('slow', function() {
                    $(this).attr('id', 'header_user_old').after(jsonData.block_user_info).fadeIn('slow');
                    $('#header_user_old').remove();
                });
            }
            //updateAddressSelection(jsonData.id_address_delivery, jsonData.id_address_invoice);

            //updateCarrierList(jsonData);
            //updatePaymentMethods(jsonData);
            updateCartSummary(jsonData.summary);
            //updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
            //updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
            // OPCKT added
            //updateAddressesForms(jsonData);
            updateFormsAfterLogin(jsonData);
            //if ($('#gift-price').length == 1)
            //        $('#gift-price').html(jsonData.gift_price);
            $('#opc_new_account-overlay').fadeOut('slow');
            //setPaymentModuleHandler();



            //updateAddressesDisplay(true);
            updateCarrierList(jsonData.carrier_list);
            updatePaymentMethods(jsonData);
            if ($('#gift-price').length == 1)
                $('#gift-price').html(jsonData.gift_price);
            $('#opc_delivery_methods-overlay').fadeOut('slow');
            $('#opc_payment_methods-overlay').fadeOut('slow');
            $('#existing_email_msg').fadeOut('slow');
            setPaymentModuleHandler();
	    nextTimeAnimateInv = true;
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function submitAccount(payment_module_button) {
    //$('#opc_new_account-overlay').show();
    //$('#opc_delivery_methods-overlay').show();
    //$('#opc_payment_methods-overlay').show();
  
    // TOS active and checked?
    var tos_nok = ($('input#cgv').length && $('input#cgv:checked').length == 0);
    if (tos_nok) {
	errors = '<b>'+txtTOSIsNotAccepted+'</b>';
        $('#opc_tos_errors').html(errors).slideUp('fast').slideDown('slow');
        //$.scrollTo('#opc_tos_errors', 600);
	processing_payment = false;// Enable payment buttons again
	return false;
    }
 
    $('#opc_new_account-overlay').fadeIn('slow');
    $('#opc_delivery_methods-overlay').fadeIn('slow');
    $('#opc_payment_methods-overlay').fadeIn('slow');
			
    // RESET ERROR(S) MESSAGE(S)
    $('#opc_account_errors').html('').hide();
    $('#opc_account_errors_invoice').html('').hide();
			
    if ($('input#opc_id_customer').val() == 0)
    {
        var callingFile = authenticationUrl;
        var params = 'submitAccount=true&';
    }
    else
    {
        var callingFile = orderOpcUrl;
        var params = 'method=editCustomer&';
    }
			
    $('#opc_account_form input:visible, #offer_password input:visible').each(function() {
        if ($(this).is('input[type=checkbox]'))
        {
            if ($(this).is(':checked'))
                params += encodeURIComponent($(this).attr('name'))+'=1&';
        }
        else if ($(this).is('input[type=radio]'))
        {
            if ($(this).is(':checked'))
                params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
        }
        else
            params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
    });
    $('#opc_account_form select:visible').each(function() {
        params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
    });
    
    // Fix for country ID when field is hidden
    $('#opc_account_form select#id_country:hidden, #opc_account_form select#id_country_invoice:hidden,').each(function() {
        params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
    });
    
    params += 'customer_lastname='+encodeURIComponent($('#lastname').val())+'&';
    params += 'customer_firstname='+encodeURIComponent($('#firstname').val())+'&';
    params += 'alias='+encodeURIComponent($('#alias').val())+'&';
    params += 'other='+encodeURIComponent($('#other').val())+'&';
    params += 'is_new_customer='+encodeURIComponent($('#is_new_customer').val())+'&';
    // Clean the last &
    params = params.substr(0, params.length-1);
		
    var ret_value = true;
    $.ajax({
        type: 'POST',
        url: callingFile,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&'+params+'&token=' + static_token ,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var tmp = '';
                var i = 0;
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                    {
                        i = i+1;
                        tmp += '<li>'+jsonData.errors[error]+'</li>';
                    }
                tmp += '</ol>';
                var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
                $('#opc_account_errors').html(errors).slideDown('slow');
		var scroll_pos = $('#opc_account_errors').offset().top;
		if (isFixedSummary)
		  scroll_pos -= $('#tfoot_static').height();
                $.scrollTo(scroll_pos-5, 800);	
                ret_value = false;
		// Inline validation including empty fields
   		var skipEmpty = false;
		if (opc_inline_validation == "1")
    		  validateAllFieldsNow(skipEmpty);
            }

            isGuest = ($('#is_new_customer').val() == 1 ? 0 : 1);
					
            if (jsonData.id_customer != undefined && jsonData.id_customer != 0 && jsonData.isSaved)
            {
                // update token
                static_token = jsonData.token;
						
                // update addresses id
                $('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
                $('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
			
                // It's not a new customer
                if ($('input#opc_id_customer').val() != '0')
                {
                    if (!saveAddress('delivery'))
                    {
		 	processing_payment = false;// Enable payment buttons again
                        ret_value = false;
                        return false;
                    }
                }
						
                // update id_customer
                $('input#opc_id_customer').val(jsonData.id_customer);
						
                if ($('#invoice_address:checked').length != 0)
                {
                    if (!saveAddress('invoice'))
                    {
		 	processing_payment = false;// Enable payment buttons again
                        ret_value = false;
                        return false;
                    }
                }
						
                // update id_customer
                $('input#opc_id_customer').val(jsonData.id_customer);
						
            // force to refresh carrier list
            /*	if (isGuest)
						{
							$('#opc_account_saved').fadeIn('slow');
							$('#submitAccount').hide();
							updateAddressSelection();
						}
						*/
            //else
            //	updateNewAccountToAddressBlock();
					
            }
            $('#opc_new_account-overlay').fadeOut('slow');
            $('#opc_delivery_methods-overlay').fadeOut('slow');
            $('#opc_payment_methods-overlay').fadeOut('slow');
            setPaymentModuleHandler();

	    if (ret_value) {
	      if (opc_relay_update == "1")
		updatePaymentsOnly();
	      var link_href = payment_module_button.attr("href");
	      if (link_href !== undefined) {
	        window.location.href = link_href;
	      } else {
	        programatically_clicked = true;
	        payment_module_button.click();
	      }
	    } else {
		 processing_payment = false;// Enable payment buttons again
	    }//if (ret_value)


        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to save account \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            ret_value = false;
        }
    });
    return ret_value;

}//submitAccount()

var isFixedCart=false;
var isFixedInfo=false;
var isFixedSummary=false;
var cart_block, cart_block_anchor, info_block_anchor;
var topOffsetCart, topOffsetSummary, topOffsetInfoBlock, topOffsetBeforeInfoBlock, staticWidth;
var tfoot_static, tfoot_static_underlay, cart_summary;
var info_below_cart = true;
var cart_position = "static"; // set to actual position when changing to fixed
var cart_top = 0; // set to actual top position when changing to fixed

// OPCKT added methods
function setScrollHandler() {

    // opc_scroll_cart - global JS variable set in order-opc.tpl
    var scroll_cart = (opc_scroll_cart == "1");
    var scroll_summary = (opc_scroll_summary == "1");
    var scroll_info = (opc_scroll_info == "1") && _isInfoBlockEnabled();

    info_below_cart = (opc_before_info_element == '#cart_block' && scroll_cart && scroll_info);

    if (scroll_cart || scroll_summary || scroll_info)
    {
        if (scroll_cart /*&& (window.ajaxCart !== undefined)*/) {
            // cart scrolling
            if (info_below_cart)
              cart_block = $("#cart_scroll_section");
            else
              cart_block = $("#cart_block");
            cart_block.css('z-index', 100);
            cart_block.before("<div id=\"cart_block_anchor\" style=\"display:none; height:1px;\">&nbsp;</div>");
            cart_block_anchor = $("#cart_block_anchor");

            topOffsetCart = cart_block.offset().top;
        }

	if (scroll_info && !info_below_cart) {
	    info_block = $("#opc_info_block");
            info_block.css('z-index', 100);
            info_block.before("<div id=\"info_block_anchor\" style=\"display:none; height:1px;\">&nbsp;</div>");
            info_block_anchor = $("#info_block_anchor");

	    if ($(opc_before_info_element).length > 0)
              topOffsetBeforeInfoBlock = $(opc_before_info_element).offset().top;
	}

        if (scroll_summary) {
            // sumary scrolling
            tfoot_static = $("#tfoot_static");
            tfoot_static_underlay = $("#tfoot_static_underlay");
            cart_summary = $("#cart_summary");

            topOffsetSummary = tfoot_static.offset().top - parseFloat(tfoot_static.css('margin-top').replace(/auto/, 0));
            staticWidth = tfoot_static.width();
            tfoot_static_underlay.width(tfoot_static.width());
            tfoot_static_underlay.height(tfoot_static.height());
        }


        $(window).scroll(function() {
            var y = $(this).scrollTop();
//console.info('scrolled to '+y+', isFixed: cart, info, summary'+isFixedCart+isFixedInfo+isFixedSummary+', topOffsetInfo='+topOffsetInfoBlock);
            if (scroll_cart) {
                if (!isFixedCart && y >= topOffsetCart) {
                    cart_block_anchor.show();
		    cart_position = cart_block.css("position");
		    if (cart_position != "absolute" && cart_position != "static")
		      cart_position = "static";
		    cart_top = cart_block.css("top");
		    if (cart_top == "")
			cart_top = 0;
		    
                    cart_block.css("position", "fixed").css("top", 0);
                    cart_block_anchor.css("margin-bottom", cart_block.height()+parseFloat(cart_block.css('margin-bottom').replace(/auto/, 0)));
                    isFixedCart = true;
		    if (!info_block_displayed && info_below_cart) displayInfoBlock();
                } else {
                    if (isFixedCart && y < topOffsetCart) {
                        cart_block_anchor.hide();
                        cart_block.css("position", cart_position);
                        cart_block.css("top", cart_top);
                        cart_block_anchor.css("margin-bottom", 0);
                        isFixedCart = false;
		    	//if (info_block_displayed) hideInfoBlock();
                    }
                }
            }//if(scroll_cart)
            if (scroll_info && !info_below_cart) {

		if (!isFixedInfo && y >= topOffsetBeforeInfoBlock && !info_block_displayed) {
		  displayInfoBlock();
		  topOffsetInfoBlock = $("#opc_info_block").offset().top;
		}
                if (!isFixedInfo && y >= topOffsetInfoBlock && topOffsetInfoBlock !== undefined) {
                    info_block_anchor.show();
                    info_block.css("position", "fixed").css("top", 0);
                    info_block_anchor.css("margin-bottom", info_block.height()+parseFloat(info_block.css('margin-bottom').replace(/auto/, 0)));
                    isFixedInfo = true;
		    //if (!info_block_displayed) displayInfoBlock();
                } else {
                    if (isFixedInfo && y < topOffsetInfoBlock) {
                        info_block_anchor.hide();
                        info_block.css("position", "static");
                        info_block_anchor.css("margin-bottom", 0);
                        isFixedInfo = false;
		    	//if (info_block_displayed) hideInfoBlock();
                    }
                }
            }//if(scroll_cart)
            if (scroll_summary) {
                if (!isFixedSummary && y >= topOffsetSummary) {
                    tfoot_static_underlay.show();
                    tfoot_static.css("position", "fixed").css("top", 0).width(staticWidth);
                    cart_summary.css("margin-bottom", tfoot_static.height());
                    tfoot_static.addClass('floating-summary');
            	    tfoot_static_underlay.height(tfoot_static.height());
                    isFixedSummary = true;
                } else {
                    if (isFixedSummary && y < topOffsetSummary) {
                        tfoot_static_underlay.hide();
                        tfoot_static.css("position", "static");
                        cart_summary.css("margin-bottom", 0);
                        tfoot_static.removeClass('floating-summary');
                        isFixedSummary = false;
                    }
                }
            }//if(scroll_summary)

        });
    }//if(scroll_cart||scroll_summary)
}//setScrollHandler()

var payment_ret_val = true;
var programatically_clicked = false; 
var processing_payment = false;

function setPaymentModuleHandler() {
    // Payment modules - safe mode (OPCKT)
    $('p.payment_module').unbind('click').click(function(e) {
	if (programatically_clicked) {
	  programatically_clicked = false;
	  return true;
	}
	// disable repeated click
	if (processing_payment)
	  return false;
	processing_payment = true;

	var el = $(e.target);
	var levels = 3; // search for clickable parent this much levels

	while (levels > 0 && !el.is("input") && !el.is("a")) {
	  el = el.parent();
	  levels--;
	}
        var ret = submitAccount(el);
        if (ret) {
            $('registerme').hide();
        }
	return false;
	//payment_ret_val = ret;	
        //return ret;
    });
    // Payment modules error message hide (OPCKT)
    $('input[name=id_payment_method]').unbind('change').change(function() {
	$('#opc_payment_errors').slideUp('slow');	
    });
}//setPaymentModuleHandler()

function paymentModuleConfirm() {
    var errors = '';
    var link_id = $('input[name=id_payment_method]:checked').val();
    if (link_id === undefined) {
	errors = '<b>'+errorPayment+'</b>';
        $('#opc_payment_errors').html(errors).slideUp('fast').slideDown('slow');
        //$.scrollTo('#opc_payment_errors', 300);
    }else {
        link_id = '#'+link_id;
        $(link_id).click();
       // var link_href = $(link_id).attr("href");
       // if (link_href !== undefined && payment_ret_val) {
       //     window.location.href = link_href;
       // }
    }
}


function cartBlockCheckoutButtonHandler() {


    $.scrollTo('#opc_payment_methods', 1200);

    //$("#opc_payment_methods").prev("div")
    $('#opc_payment_methods').fadeTo(800, 0.2, function() { 
        $('#opc_payment_methods').fadeTo(400, 0.9, function() { 
            $('#opc_payment_methods').fadeTo(400, 0.4, function() { 
                $('#opc_payment_methods').fadeTo(400, 1);  
            });
        });
    });
    return false;

}

function country_change_handler(force_display) {
    pre_saveAddress('delivery');
    validateFieldAndDisplayInline($('#postcode')); 
    return false;
}//country_change_handler()


function state_change_handler(force_display) {
    pre_saveAddress('delivery');
}//state_change_handler()

function invoice_country_change_handler(force_display) {
    pre_saveAddress('invoice'); 
    validateFieldAndDisplayInline($('#postcode_invoice')); 
    return false;
}
function invoice_state_change_handler(force_display) {
    pre_saveAddress('invoice');
}

function overrideItemHandlers() {

    $('#id_country').unbind('change').change(function(){
        updateState(); // USA states
        updateNeedIDNumber(); // Spanish DNI
        updateZipCode();
        country_change_handler(false);
    });

    $('#id_state').unbind('change').change(function(){
        state_change_handler(false);
    // updateState(); // USA states
    });

    $('#id_country_invoice').unbind('change').change(function(){
        updateState('invoice'); // USA states
        updateNeedIDNumber('invoice'); // Spanish DNI
        updateZipCode('invoice');
        invoice_country_change_handler(false);
    });

    $('#id_state_invoice').unbind('change').change(function(){
        invoice_state_change_handler(false);
    // updateState(); // USA states
    });
    $('input[name=submitAddDiscount]').unbind('click').click(function() {
       submitDiscount('add');
       return false;
    });
    overrideDeleteDiscount();

        $('#existing_email_login').click(function(){
            $('#login_form_content #login_email').val($('#new_account_form #email').val());
	    $('#login_form_content').show('slow', function(){$('#login_form_content #login_passwd').focus();});
            return false;
        });
    
}//overrideItemHandlers()




function overrideDeleteDiscount() {
        $('td.cart_discount_delete a, #cart_block_list table#vouchers td.delete a').unbind('click').click(function() {
        var orig_link = $(this).attr('href');
        var id_discount = orig_link.match(/deleteDiscount=(\d+)/);
       submitDiscount('delete', id_discount[1]);
       return false;
    });
}

function setSampleHints() {
  if (opc_sample_values == "1") {
      $('form#new_account_form input, form#offer_password input').focus(function(){
	$(this).nextAll('span.sample_text').removeClass('ex_blur').addClass('ex_focus');
	return false;
      });
      $('form#new_account_form input, form#offer_password input').blur(function(){
	$(this).nextAll('span.sample_text').removeClass('ex_focus').addClass('ex_blur');
	return false;
      });
  }//if(opc_sample_values)
}//setSampleHints()


// required, validation_method, min_length
var fields_definition = {
  'email': [true, 'isEmail', 3],
  'passwd': [true, 'isPassword', 5],
//  'days': [false, 'isNumber', 1],
//  'months': [false, 'isNumber', 1],
//  'years': [false, 'isNumber', 4],
  'company': [false, 'isText', 2],
  'dni': [true, 'isText', 9],
  'vat_number': [false, 'isText', 8],
  'firstname': [true, 'isText', 2],
  'lastname': [true, 'isText', 2],
  'address1': [true, 'isAddress', 3],
  'address2': [false, 'isText', 2],
  'postcode': [true, 'isPostcode', 3],
  'city': [true, 'isText', 2],
  'id_country': [true, 'isNumber', 1],
  'id_state': [true, 'isNumber', 1],
  'phone_mobile': [true, 'isPhone', 6],
  'phone': [false, 'isPhone', 6],
  'other': [false, 'isText', 2],
  'company_invoice': [false, 'isText', 2],
  'firstname_invoice': [true, 'isText', 2],
  'lastname_invoice': [true, 'isText', 2],
  'address1_invoice': [true, 'isAddress', 3],
  'address2_invoice': [false, 'isText', 2],
  'postcode_invoice': [true, 'isPostcode', 3],
  'city_invoice': [true, 'isText', 2],
  'id_country_invoice': [true, 'isNumber', 1],
  'id_state_invoice': [true, 'isNumber', 1],
  'phone_mobile_invoice': [true, 'isPhone', 6],
  'phone_invoice': [false, 'isPhone', 6],
  'other_invoice': [false, 'isText', 2]
}

function isText(field_value, min_length) {
  return (field_value.length >= min_length)
}

function isAddress(field_name, field_value, min_length) {

  if (field_value.length < min_length)
    return false;
  if (opc_check_number_in_address == "1") {
	  var pattern = /\d/;
	  if (!field_value.match(pattern)) {
	    notificationMessage($('#'+field_name).parent(), ntf_number_in_address_missing);
	    return false;
	  }
	  else {
	    closeNotificationMessage($('#'+field_name).parent());
	    return true;
	  }
  } else {
    return true;
  }
}//isAddress()

function isEmail(field_value, min_length) {
  // ajax query - is this email already used? Offer log-in
    var password_shown = $('#login_form').is(':visible');

    if (password_shown) {
            var email_field = $('#new_account_form #email');
            var email = email_field.val();
            if (email != '') {
                $.ajax({
                    type: 'POST',
                    url: orderOpcUrl,
                    async: true,
                    cache: false,
                    dataType : "json",
                    data: 'ajax=true&method=emailCheck&cust_email=' + email,
                    success: function(jsonData)
                    {
                        if (jsonData.is_registered == 1) {
                            $('p#p_registerme, form#offer_password').hide();
                            $('#existing_email_msg').show();
                        }
                        else {
                            $('p#p_registerme, form#offer_password').show();
                            $('#existing_email_msg').hide();
                        }
  			var formatOk = (/^.+@.+\..+$/i.test(field_value) && field_value.length >= min_length)
			if (formatOk) {
			  email_field.removeClass("error_field").addClass("ok_field");
			} else {
			  email_field.removeClass("ok_field").addClass("error_field");
			}
           		if (opc_validation_checkboxes == "1")
			  if (formatOk) {
             		    email_field.nextAll('span.validity').removeClass('valid_loading').addClass('valid_ok');
			  } else {
             		    email_field.nextAll('span.validity').removeClass('valid_loading').addClass('valid_nok');
			  }
                    }//sucess:
                });
      		return 3; // loader
            }//if(email!='')
        //console.info('blur: '+$(this).val()+','+$(this).attr('name'));

    }//if(password_shown)
    return (/^.+@.+\..+$/i.test(field_value) && field_value.length >= min_length);
}//isEmail()

function isPostcode(field_name, field_value, min_length) {

    // field_name can be postcode or postcode_invoice, depending on that we'll pass correct id_country
    var id_country = (field_name == 'postcode')?$('#id_country').val():$('#id_country_invoice').val();
    var postcode_field = $('#'+field_name);
    var result_ok = false;

    if (field_value != "" && id_country != "")
    {
                $.ajax({
                    type: 'POST',
                    url: orderOpcUrl,
                    async: true,
                    cache: false,
                    dataType : "json",
                    data: 'ajax=true&method=zipCheck&id_country=' + id_country +'&postcode='+field_value,
                    success: function(jsonData)
                    {
			if (jsonData.is_ok) {
			  result_ok = true;
			  postcode_field.removeClass("error_field").addClass("ok_field");
                        } else {
                          postcode_field.removeClass("ok_field").addClass("error_field");
                        }

                        if (opc_validation_checkboxes == "1")
                          if (jsonData.is_ok) {
                            postcode_field.nextAll('span.validity').removeClass('valid_loading').addClass('valid_ok');
                          } else {
                            postcode_field.nextAll('span.validity').removeClass('valid_loading').addClass('valid_nok');
                          }

                    }//sucess:
                });
      		return 3; // loader
    }//if(field_value!=''&&id_country!='')
    //console.info('blur: '+$(this).val()+','+$(this).attr('name'));

    return (field_value.length >= min_length);
}//isPostcode()

function isPassword(field_value, min_length) {
  return (field_value.length >= min_length)
}

function isNumber(field_value, min_length) {
  return (/^\d+$/i.test(field_value) && field_value.length >= min_length)
}

function isPhone(field_value, min_length) {
  return (/^[\d-. _+,]+$/i.test(field_value) && field_value.length >= min_length)
}

// returns: 0: invalid/required, 1: valid, 2: invalid/not-required
function validateField(field_name, field_value) {
   var field_def = fields_definition[field_name];
   field_value = jQuery.trim(field_value);
   var validity_check = 1;
   if (field_def !== undefined) {
       var valid = true;
       if (field_def[1] == 'isText')
           valid = isText(field_value, field_def[2]);
       else if (field_def[1] == 'isEmail')
           valid = isEmail(field_value, field_def[2]);
       else if (field_def[1] == 'isAddress')
           valid = isAddress(field_name, field_value, field_def[2]);
       else if (field_def[1] == 'isPostcode')
           valid = isPostcode(field_name, field_value, field_def[2]);
       else if (field_def[1] == 'isPassword')
           valid = isPassword(field_value, field_def[2]);
       else if (field_def[1] == 'isNumber')
           valid = isNumber(field_value, field_def[2]);
       else if (field_def[1] == 'isPhone')
           valid = isPhone(field_value, field_def[2]);
       
       if (valid) {
	   if (valid == 3)
             validity_check = 3; // just display loader and wait for ajax
	   else
             validity_check = 1;
       } else {
           if (field_def[0]) // is required?
               validity_check = 0;
           else {
               if (field_value == '') // is empty?
                validity_check = 1;
               else
                validity_check = 2;
           }
       }
   }
   return validity_check;
}


function validateFieldAndDisplayInline(_this)
{
       var validity_check = validateField(_this.attr('name'), _this.val());
       //console.info(_this.val()+' on name='+_this.attr('name')+'validity_check='+validity_check); 
       if (validity_check == 0) { // invalid & required
           _this.removeClass("ok_field").addClass("error_field");
           // and display red triangle or exclamation mark
            if (opc_validation_checkboxes == "1")
             _this.nextAll('span.validity').removeClass('valid_ok').removeClass('valid_blank').removeClass('valid_loading').removeClass('valid_warn').addClass('valid_nok');
       }
       else if (validity_check == 1) { // valid
           _this.removeClass("error_field").addClass("ok_field");
           // and display green check
           if (opc_validation_checkboxes == "1")
             _this.nextAll('span.validity').removeClass('valid_nok').removeClass('valid_blank').removeClass('valid_loading').removeClass('valid_warn').addClass('valid_ok');
       } 
       else if (validity_check == 2) { // invalid but not required
           // remove green check
           _this.removeClass("ok_field").addClass("error_field");
           if (opc_validation_checkboxes == "1")
             _this.nextAll('span.validity').removeClass('valid_nok').removeClass('valid_ok').removeClass('valid_loading').removeClass('valid_blank').addClass('valid_warn');
       }
       else if (validity_check == 3) { // display ajax loader, result will be set in ajax call "success"
           // remove green check
           if (opc_validation_checkboxes == "1")
             _this.nextAll('span.validity').removeClass('valid_nok').removeClass('valid_ok').removeClass('valid_loading').removeClass('valid_blank').removeClass('valid_warn').addClass('valid_loading');
       }

}

function validateAllFieldsNow(skipEmpty) {
    $('form#new_account_form input[type=text], form#new_account_form input[type=password], form#new_account_form select , form#new_account_form textarea, form#offer_password input[type=password]').each(function(){
	if (!skipEmpty || jQuery.trim($(this).val()) != "")
          validateFieldAndDisplayInline($(this));
    });
}

function setFieldValidation() {
    // set Blur validation
    $('form#new_account_form input[type=text], form#new_account_form input[type=password], form#new_account_form select , form#new_account_form textarea, form#offer_password input[type=password]').blur(function(){
       validateFieldAndDisplayInline($(this));    
    });

    // validate on page load - only for non-empty fields!
    var skipEmpty = true;
    validateAllFieldsNow(skipEmpty);
}

var info_block_displayed = false;

function _isInfoBlockEnabled() {
  return (opc_display_info_block == '1' && $('div#cart_block').length && jQuery.trim(opc_info_block_content) != '');
}
function addInfoBlock() {
	if (_isInfoBlockEnabled()) {
//	  $('div#cart_block').wrap('<div id="cart_scroll_section" />');
	  if (opc_before_info_element == '#cart_block' && opc_scroll_cart == "1") {
 	    $('div#cart_block').wrap('<div id="cart_scroll_section" />');
            $('div#cart_block').after('<div id="opc_info_block" class="block">'+ opc_info_block_content + '</div>');
            $('div#opc_info_block').css('width', $('#cart_block').css('width'));
	  } else {
	    if ($(opc_before_info_element).length > 0)
	      $(opc_before_info_element).after('<div id="opc_info_block" class="block">'+ opc_info_block_content + '</div>');
	  }

	  if (isFixedCart)
	    displayInfoBlock();
	}
}

function displayInfoBlock() {
	if (_isInfoBlockEnabled()) {
	  $('div#opc_info_block').slideDown(800).fadeTo(1000, 1); //, function() {$(this).fadeIn(2000);});
	  info_block_displayed = true;
	}
}

function hideInfoBlock() {
	// Disabled for now
	/*if (_isInfoBlockEnabled()) {
	  $('div#opc_info_block').slideUp();
	  info_block_displayed = false;
	}*/
}

function toggle_info_block(collapseStr, expandStr) {
    if ($('#opc_info_block .block_content:visible').length > 0) {
      $('#opc_info_block .block_content').slideToggle('slow');
      $('#toggle_link').html(expandStr);
    }else {
      $('#opc_info_block .block_content').slideToggle('slow');
      $('#toggle_link').html(collapseStr);
    }
}

// override method with same name in themes/prestashop/js/cart-summary.js
function updateHookShoppingCartExtra(html)
{
        $('#HOOK_SHOPPING_CART_EXTRA').html(html);
        if ($('#emptyCartWarning').is(":visible")) {
          // reload page so that all opc related JS tunings (page fading, sticky cart, info box) disappear
          location.reload();
        }
}


// container = element to put message into
// text = what you want the message to say
function notificationMessage(container, text) {
	var notificationDiv = $('div.uniqueNotification', container);

	if (notificationDiv.length == 0) {
          var msg = $('<div class="uniqueNotification"/>').addClass('classic tooltip').html('<a class="x" title="'+ntf_close+'">[x]</a><div class="msg">' + text + '</div>').bind('click', function() { 
		      $(this).hide(); 
		      $(this).prevAll('input').removeClass("error_field").addClass("ok_field");  
		      if (opc_validation_checkboxes == "1")
		        $(this).prevAll('span.validity').removeClass('valid_nok').removeClass('valid_blank').removeClass('valid_loading').removeClass('valid_warn').addClass('valid_ok');
	            });
          container.append(msg);
	  msg.slideDown();
	} else if ( !notificationDiv.is("visible") ) {
	  notificationDiv.slideDown();
	}
}

function closeNotificationMessage(parent_element) {
	$('div.uniqueNotification', parent_element).slideUp();
}

function updatePaymentsOnly() {
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: false,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updatePaymentsOnly&token=' + static_token,
        success: function(json)
        {
            $('div#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
            $('#opc_payment_methods-content div#HOOK_PAYMENT').html(json.HOOK_PAYMENT.orig_hook);
          //  $('#opc_payment_methods-parsed-content div#HOOK_PAYMENT_PARSED').html(json.HOOK_PAYMENT.parsed_content);
         //   $('#opc_payment_methods-overlay').fadeOut('slow');
        //    setPaymentModuleHandler();
        }
    });
}

String.prototype.toCapitalize = function()
{ 
   return this.toLowerCase().replace(/^.|\s\S/g, function(a) { return a.toUpperCase(); });
}


$(function() {
{
    // LOGIN FORM
    $('#openLoginFormBlock').click(function() {
        $('#login_form_content').toggle('slow');
        return false;
    });
    // LOGIN FORM SENDING
    $('#SubmitLoginOpc').click(function() {
        $.ajax({
            type: 'POST',
            url: authenticationUrl,
            async: false,
            cache: false,
            dataType : "json",
            data: 'SubmitLogin=true&ajax=true&email='+encodeURIComponent($('#login_email').val())+'&passwd='+encodeURIComponent($('#login_passwd').val())+'&token=' + static_token ,
            success: function(jsonData)
            {
                if (jsonData.hasError)
                {
                    var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+txtErrors+':</b><ol>';
                    for(error in jsonData.errors)
                        //IE6 bug fix
                        if(error != 'indexOf')
                            errors += '<li>'+jsonData.errors[error]+'</li>';
                    errors += '</ol>';
                    $('#opc_login_errors').html(errors).slideDown('slow');
                }
                else
		{
			// update token
			static_token = jsonData.token;
			updateNewAccountToAddressBlock();
			
			// Uncomment if you have blockslides module
			/*
						$('#toppanel').empty();
						$('#toppanel').load(authenticationUrl + ' #toppanel> *',function() {
							// Expand Panel
							$("#open").click(function(){
								$("div#panel").slideDown("slow");
							});	
							// Collapse Panel
							$("#close").click(function(){
								$("div#panel").slideUp("slow");	
							});		
							// Switch buttons from "Log In | Register" to "Close Panel" on click
							$("#toggle a").click(function () {
								$("#toggle a").toggle();
							});	
						});
			*/
		}

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            }
        });
        return false;
    });
	

    // only for non-empty cart
    if ($('#id_country').length > 0)
    {

	addInfoBlock();	
        setScrollHandler();
        setPaymentModuleHandler();
        overrideItemHandlers();


        if ($('#invoice_address:checked').length != 0)
        {
        //	pre_saveAddress('invoice'); 
        }

        updateState(); // USA states
        updateNeedIDNumber(); // Spanish DNI
        updateZipCode();
        // OPCKT initial order page render
        pre_saveAddress('delivery'); // we need to save address at the very beginning to allow shipping / payment work immediately
        if ($('#invoice_address:checked').length != 0)
        {
            updateState('invoice');
            updateNeedIDNumber('invoice');
            updateZipCode('invoice');
        }
    }

    $("a#button_order_cart").click(cartBlockCheckoutButtonHandler);
	
    // INVOICE ADDRESS
    $('#invoice_address').click(function() {
        if ($('#invoice_address:checked').length > 0)
        {
            // OPCKT added - pre_saveAddress() - to refresh correct id_address_invoice / delivery model state
            $('#invoice_address').attr('disabled', true);
            $('#opc_invoice_address').slideDown('slow', function() {
                pre_saveAddress('invoice');
                $('#invoice_address').removeAttr('disabled');
            });
            //if ($('#company_invoice').val() == '')
            //	$('#vat_number_block_invoice').hide();
            // OPCKT dynamic tax - save invoice address - at least dummy sketch - immediately
            //pre_saveAddress('invoice'); 
            updateState('invoice');
            updateNeedIDNumber('invoice');
            updateZipCode('invoice');
        }
        else {
            $('#invoice_address').attr('disabled', true);
            $('#opc_invoice_address').slideUp('slow', function() {
                pre_saveAddress('delivery');
                $('#invoice_address').removeAttr('disabled');
            });
        }
    });
		
/*                                function vat_number()
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
                                                $('#company').blur(function(){
                                                        vat_number();
                                                });
                                                $('#company_invoice').blur(function(){
                                                        vat_number_invoice();
                                                });
                                                vat_number();
                                                vat_number_invoice();
*/
}
	
// Order message update
$('#message').blur(function() {
    //$('#opc_delivery_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updateMessage&message=' + encodeURIComponent($('#message').val()) + '&token=' + static_token ,
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                alert(errors);
            }
//            else
//                $('#opc_delivery_methods-overlay').fadeOut('slow');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to save message \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
});
	
//// Recyclable checkbox
//$('input#recyclable').click(function() {
//    updateCarrierSelectionAndGift();
//});
	
// Gift checkbox update
$('input#gift').click(function() {
    updateCarrierSelectionAndGift();
    if ($('input#gift').is(':checked'))
        $('p#gift_div').show();
    else
        $('p#gift_div').hide();
});
	
if ($('input#gift').is(':checked'))
    $('p#gift_div').show();
else
    $('p#gift_div').hide();

if ($('input#registerme').is(':checked'))
    $('p.password').show();
else
    $('p.password').hide();
	
//// Gift message update
//$('textarea#gift_message').blur(function() {
//    updateCarrierSelectionAndGift();
//});
	
// TOS
$('#cgv').click(function() {
    if ($('#cgv:checked').length != 0) {
        var checked = 1;
	// display green check in place of red exclamation
	$('#opc_tos_errors').slideUp('slow');
	if (opc_validation_checkboxes == "1")
	  $('#cgv').nextAll('span.validity').removeClass('valid_nok').removeClass('valid_blank').removeClass('valid_loading').removeClass('valid_warn').addClass('valid_ok');
    }
    else {
        var checked = 0;
	if (opc_validation_checkboxes == "1")
	  $('#cgv').nextAll('span.validity').removeClass('valid_ok').removeClass('valid_blank').removeClass('valid_loading').removeClass('valid_warn').addClass('valid_nok');
	// display red exclamation
    }
		
    //$('#opc_payment_methods-overlay').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: orderOpcUrl,
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=updateTOSStatusAndGetPayments&checked=' + checked + '&token=' + static_token,
        success: function(json)
        {
/* Do nothing for now, T&C are checked as any other error, this ajax call is just for checkedTOS cookie setting
            $('div#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
            $('#opc_payment_methods-content div#HOOK_PAYMENT').html(json.HOOK_PAYMENT.orig_hook);
            $('#opc_payment_methods-parsed-content div#HOOK_PAYMENT_PARSED').html(json.HOOK_PAYMENT.parsed_content);
            $('#opc_payment_methods-overlay').fadeOut('slow');
            setPaymentModuleHandler();
*/
        }
    });

});
	
$('#opc_account_form input,select,textarea').change(function() {
    if ($(this).is(':visible'))
    {
        $('#opc_account_saved').fadeOut('slow');
        $('#submitAccount').show();
    }
});
        
if (opc_page_fading == "1") {
    var fading_duration = parseInt(opc_fading_duration);
    var fading_opacity = parseInt(opc_fading_opacity);
    if (isNaN(fading_duration) || fading_duration < 0)
        fading_duration = 3000;
    if (isNaN(fading_opacity) || fading_opacity < 0 || fading_opacity > 100)
        fading_opacity = 40;
    $('#header, #footer, #left_column div, #right_column div').not("div#cart_block, div#cart_block_anchor, div#cart_scroll_section, div#opc_info_block").not($("div#cart_block, div#cart_scroll_section, div#opc_info_block").find('div')).fadeTo(fading_duration, fading_opacity/100);
}

setSampleHints();
if (opc_inline_validation == "1")
    setFieldValidation();

// This juggling is because of PS 1.4.4 tied togeter page_id with php_self variable (unlucky choice)
$("#modules\\/onepagecheckout\\/order-opc").attr("id", "order-opc");

// Close notification below field, when field is focused
$("#address1, #address1_invoice").focus(function() { closeNotificationMessage($(this).parent()); });

//closeNotificationMessage($('#'+field_name).parent());
//$('input[title]').qtip();

// Capitalize letters as customer types for configurable fields
if (jQuery.trim(opc_capitalize_fields) != "") {
  $(opc_capitalize_fields).css('text-transform', 'capitalize');
  $(opc_capitalize_fields).blur(function() { $(this).val($(this).val().toCapitalize()); });
}


});
