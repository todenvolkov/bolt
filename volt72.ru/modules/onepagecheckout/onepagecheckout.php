<?php

abstract class OptionType {
    const RadioBox = 0;
    const InputField = 1;
    const TextField = 2;
}

class Option {

    /**
     * @var OptionType $optionType 
     */
    public $optionType;
    /**
     * @var string $title
     */
    public $title;
    /**
     * @var string $description
     */
    public $description;

    function __construct($optionType, $title, $description)
    {
        $this->optionType = $optionType;
        $this->title = $title;
        $this->description = $description;
    }

}


class OnePageCheckout extends Module {

    /**
     * @var array $module_settings   An array of settings provided on configuration page
     */
    public $conf_prefix = "opc_";
    private $module_settings;
    //$general_settings;
    //private $delivery_address_settings;
    private $need_override_instructions = false;
    private $need_change_options = false;

    function __construct()
    {
	global $cookie;
        $cookie_set = isset($cookie);

        $this->name = 'onepagecheckout';
        $this->tab = 'Commercial Modules by Zelarg';
        $this->version = '2.0.5';
        $this->author = 'Zelarg';

        parent::__construct(); // The parent construct is required for translations

        $this->page = basename(__FILE__, '.php');
        $this->displayName = ($cookie_set)?$this->l('One Page Checkout for PS 1.4'):'One Page Checkout for PS 1.4';
        $this->description = ($cookie_set)?$this->l('Powerful and intuitive checkout process.'):'Powerful and intuitive checkout process.';
        $this->_setOptions();

	// check whether required options are set properly in PS
	$need_options = Configuration::getMultiple(array('PS_GUEST_CHECKOUT_ENABLED','PS_ORDER_PROCESS_TYPE','PS_FORCE_SMARTY_2'));
	if ($need_options['PS_GUEST_CHECKOUT_ENABLED'] == 0 || $need_options['PS_ORDER_PROCESS_TYPE'] == 0 || $need_options['PS_FORCE_SMARTY_2'] == 1)	{
            $this->warning = $this->l('Some Prestashop preferences need to be changed, click for more info.')." ";
	    $this->need_change_options = true;
	}

        // check whether override controllers and classes were copied properly
        if (
                !$this->isOverriden("controllers/ParentOrderController.php", "/VK##1/") ||
                !$this->isOverriden("controllers/AddressController.php", "/VK##1/") ||
                !$this->isOverriden("classes/Customer.php", "/VK##1/")) {
            $this->warning = $this->l('Please copy files from modified_1.4/override/ folder to /override/ directory as instructed on config page.')." ";
            $this->need_override_instructions = true;
        }


    }

    /**
     *
     * @param string $filename
     * @param string $pattern Rexexp pattern to be searched for in a file.
     * @return bool 
     */
    function isOverriden($filename, $pattern)
    {
        $file = _PS_ROOT_DIR_ . "/override/" . $filename;
        $result = false;
        if (file_exists($file)) {
            $file_content = file_get_contents($file);
            $result = (preg_match($pattern, $file_content) > 0);
        }
        return $result;
    }

