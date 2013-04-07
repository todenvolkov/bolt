
var bo_fade_opacity = 0.3;
var opt_prefix = 'opc_';


// Maintain correct id_customer in Edit customer group link
$('#'+opt_prefix+'payment_customer_id').keyup(function() {
  var id_cust = $('#'+opt_prefix+'payment_customer_id').val();
  var link = $('#edit_sim_cust').attr('href');
  if (id_cust > 0)
   $('#edit_sim_cust').attr('href', link.replace(/id_customer=\d+/, "id_customer="+id_cust));
});


// Conditional fields
function setOpacityFields() {
  if ($('input[name='+opt_prefix+'page_fading]:checked').val() == 1) {
    $('input[name^='+opt_prefix+'fading_]').parent().prev('label').andSelf().css('opacity', 1);
  } else {
    $('input[name^='+opt_prefix+'fading_]').parent().prev('label').andSelf().css('opacity', bo_fade_opacity);
  }
}

function setInlineValidationFields() {
  if ($('input[name='+opt_prefix+'inline_validation]:checked').val() == 1) {
    $('input[name='+opt_prefix+'validation_checkboxes]').parent().prev('label').andSelf().css('opacity', 1);
  } else {
    $('input[name='+opt_prefix+'validation_checkboxes]').parent().prev('label').andSelf().css('opacity', bo_fade_opacity);
  }
}


// Realtime change
$('input[name='+opt_prefix+'page_fading]').change(function() {
  setOpacityFields();
});

$('input[name='+opt_prefix+'inline_validation]').change(function() {
  setInlineValidationFields();
});


// On page load - initial settings
setOpacityFields();
setInlineValidationFields();