    function _setOptions()
    {
	global $cookie;
              
        // key=option_name, value=option_type, option_display_name, option_description
        $this->module_settings = array(
            "general" => array(
                "scroll_cart" => new Option(OptionType::RadioBox, "Sticky cart block", "Keep cart block sticky when scrolling down on checkout page."),
                "scroll_summary" => new Option(OptionType::RadioBox, "Sticky cart summary", "Keep cart summary sticky when scrolling down on checkout page. CSS style 'floating-summary' is applied where additional fine-tuning of floating box can be made."),
                "sample_values" => new Option(OptionType::RadioBox, "Sample values", "Display sample values next to checkout form fields. You may want to change values (texts) in BO-Tools-Translations-Front Office, section order-carrier. Also styles 'i.ex_focus' and 'i.ex_blur' in global.css should be created."),
                "compact_form" => new Option(OptionType::RadioBox, "Compact form", "Makes your checkout form more compact by removing redundant labels and captions."),
                "payment_radio_buttons" => new Option(OptionType::RadioBox, "Payment options as radio buttons", "Display payment options as radio buttons on checkout page. If turned off, payment options will be displayed in blocks (default in PS). <u>Please note, for some payment options only block display is supported!</u>"),
                "inline_validation" => new Option(OptionType::RadioBox, "Inline validation", "Perform formal inline validation on checkout fields."),
                "validation_checkboxes" => new Option(OptionType::RadioBox, "Validation checkboxes", "In addition to validation, display green tick or red exclamation mark next to field."),                
                "page_fading" => new Option(OptionType::RadioBox, "Page fading", "Visual effect after visiting checkout form - page elements, except center column and blockcart will be fade out a bit, see two more settings below."),
                "fading_duration" => new Option(OptionType::InputField, "Fading duration", "How long should page fadeOut effect take? It's set in miliseconds, low values (<500) mean very quick fadeOut, above 3000ms it fades out gradually."),
                "fading_opacity" => new Option(OptionType::InputField, "Fading opacity", "Target fading opacity in a range 0-100%, 0-invisible, 100-totally visible."),
                "display_info_block" => new Option(OptionType::RadioBox, "Display info block", "Displays info block below cart block on checkout page. For customization, please see file /modules/onepagecheckout/info-block-content.tpl"),
                "ship2pay" => new Option(OptionType::RadioBox, "Ship2pay module support", "Support for ship2pay module. For more instructions how to set it up, please see UserGuide."),
                "hide_carrier" => new Option(OptionType::RadioBox, "Hide carrier selection", "Hide block with carrier selection, if there is only one carrier for selected country."),				
                "hide_payment" => new Option(OptionType::RadioBox, "Hide payment selection", "Hide block with payment selection, if there is only one payment for selected country / carrier (if ship2pay installed) AND only if 'Payment options on same page' is also enabled."),				
                //"info_block_content" => new Option(OptionType::TextField, "Info block content", "HTML content displayed in info block."),
            ),
            "customer" => array(
                "offer_password_top" => new Option(OptionType::RadioBox, "Offer password on Top", "Display 'Create an account...' checkbox and password field on top of the checkout for. Otherwise, it'll be displayed in the bottom part, above confirm button."),
                "gender" => new Option(OptionType::RadioBox, "Gender", "Display radio buttons with gender selection."),
                "birthday" => new Option(OptionType::RadioBox, "Birthday", "Display dropdowns for birthday."),
                "newsletter" => new Option(OptionType::RadioBox, "Newsletter", "Display 'Sign up for newsletter.' checkbox in checkout form."),
                "newsletter_checked" => new Option(OptionType::RadioBox, "Newsletter checked", "Checkbox 'Sign up for newsletter.' will be checked by default."),
                "special_offers" => new Option(OptionType::RadioBox, "Special offers", "Display 'Sign up for special offers...' checkbox in checkout form."),
                "special_offers_checked" => new Option(OptionType::RadioBox, "Special offers checked", "Checkbox 'Sign up for special offers...' will be checked by default."),
                "order_msg" => new Option(OptionType::RadioBox, "Order message", "Display textbox 'leave us comment about your order'."),
            ),
            "delivery_address" => array(
                "company_delivery" => new Option(OptionType::RadioBox, "Company", "Display field 'Company' in delivery address."),
                "address2_delivery" => new Option(OptionType::RadioBox, "Address (2)", "Display field 'Address (2)' in delivery address."),
                "country_delivery" => new Option(OptionType::RadioBox, "Country", "Display field 'Country' in delivery address."),
                "phone_delivery" => new Option(OptionType::RadioBox, "Phone", "Display field 'Home phone' in delivery address."),
                "additional_info_delivery" => new Option(OptionType::RadioBox, "Additional Info", "Display field 'Additional Information' in delivery address."),
                "check_number_in_address" => new Option(OptionType::RadioBox, "Check number in address", "Check for number in address1 field and display message to customer if they forget to add it."),
                "capitalize_fields" => new Option(OptionType::TextField, "Capitalize fields as typed", "Capitalize following fields as customer types in. E.g.: #postcode, #postcode_invoice, #lastname, #lastname_invoice"),
            ),
            "invoice_address" => array(
                "company_invoice" => new Option(OptionType::RadioBox, "Company", "Display field 'Company' in invoice address."),
                "address2_invoice" => new Option(OptionType::RadioBox, "Address (2)", "Display field 'Address (2)' in invoice address."),
                "country_invoice" => new Option(OptionType::RadioBox, "Country", "Display field 'Country' in invoice address."),
                "phone_invoice" => new Option(OptionType::RadioBox, "Phone", "Display field 'Home phone' in invoice address."),
                "additional_info_invoice" => new Option(OptionType::RadioBox, "Additional Info", "Display field 'Additional Information' in invoice address."),
            ),
            "system" => array(
                "payment_customer_id" => new Option(OptionType::InputField, "Simulated customer id", "Customer ID 'template' for initial payment methods display with proper group restrictions. <br /><a id=edit_sim_cust href=index.php?tab=AdminCustomers&addcustomer&id_customer=".Configuration::get(strtoupper($this->conf_prefix.'payment_customer_id'))."&token=".Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).((isset($cookie))?(int)($cookie->id_employee):0)).">Click here to edit default group for this customer.</a> "),
                "before_info_element" => new Option(OptionType::InputField, "Before InfoBlock element", "Element after which infoblock should be displayed - use this as an alternative if you don't have standard blockcart in left or right column. Element is identified by this jQuery expression."),
                "update_payments_relay" => new Option(OptionType::RadioBox, "Update payments after account save", "This options is required only for relay-style payment modules, or generally any payment module handling with http_cookie, like dibs and ePay. If you use such module and use relay mode, turn this option ON."),
            )            
        );
    }

    public function _getAllOptionsValues() 
    {
        $config_keys = array();
        foreach ($this->module_settings as $options_group) {
            foreach ($options_group as $option_name => $option) {
		$config_keys[] = strtoupper($this->conf_prefix . $option_name);	
            }
        }
	$db_values = Configuration::getMultiple($config_keys);
	$prefix_length = strlen($this->conf_prefix);
	$config = array();
	foreach ($db_values as $key=>$value)
	  $config[strtolower(substr($key, $prefix_length))] = $value;
        
	return $config;
    }

    private function _setSimulatedCustomer() {

	$simulatedCustomer = new Customer();
	$simulatedCustomer->lastname = 'OPC';
	$simulatedCustomer->firstname = 'Module';
	$simulatedCustomer->passwd = 'opcpasswd';
	$simulatedCustomer->email = 'presta.modules@gmail.com';
	$simulatedCustomer->enabled = 0;
	$simulatedCustomer->deleted = 1;
	
	$simulatedCustomer->add();
	return $simulatedCustomer->id;
    }

    private function _copyTranslations() {
	global $cookie;

	$translation_files = array(
	  'order-carrier.tpl',
	  'order-opc-new-account.tpl',
	  'order-opc.tpl',
	  'order-payment.tpl',
	  'payment-methods.tpl',
	  'shopping-cart.tpl'
	);

	$orig_lang = $cookie->id_lang;
	$mod = 'onepagecheckout';
	global $_LANG;

	foreach(Language::getLanguages() as $curr_lang)  // get all active languages
	{
		if ($curr_lang["active"] == 0) continue;
echo "\n\n<hr /> Processing lang ".$curr_lang["name"]."\n\n";
		$cookie->id_lang = $curr_lang["id_lang"];
		Tools::setCookieLanguage();
		// translations are now prepared in $_LANG

		$mod_lang_file = _PS_MODULE_DIR_.$mod.'/'.$curr_lang["iso_code"].".php";


		if (file_exists($mod_lang_file))
		  include($mod_lang_file);
		else
		  $_MODULE = array();
		// $_MODULE is now set

		// read all translation keys with mod='$mod'
		$str_write = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";


		foreach ($translation_files as $filename) {
		   $content = @file_get_contents(_PS_MODULE_DIR_.$mod.'/'.$filename);
		   preg_match_all("/\{l s='(.+?)' mod='$mod'/", $content, $matches);
			foreach ($matches[1] as $string) {
			  $key = Tools::substr(basename($filename), 0, -4).'_'.md5($string);
			  $ps_val = isset($_LANG[$key])?$_LANG[$key]:"";
			  $opc_val = isset($_MODULE['<{'.$mod.'}prestashop>'.$key])?$_MODULE['<{'.$mod.'}prestashop>'.$key]:"";

			  if (trim($opc_val) == "" && trim($ps_val) != "") {
			    $_MODULE['<{'.$mod.'}prestashop>'.$key] = pSQL($ps_val);
			  }
			}

		}

		foreach ($_MODULE as $key => $value) {
			  $str_write .= '$_MODULE[\''.$key.'\'] = \''.$value.'\';'."\n";
		}
		if (!file_put_contents($mod_lang_file, $str_write))
		{
		  echo "<br /><br /><p class='error'>(!) ERROR: We need to write translations to file $mod_lang_file, please grant write permissions.</p><br /><br />";
		  exit;
		}
	}//foreach(Language...)

	$cookie->id_lang = $orig_lang;

    }//_copyTranslations()

    function install()
    {
        if (!parent::install()
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'scroll_cart'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'sample_values'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'compact_form'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'payment_radio_buttons'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'inline_validation'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'validation_checkboxes'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'page_fading'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'fading_duration'), '3000') == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'fading_opacity'), '40') == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'display_info_block'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'newsletter'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'order_msg'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'country_delivery'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'company_invoice'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'country_invoice'), 1) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'payment_customer_id'), $this->_setSimulatedCustomer()) == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'before_info_element'), '#cart_block') == false
                OR Configuration::updateValue(strtoupper($this->conf_prefix.'capitalize_fields'), '#firstname, #firstname_invoice, #lastname, #lastname_invoice, #address1, #address1_invoice, #city, #city_invoice') == false
                OR Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1) == false
                OR Configuration::updateValue('PS_ORDER_PROCESS_TYPE', 1) == false // OPC checkout
                OR Configuration::updateValue('PS_FORCE_SMARTY_2', 0) == false // We need Smarty 3 (tpl_vars, relative includes)
                OR Configuration::updateValue('PS_JS_HTML_THEME_COMPRESSION', 0) == false // Turn off inline JS compression

        )
            return false;

	$this->_copyTranslations();

        return true;
    }

    private function _updateRadioValue($name)
    {
        $ret = "";
        $opc_value = Tools::getValue($this->conf_prefix . $name);
        if ($opc_value != 0 AND $opc_value != 1)
            $ret = '<div class="alert error">' . $this->l($name . ' : Invalid choice.') . '</div>';
        else {
            Configuration::updateValue(strtoupper($this->conf_prefix . $name), intval($opc_value));
            $ret = "";
        }
        return $ret;
    }

    private function _updateValue($name)
    {
        $opc_value = Tools::getValue($this->conf_prefix . $name);
        Configuration::updateValue(strtoupper($this->conf_prefix . $name), $opc_value);
    }

    private function _updateOptions($options_array)
    {
        $ret = "";
        foreach ($options_array as $options_group) {
            foreach ($options_group as $option_name => $option) {
                switch ($option->optionType) {
                    case OptionType::RadioBox: $ret .= $this->_updateRadioValue($option_name);
                        break;
                    default: $ret .= $this->_updateValue($option_name);
                        break;
                }
            }
        }
        return $ret;
    }

    function getContent()
    {
        $this->_html = '<h2>' . $this->displayName . '</h2>';

        if (Tools::isSubmit('submitOPC')) {
            	$output = $this->_updateOptions($this->module_settings);
                $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('Confirmation') . '" />' . $this->l('Settings updated') . '</div>';	
		$this->_html .= $output;
        }

        if ($this->need_override_instructions)
            $this->_html .= '<div class="alert">
                            <strong>Incomplete installation</strong> - 
                            Files <u>AddressController.php</u>, <u>ParentOrderController.php</u> and <u>Customer.php</u>
                            from /modules/onepagecheckout/modified_1.4/override folder need to be copied to /override/controllers and /override/classes.
                            </div>';
        if ($this->need_change_options) {
            $this->_html .= '<div class="alert">
                            <strong>Some prestashop options need to be changed so that OPC could work properly</strong><br />';

		$need_options = Configuration::getMultiple(array('PS_GUEST_CHECKOUT_ENABLED','PS_ORDER_PROCESS_TYPE','PS_FORCE_SMARTY_2'));

		if ($need_options['PS_GUEST_CHECKOUT_ENABLED'] == 0)
		  $this->_html .= "- ".$this->l('Guest checkout option in Preferences must be turned on')."<br />";
		if ($need_options['PS_ORDER_PROCESS_TYPE'] == 0)
		  $this->_html .= "- ".$this->l('Order process type in Preferences must be set to One page checkout')."<br />";
		if ($need_options['PS_FORCE_SMARTY_2'] == 1)
		  $this->_html .= "- ".$this->l('Force smarty v2 option in Preferences must be turned off')."<br />";
            $this->_html .= '</div>'; 
	}

        $this->_displayForm();

        return $this->_html;
    }

    private function _displayOption($title, $name, $desc)
    {
        return '
				<label>' . $this->l($title) . '</label>
				<div class="margin-form">
					<input type="radio" name="'.$this->conf_prefix . $name . '" id="'.$this->conf_prefix . $name . '_on" value="1" ' . (Tools::getValue($this->conf_prefix . $name, Configuration::get(strtoupper($this->conf_prefix . $name))) ? 'checked="checked" ' : '') . '/>
					<label class="t" for="'.$this->conf_prefix . $name . '_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label>
					<input type="radio" name="'.$this->conf_prefix . $name . '" id="'.$this->conf_prefix . $name . '_off" value="0" ' . (!Tools::getValue($this->conf_prefix . $name, Configuration::get(strtoupper($this->conf_prefix . $name))) ? 'checked="checked" ' : '') . '/>
					<label class="t" for="'.$this->conf_prefix . $name . '_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>
					<p class="clear">' . $this->l($desc) . '</p>
				</div>
		';
    }

    private function _displayTextField($title, $name, $desc)
    {
        return '
				<label>' . $this->l($title) . '</label>
				<div class="margin-form">
					<textarea rows="2" cols="50" name="'.$this->conf_prefix . $name . '" id="'.$this->conf_prefix . $name . '">' . Tools::getValue($this->conf_prefix . $name, Configuration::get(strtoupper($this->conf_prefix . $name))) . '</textarea>
					<p class="clear">' . $this->l($desc) . '</p>
				</div>
		';
    }

    private function _displayInputField($title, $name, $desc)
    {
        return '
				<label>' . $this->l($title) . '</label>
				<div class="margin-form">
					<input type="text" size="15" name="'.$this->conf_prefix . $name . '" id="'.$this->conf_prefix . $name . '" value="' . Tools::getValue($this->conf_prefix . $name, Configuration::get(strtoupper($this->conf_prefix . $name))) . '" />
					<p class="clear">' . $this->l($desc) . '</p>
				</div>
		';
    }

    private function _displayOptions($options_array)
    {
        $ret = "";
        foreach ($options_array as $option_name => $option) {
            switch ($option->optionType) {
                case OptionType::RadioBox: $ret .= $this->_displayOption($option->title, $option_name, $option->description);
                    break;
                case OptionType::InputField: $ret .= $this->_displayInputField($option->title, $option_name, $option->description);
                    break;
                case OptionType::TextField: $ret .= $this->_displayTextField($option->title, $option_name, $option->description);
                    break;
                default:
                    break;
            }
        }
        return $ret;
    }

    private function _includeJs() {
	$ret = '<script type="text/javascript" src="../modules/onepagecheckout/js/bo.js"></script>';
	return $ret;
    }

    private function _displayForm()
    {

        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $divLangName = 'text_left¤text_right';

        /* TODO: zmazat?
         * $zones = Zone::getZones(true);
          $zones_html = "<select name=\"default_zone\">\n";
          foreach ($zones AS $zone) {
          $selected = ($zone['id_zone'] == $zone['active'])?" selected=\"selected\"":"";
          if ($zone['enabled'] == 1)
          $zones_html .= "<option value=\"{$zone['id_zone']}\"$selected>{$zone['name']}</option>\n";
          }
          $zones_html .= "</select>";
         */


        $this->_html .= '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
			<fieldset>
				<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Settings') . '</legend>' .
                '<h3>General settings:</h3>' .
                $this->_displayOptions($this->module_settings["general"]) .
                /*
                  '<!--'.
                  $this->_displayOption("Already registered link", "already_registered", "Display 'already registered? click here.' on the top of checkout page for registered customers.").
                  '-->'.
                  $this->_displayOption("Payment options on same page", "payment_sp", "Displays payment options on same page (just below carrier selection).").
                  $this->_displayOption("Ship2pay support", "ship2pay_active", "Enable support for ship2pay module. NB: You need to install ship2pay module to enable this, but do not overwrite classes/Module.php file from ship2pay installation.").
                  $this->_displayOption("Cheapest carrier selection", "cheap_carrier", "Enable the cheapest carrier selection. It will override even default carrier, unless customer manually selects preferred carrier.").
                  $this->_displayInputField("Pick-up carrier", "pickup_crr", "Name of pickup carrier. This will be excluded when selecting cheapest carrier (shipping cost calculation). Supports regexp match, so to match more carriers, you can write: (Pick up place1)|(Pick up place2)").
                  $this->_displayOption("Sticky cart block", "scroll_cart", "Keep cart block sticky when scrolling down on checkout page.").
                  $this->_displayOption("Sticky cart summary", "scroll_summary", "Keep cart summary (totals / shipping) sticky when scrolling down on checkout page.").
                  $this->_displayOption("GA checkout form tracker", "checkout_tracker", "Records filled-in fields into your Google Analytics account, so you can see where customer left checkout form. Requires Google Analytics module installed.").
                  $this->_displayOption("Hide carrier selection", "hide_carrier", "Hide block with carrier selection, if there is only one carrier for selected country.").
                  $this->_displayOption("Hide payment selection", "hide_payment", "Hide block with payment selection, if there is only one payment for selected country / carrier (if ship2pay installed) AND only if 'Payment options on same page' is also enabled.").
                  $this->_displayInputField("Shipping estimation carrier", "shipp_est_crr", "Name of shipping estimation carrier. This options is supported only when you have integrated shipping calculator, e.g. for UPS or USPS.").
                  $this->_displayOption("Dynamic taxes", "dynamic_tax", "Use dynamic tax switching on checkout page. (!) Please note, if turned on, additional update of blockcart module is required, please refer to UserGuide.pdf - dynamic tax section for details.").

                 */
                '<center><input type="submit" name="submitOPC" value="' . $this->l('Save') . '" class="button" /></center>' .
                '<hr />' .
                '<h3>Control which fields or checkboxes are displayed in order form:</h3>' .
                $this->_displayOptions($this->module_settings["customer"]) .
                        '<center><input type="submit" name="submitOPC" value="' . $this->l('Save') . '" class="button" /></center>' .
                '<hr /><h3>Delivery address:</h3>' .
                $this->_displayOptions($this->module_settings["delivery_address"]) .
                        '<center><input type="submit" name="submitOPC" value="' . $this->l('Save') . '" class="button" /></center>' .
                '<hr /><h3>Invoice address:</h3>'.
                $this->_displayOptions($this->module_settings["invoice_address"]) .
                '<hr /><h3>System settings:</h3>'.
                '<p>! Change these only when you understand what they mean - please see UserGuide to read more.<p>'.
                $this->_displayOptions($this->module_settings["system"]) .
                        
                /* $this->_displayOption("Company", "company_delivery", "Display field 'Company' in delivery address.").
                  $this->_displayOption("Address (2)", "address2_delivery", "Display field 'Address (2)' in delivery address.").
                  $this->_displayOption("Country", "country_delivery", "Display field 'Country' in delivery address.").
                  $this->_displayOption("Phone", "phone", "Display field 'Phone' in delivery address.").
                  $this->_displayOption("Additional Info", "additional_info", "Display field 'Additional Information' in delivery address.").
                  $this->_displayOption("Mandatory phone", "phone_mandatory", "Makes field 'Phone' mandatory.").
                  '<hr /><h3>Billing address:</h3>'.
                  $this->_displayOption("Same addresses", "same_addresses", "Display checkbox 'Use same address for billing' in checkout form.").
                  $this->_displayOption("Same addresses reverse", "same_addresses_rev", "Use reverse logic for 'Use same address for billing' - i.e. expand when checkbox is checked. You may also want to rename associated label.").
                  $this->_displayOption("Company", "company_invoice", "Display field 'Company' in billing address.").
                  $this->_displayOption("Address (2)", "address2_invoice", "Display field 'Address (2)' in billing address.").
                  $this->_displayOption("Country", "country_invoice", "Display field 'Country' in billing address.").
                  '<hr /><h3>Password and Email settings:</h3>'.
                  $this->_displayOption("Send password in separate email", "separate_welcome", "Always send generated / user defined password in separate (Welcome) email. Turned off would send password in order confirmation email - unless checked 'Create account' during checkout.").
                  $this->_displayInputField("Auto-generated password length", "pwd_len", "Length of auto-generated passwords. (Default value = 5)").
                  $this->_displayOption("Make email field optional", "optional_email", "Makes email field optional. Email would be auto-generated for all customers who do not fill in email and thus no emails would be sent to them.").
                  $this->_displayOption("Hide email", "hide_email", "This option would make email field optional and would also hide it for each customer.").
                  $this->_displayOption("Show password", "show_password", "Shows password field under email. Customers can define their own password instead of generated one.").
                  $this->_displayInputField("Auto-generated domain", "gen_domain", "Domain part of generated emails. This will show up in email fields in backoffice.").
                  '<hr /><h3>Virtual items:</h3>'.
                  $this->_displayOption("Hide delivery address", "no_delivery", "Hide delivery address when only virtual items in cart.").
                  $this->_displayTextField("Invoice Address Message", "inv_addr_msg", "Message displayed instead of 'Use the same address for billing'.").
                  $this->_displayInputField("Virtual Name", "virtual_name", "String used instead of real name in case delivery address is hidden.").
                  $this->_displayInputField("Virtual Lastname", "virtual_lastname", "String used instead of real lastname in case delivery address is hidden.").
                  $this->_displayInputField("Virtual Address", "virtual_address", "String used instead of real address in case delivery address is hidden.").
                  $this->_displayInputField("Virtual ZIP", "virtual_zip", "String used instead of real ZIP code in case delivery address is hidden.").
                  $this->_displayInputField("Virtual City", "virtual_city", "String used instead of real city code in case delivery address is hidden.").

                 */
                '<center><input type="submit" name="submitOPC" value="' . $this->l('Save') . '" class="button" /></center>
			</fieldset>
		</form>' . $this->_includeJs() ;
    }

    function putContent($xml_data, $key, $field, $forbidden)
    {
        foreach ($forbidden AS $line)
            if ($key == $line)
                return 0;
        $field = htmlspecialchars($field);
        if (!$field)
            return 0;
        return ("\n" . '		<' . $key . '>' . $field . '</' . $key . '>');
    }

    public function uninstall()
    {
        //require(_PS_MODULE_DIR_."onepagecheckout/uninstall_files.php");

        if (!parent::uninstall())
            return false;
        return true;
    }

}

?>
