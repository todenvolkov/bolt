<?php

/*
 * 2007-2011 PrestaShop 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

ControllerFactory::includeController('ParentOrderController');

if(Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(dirname(__FILE__).'/../../modules/vatnumber/vatnumber.php'))
	include_once(dirname(__FILE__).'/../../modules/vatnumber/vatnumber.php');

class OrderOpcktController extends ParentOrderController {

    public $isLogged;

    public function __construct() {
        $this->guestAllowed = true;

        parent::__construct();
    }

    // function originally in ParentOrderController, updated for opckt needs
    private function origParentPreProcess() {
        if (Configuration::get('PS_CATALOG_MODE'))
            $this->errors[] = Tools::displayError('This store has not accepted your new order.');

        if (Tools::isSubmit('submitReorder') AND $id_order = (int) Tools::getValue('id_order')) {
            $oldCart = new Cart(Order::getCartIdStatic((int) $id_order, (int) self::$cookie->id_customer));
            $duplication = $oldCart->duplicate();
            if (!$duplication OR !Validate::isLoadedObject($duplication['cart']))
                $this->errors[] = Tools::displayError('Sorry, we cannot renew your order.');
            elseif (!$duplication['success'])
                $this->errors[] = Tools::displayError('Missing items - we are unable renew your order');
            else {
                self::$cookie->id_cart = $duplication['cart']->id;
                self::$cookie->write();
                Tools::redirect(_ORDEROPCKT_REL_PATH_);
            }
        }

        $this->_submitDiscount();
        
        self::$smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
    }//origParentPreprocess()

    private function _submitDiscount() {
        if ($this->nbProducts) {
            if (Tools::isSubmit('submitAddDiscount') AND Tools::getValue('discount_name')) {
                $discountName = Tools::getValue('discount_name');
                if (!Validate::isDiscountName($discountName))
                    $this->errors[] = Tools::displayError('Voucher name invalid.');
                else {
                    $discount = new Discount((int) (Discount::getIdByName($discountName)));
                    if (Validate::isLoadedObject($discount)) {
                        if ($tmpError = self::$cart->checkDiscountValidity($discount, self::$cart->getDiscounts(), self::$cart->getOrderTotal(), self::$cart->getProducts(), true))
                            $this->errors[] = $tmpError;
                    }
                    else
                        $this->errors[] = Tools::displayError('Voucher name invalid.');
                    if (!sizeof($this->errors)) {
                        self::$cart->addDiscount((int) ($discount->id));
                        if (!Tools::isSubmit('ajax'))
                          Tools::redirect(_ORDEROPCKT_REL_PATH_);
                        else {
                            // refresh discounts
                            self::$cart->resetCartDiscountCache();

                            die(Tools::jsonEncode(array('id_lang'=>self::$cookie->id_lang, 'last_discount'=>$discount, 'summary' => self::$cart->getSummaryDetails())));
                            exit; // OK status
                        }
                    }
                }
                if (Tools::isSubmit('ajax')) {
                    if (sizeof($this->errors)) {
                        die('{"hasError" : true, "discount_name" : "'.Tools::safeOutput($discountName).'", "errors" : ["' . implode('\',\'', $this->errors) . '"]}');
                    }
                } else {
                    self::$smarty->assign(array(
                        'errors' => $this->errors,
                        'discount_name' => Tools::safeOutput($discountName)
                    ));
                }
            } 
            elseif (Tools::getValue('deleteDiscount') AND Validate::isUnsignedId(Tools::getValue('deleteDiscount'))) {
                self::$cart->deleteDiscount((int) (Tools::getValue('deleteDiscount')));
                if (!Tools::isSubmit('ajax'))
                    Tools::redirect(_ORDEROPCKT_REL_PATH_);
                else {
                    die(Tools::jsonEncode(array('summary' => self::$cart->getSummaryDetails())));
                }
            }
            /* Is there only virtual product in cart */
            if ($isVirtualCart = self::$cart->isVirtualCart())
                $this->_setNoCarrier();
        }//if ($this->nbProducts)
    }//_submitDiscount()
    
    public function init() {
        parent::init();
        // if PS 1.4.0 (older than 1.4.1), call setOpcktPaths();, otherwise, it'll be called in setMedia()
        if (version_compare(_PS_VERSION_, '1.4.1') < 0)
                $this->setOpcktPaths ();
    }
    
    private function setOpcktPaths()
    {
        $opc_script_name = "order-opc.php";
        define('_ORDEROPCKT_REL_PATH_', '/modules/onepagecheckout/' . $opc_script_name);
        define('_PS_OPCKT_DIR_', _PS_ROOT_DIR_ . '/modules/onepagecheckout/');
        define('_PS_OPCKT_BASE_URI_', __PS_BASE_URI__ . 'modules/onepagecheckout/');

        $o_link = new Link();
        $opckt_link = $o_link->getPageLink(_ORDEROPCKT_REL_PATH_, $this->ssl);
	$opckt_link = str_replace("//modules/onepage", "/modules/onepage", $opckt_link);

        self::$smarty->assign("opckt_dir", _PS_OPCKT_DIR_);
        self::$smarty->assign("opckt_base_uri", _PS_OPCKT_DIR_);
        self::$smarty->assign("opckt_script", $opckt_link);
        self::$smarty->assign("opckt_script_name", $opc_script_name);
    }
    
    // Performance tuning purposes only.
    function current_millis()
    {
        list($usec, $sec) = explode(" ", microtime());
        return round(((float) $usec + (float) $sec) * 1000);
    }


 
    
    public function preProcess() {
        parent::preProcess();
        
        $this->origParentPreProcess();
        
                
        // OPCKT module settings
        $this->_assignOpcSettings();

	// OPCKT info block (below blockcart)
	$this->_setInfoBlockContent();

        if ($this->nbProducts)
            self::$smarty->assign('virtual_cart', false);
        $this->isLogged = (bool) ((int) (self::$cookie->id_customer) AND Customer::customerIdExistsStatic((int) (self::$cookie->id_customer)));

        if (self::$cart->nbProducts()) {
            if (Tools::isSubmit('ajax')) {
                if (Tools::isSubmit('method')) {
                    switch (Tools::getValue('method')) {
                        case 'emailCheck':
			    if (Tools::isSubmit('cust_email')) {
				$customer_email = Tools::getValue('cust_email');
				$is_registered = (Validate::isEmail($customer_email))?Customer::customerExists($customer_email):0;
				$return = array(
				  'is_registered' => $is_registered
				);	
				die(Tools::jsonEncode($return));
			    }
			    break;
                        case 'zipCheck':
			    if (Tools::isSubmit('id_country')) {

				$id_country = Tools::getValue('id_country');
				if ($id_country > 0) {
				  $errors = array();

				  $country = new Country($id_country);
				  $zip_code_format = $country->zip_code_format;
                        	  if ($country->need_zip_code)
                        	  {
                                    if (($postcode = Tools::getValue('postcode')) AND $zip_code_format)
                                    {
                                        $zip_regexp = '/^'.$zip_code_format.'$/ui';
                                        $zip_regexp = str_replace(' ', '( |)', $zip_regexp);
                                        $zip_regexp = str_replace('-', '(-|)', $zip_regexp);
                                        $zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
                                        $zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
                                        $zip_regexp = str_replace('C', $country->iso_code, $zip_regexp);
                                        if (!preg_match($zip_regexp, $postcode))
                                                $errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.').'<br />'.Tools::displayError('Must be typed as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
                                    }
                                    elseif ($zip_code_format)
                                        $errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is required.');
                                    elseif ($postcode AND !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
                                        $errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.').'<br />'.Tools::displayError('Must be typed as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
                        }

				} //if($id_country>0)
				
				$return = array(
				  'is_ok' => empty($errors),
				  'errors' => $errors
				);	
				die(Tools::jsonEncode($return));
			    }
			    break;
                        case 'updateMessage':
                            if (Tools::isSubmit('message')) {
                                $txtMessage = urldecode(Tools::getValue('message'));
                                $this->_updateMessage($txtMessage);
                                if (sizeof($this->errors))
                                    die('{"hasError" : true, "errors" : ["' . implode('\',\'', $this->errors) . '"]}');
                                die(true);
                            }
                            break;
                        case 'updateCarrierAndGetPayments':
                            if (Tools::isSubmit('id_carrier') AND Tools::isSubmit('recyclable') AND Tools::isSubmit('gift') AND Tools::isSubmit('gift_message')) {
                                if ($this->_processCarrier()) {
                                    
                                  /*  $started = $this->current_millis();
                                    $ret1 = self::$cart->getSummaryDetails();
                                    $milis_took1 = $this->current_millis() - $started;
                                    
                                    $started = $this->current_millis();
                                    $ret2 =Module::hookExec('paymentTop');
                                    $milis_took2 = $this->current_millis() - $started;
                                    
                                    $started = $this->current_millis();
                                    $ret3 = self::_getPaymentMethods();
                                    $milis_took3 = $this->current_millis() - $started;
                                    $resultat = $milis_took1 . "-" .$milis_took2."-".$milis_took3;*/
                                    
                                    $return = array(
                                        //'resultat' => $resultat,
                                        'summary' => self::$cart->getSummaryDetails(),
                                        'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
                                        'HOOK_PAYMENT' => self::_getPaymentMethods()
                                    );
                                    die(Tools::jsonEncode($return));
                                }
                                else
                                    $this->errors[] = Tools::displayError('Error occurred updating cart.');
                                if (sizeof($this->errors))
                                    die('{"hasError" : true, "errors" : ["' . implode('\',\'', $this->errors) . '"]}');
                                exit;
                            }
                            break;
                        case 'updateTOSStatusAndGetPayments':
                            if (Tools::isSubmit('checked')) {
                                self::$cookie->checkedTOS = (int) (Tools::getValue('checked'));
                                die(Tools::jsonEncode(array(
                                            'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
                                            'HOOK_PAYMENT' => self::_getPaymentMethods()
                                        )));
                            }
                            break;
                        case 'updatePaymentsOnly':
                                die(Tools::jsonEncode(array(
                                            'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
                                            'HOOK_PAYMENT' => self::_getPaymentMethods()
                                        )));
                            break;
                        case 'getCarrierList':
                            die(Tools::jsonEncode(self::_getCarrierList()));
                            break;
                        case 'editCustomer':
                            if (!$this->isLogged)
                                exit;
                            $customer = new Customer((int) self::$cookie->id_customer);
                            if (Tools::getValue('years'))
                                $customer->birthday = (int) Tools::getValue('years') . '-' . (int) Tools::getValue('months') . '-' . (int) Tools::getValue('days');
                            #	$_POST['lastname'] = $_POST['customer_lastname'];
                            #	$_POST['firstname'] = $_POST['customer_firstname'];
                            if (trim($_POST['customer_lastname']) == "")
                                $_POST['customer_lastname'] = $_POST['lastname'];
                            if (trim($_POST['customer_firstname']) == "")
                                $_POST['customer_firstname'] = $_POST['firstname'];
                            $this->errors = $customer->validateControler();
                            $customer->newsletter = (int) Tools::isSubmit('newsletter');
                            $customer->optin = (int) Tools::isSubmit('optin');
                            $return = array(
                                'hasError' => !empty($this->errors),
                                'errors' => $this->errors,
                                'id_customer' => (int) self::$cookie->id_customer,
                                'token' => Tools::getToken(false)
                            );
                            if (!sizeof($this->errors))
                                $return['isSaved'] = (bool) $customer->update();
                            else
                                $return['isSaved'] = false;
                            die(Tools::jsonEncode($return));
                            break;
                        case 'getAddressBlockAndCarriersAndPayments':
                            if (self::$cookie->isLogged()) {
                                if (file_exists(_PS_MODULE_DIR_ . 'blockuserinfo/blockuserinfo.php')) {
                                    include_once(_PS_MODULE_DIR_ . 'blockuserinfo/blockuserinfo.php');
                                    $blockUserInfo = new BlockUserInfo();
                                }
                                self::$smarty->assign('isVirtualCart', self::$cart->isVirtualCart());
                                
                                $customer = new Customer((int) self::$cookie->id_customer);
                                $customer_info = array(
                                    "id" => $customer->id,
                                    "email" => $customer->email,
                                    "id_gender" => $customer->id_gender,
                                    "birthday" => $customer->birthday,
                                    "newsletter" => $customer->newsletter,
                                    "optin" => $customer->optin,
                                    "is_guest" => $customer->is_guest
                                );
                                        
                                // TODO: try to get last addresses used by this customer and assign them to self::$cart
                                  
                                
				$this->_processAddressFormat();
                                $this->_assignAddress();
                                
                                $address_delivery = self::$smarty->tpl_vars['delivery']->value;
                                $address_invoice = self::$smarty->tpl_vars['invoice']->value;
                                
                                if (isset($address_delivery) && Configuration::get('VATNUMBER_MANAGEMENT') AND
                                        file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') &&
                                        VatNumber::isApplicable($address_delivery->id_country) &&
                                        Configuration::get('VATNUMBER_COUNTRY') != $address_delivery->id_country)
                                    $allow_eu_vat_delivery = 1;
                                else
                                    $allow_eu_vat_delivery = 0;

                                if (isset($address_invoice) &&  Configuration::get('VATNUMBER_MANAGEMENT') AND
                                        file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') &&
                                        VatNumber::isApplicable($address_invoice->id_country) &&
                                        Configuration::get('VATNUMBER_COUNTRY') != $address_invoice->id_country)
                                    $allow_eu_vat_invoice = 1;
                                else
                                    $allow_eu_vat_invoice = 0;
                                
                                // Wrapping fees
                                $wrapping_fees = (float) (Configuration::get('PS_GIFT_WRAPPING_PRICE'));
                                $wrapping_fees_tax = new Tax((int) (Configuration::get('PS_GIFT_WRAPPING_TAX')));
                                $wrapping_fees_tax_inc = $wrapping_fees * (1 + (((float) ($wrapping_fees_tax->rate) / 100)));
                                $return = array(
                                    'customer_info' => $customer_info,
                                    'allow_eu_vat_delivery' => $allow_eu_vat_delivery,
                                    'allow_eu_vat_invoice' => $allow_eu_vat_invoice,
                                    'customer_addresses' => self::$smarty->tpl_vars['addresses']->value,
                                    'summary' => self::$cart->getSummaryDetails(),
                                    'order_opc_adress' => self::$smarty->fetch(_PS_THEME_DIR_ . 'order-address.tpl'),
                                    'block_user_info' => (isset($blockUserInfo) ? $blockUserInfo->hookTop(array()) : ''),
                                    'carrier_list' => self::_getCarrierList(),
                                    'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
                                    'HOOK_PAYMENT' => self::_getPaymentMethods(),
                                    'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int) (self::$cookie->id_currency))))
                                );
                                die(Tools::jsonEncode($return));
                            }
                            die(Tools::displayError());
                            break;
                        case 'makeFreeOrder':
                            /* Bypass payment step if total is 0 */
                            if (($id_order = $this->_checkFreeOrder()) AND $id_order) {
                                $email = self::$cookie->email;
                                if (self::$cookie->is_guest)
                                    self::$cookie->logout(); // If guest we clear the cookie for security reason
                                die('freeorder:' . $id_order . ':' . $email);
                            }
                            exit;
                            break;
                        case 'updateAddressesSelected':
                            $id_address_delivery = (int) (Tools::getValue('id_address_delivery'));
                            $id_address_invoice = (int) (Tools::getValue('id_address_invoice'));
                            $address_delivery = new Address((int) (Tools::getValue('id_address_delivery')));
                            $address_invoice = ((int) (Tools::getValue('id_address_delivery')) == (int) (Tools::getValue('id_address_invoice')) ? $address_delivery : new Address((int) (Tools::getValue('id_address_invoice'))));

                            if (isset($address_delivery) && Configuration::get('VATNUMBER_MANAGEMENT') AND
                                    file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') &&
                                    VatNumber::isApplicable($address_delivery->id_country) &&
                                    Configuration::get('VATNUMBER_COUNTRY') != $address_delivery->id_country)
                                $allow_eu_vat_delivery = 1;
                            else
                                $allow_eu_vat_delivery = 0;

                            if (isset($address_invoice) && Configuration::get('VATNUMBER_MANAGEMENT') AND
                                    file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') &&
                                    VatNumber::isApplicable($address_invoice->id_country) &&
                                    Configuration::get('VATNUMBER_COUNTRY') != $address_invoice->id_country)
                                $allow_eu_vat_invoice = 1;
                            else
                                $allow_eu_vat_invoice = 0;
                            
                            if (!Address::isCountryActiveById((int) (Tools::getValue('id_address_delivery'))))
                                $this->errors[] = Tools::displayError('This address is not in a valid area.');
                            elseif (!Validate::isLoadedObject($address_delivery) OR !Validate::isLoadedObject($address_invoice) OR $address_invoice->deleted OR $address_delivery->deleted)
                                $this->errors[] = Tools::displayError('This address is invalid.');
                            else {
                                self::$cart->id_address_delivery = (int) (Tools::getValue('id_address_delivery'));
                                self::$cart->id_address_invoice = Tools::isSubmit('same') ? self::$cart->id_address_delivery : (int) (Tools::getValue('id_address_invoice'));
                                if (!self::$cart->update())
                                    $this->errors[] = Tools::displayError('An error occurred while updating your cart.');
                                if (!sizeof($this->errors)) {
                                    if (self::$cookie->id_customer) {
                                        $customer = new Customer((int) (self::$cookie->id_customer));
                                        $groups = $customer->getGroups();
                                    }
                                    else
                                        $groups = array(1);
                                    $result = self::_getCarrierList();



                                    // Wrapping fees
                                    $wrapping_fees = (float) (Configuration::get('PS_GIFT_WRAPPING_PRICE'));
                                    $wrapping_fees_tax = new Tax((int) (Configuration::get('PS_GIFT_WRAPPING_TAX')));
                                    $wrapping_fees_tax_inc = $wrapping_fees * (1 + (((float) ($wrapping_fees_tax->rate) / 100)));
                                    $result = array_merge($result, array(
                                        'allow_eu_vat_delivery' => $allow_eu_vat_delivery,
                                        'allow_eu_vat_invoice' => $allow_eu_vat_invoice,
                                        'summary' => self::$cart->getSummaryDetails(),
                                        'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
                                        'HOOK_PAYMENT' => self::_getPaymentMethods(),
                                        'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 0 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int) (self::$cookie->id_currency))))
                                            ));
                                    die(Tools::jsonEncode($result));
                                }
                            }
                            if (sizeof($this->errors))
                                die('{"hasError" : true, "errors" : ["' . implode('\',\'', $this->errors) . '"]}');
                            break;
                        default:
                            exit;
                    }
                }
                exit;
            }
        }
        elseif (Tools::isSubmit('ajax'))
            exit;
    }

    public function setMedia() {
        parent::setMedia();
 
        if (version_compare(_PS_VERSION_, '1.4.1') >= 0)
                $this->setOpcktPaths ();

	// Theme's styles
        Tools::addCSS(_THEME_CSS_DIR_.'order-opc.css');
//        Tools::addJS(_THEME_JS_DIR_.'order-opc.js');

        // Adding CSS style sheet - mostly empty, customization possible
        Tools::addCSS(_PS_OPCKT_BASE_URI_ . 'css/order-opc.css');
        
        // Theme's customized CSS (if exists)
        if (file_exists(dirname(__FILE__).'/css/'._THEME_NAME_.'.css')) 
            Tools::addCSS(_PS_OPCKT_BASE_URI_ . 'css/'._THEME_NAME_.'.css'); 
        
        // Adding JS files
        Tools::addJS(_PS_OPCKT_BASE_URI_ . 'js/order-opc.js');
        Tools::addJs(_PS_JS_DIR_ . 'jquery/jquery.scrollTo-1.4.2-min.js');
        //Tools::addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
        Tools::addJS(_PS_OPCKT_BASE_URI_ . 'js/statesManagement.js');
    }

    private function _assignOpcSettings() {
	if (file_exists(_PS_OPCKT_DIR_."onepagecheckout.php")) {
	  require_once(_PS_OPCKT_DIR_."onepagecheckout.php");
	  $opc_mod = new OnePageCheckout();
          $opc_config = $opc_mod->_getAllOptionsValues();
	  self::$smarty->assign("opc_config", $opc_config);
	}
    }

    private function _setInfoBlockContent() {
	if (file_exists(_PS_OPCKT_DIR_."info-block-content.tpl")) {
          $info_block_content =  self::$smarty->fetch(_PS_OPCKT_DIR_."info-block-content.tpl");
	} else {
	  $info_block_content = "";
	}
	  self::$smarty->assign("info_block_content", $info_block_content);
    }
    
    public function process() {
        // SHOPPING CART
        $this->_assignSummaryInformations();
        // WRAPPING AND TOS
        $this->_assignWrappingAndTOS();


        $selectedCountry = (int) (Configuration::get('PS_COUNTRY_DEFAULT'));
        $countries = Country::getCountries((int) (self::$cookie->id_lang), true);
        self::$smarty->assign(array(
            'isLogged' => $this->isLogged,
            'isGuest' => isset(self::$cookie->is_guest) ? self::$cookie->is_guest : 0,
            'countries' => $countries,
            'sl_country' => isset($selectedCountry) ? $selectedCountry : 0,
            'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
            'errorCarrier' => Tools::displayError('You must choose a carrier before', false),
            'errorTOS' => Tools::displayError('You must accept terms of service before', false),
            'isPaymentStep' => (bool) (isset($_GET['isPaymentStep']) AND $_GET['isPaymentStep'])
        ));
        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();
        self::$smarty->assign(array(
            'years' => $years,
            'months' => $months,
            'days' => $days,
        ));

        /* Load guest informations */
        //if ($this->isLogged AND self::$cookie->is_guest)
        if ($this->isLogged)
            self::$smarty->assign('guestInformations', $this->_getGuestInformations());

        // OPCKT default address update - in case customer is not yet logged-in and address is not 
        // yet entered and refresh happens
        if (self::$cart->id_address_delivery > 0) {
            $def_address = new Address(self::$cart->id_address_delivery);
            $def_country = $def_address->id_country;
            $def_state = $def_address->id_state;
        } else {
            $def_country = 0;
            $def_state = 0;
        }
        if (self::$cart->id_address_invoice > 0) {
            $def_address_invoice = new Address(self::$cart->id_address_invoice);
            $def_country_invoice = $def_address_invoice->id_country;
            $def_state_invoice = $def_address_invoice->id_state;
        } else {
            $def_country_invoice = 0;
            $def_state_invoice = 0;
        }

        if (self::$cart->id_address_delivery > 0 && self::$cart->id_address_invoice > 0 &&
                self::$cart->id_address_delivery != self::$cart->id_address_invoice)
            $def_different_billing = 1;
        else
            $def_different_billing = 0;

        self::$smarty->assign('def_different_billing', $def_different_billing);
        self::$smarty->assign('def_country', $def_country);
        self::$smarty->assign('def_state', $def_state);
        self::$smarty->assign('def_country_invoice', $def_country_invoice);
        self::$smarty->assign('def_state_invoice', $def_state_invoice);

        if ($this->isLogged)
            $this->_assignAddress(); // ADDRESS
            
// CARRIER
        $this->_assignCarrier();
        // PAYMENT
        $this->_assignPayment();
        Tools::safePostVars();
    }

    public function displayHeader() {
        if (Tools::getValue('ajax') != 'true')
            parent::displayHeader();
    }

    private function _displayField($name) {
        return isset(self::$smarty->tpl_vars["opc_config"]->value[$name]) && self::$smarty->tpl_vars["opc_config"]->value[$name];
    }
    
    /* Development of this concept discontinued, too many special cases in checkout form fields
    private function assignOpcFields()
    {
        // field, field display name, sample value, display?
        self::$smarty->assign("opc_fields", array(
            "email" => array("E-mail", "jack@gmail.com", 1, 1),
            "company" => array("The company", "my company, s.r.o", 1, $this->_displayField("company_delivery")),
        ));
    }*/
    
    public function displayContent() {
        parent::displayContent();

        // count opening divs versus closing ones in order-payment.tpl and add one 
        // more div tag in payment if necessary - to fix layout issues on some themes (e.g. matrice)
        $header_file_content = @file_get_contents(_PS_THEME_DIR_ . 'order-payment.tpl');
        $opening_divs = substr_count($header_file_content, "<div");
        $closing_divs = substr_count($header_file_content, "</div");
        self::$smarty->assign("add_extra_div", ($opening_divs < $closing_divs)?$closing_divs-$opening_divs:false);
        
       // $this->assignOpcFields();
        
	$this->_processAddressFormat();

        self::$smarty->assign("lastProductAdded", 0);
        self::$smarty->display(_PS_THEME_DIR_ . 'errors.tpl');
        self::$smarty->display(_PS_OPCKT_DIR_ . 'order-opc.tpl');
    }

    public function displayFooter() {
        if (Tools::getValue('ajax') != 'true')
            parent::displayFooter();
    }

    protected function _getGuestInformations() {
        $customer = new Customer((int) (self::$cookie->id_customer));
        $address_delivery = new Address((int) self::$cart->id_address_delivery);

        if ($customer->birthday)
            $birthday = explode('-', $customer->birthday);
        else
            $birthday = array('0', '0', '0');

        $ret = array(
            'use_another_invoice_address' => (bool) ((int) self::$cart->id_address_invoice != (int) self::$cart->id_address_delivery), # opc added
            'id_address_invoice' => (int) self::$cart->id_address_invoice, # opc added
            'id_customer' => (int) (self::$cookie->id_customer),
            'email' => Tools::htmlentitiesUTF8($customer->email),
            'customer_lastname' => Tools::htmlentitiesUTF8($customer->lastname),
            'customer_firstname' => Tools::htmlentitiesUTF8($customer->firstname),
            'newsletter' => (int) $customer->newsletter,
            'optin' => (int) $customer->optin,
            'id_address_delivery' => (int) self::$cart->id_address_delivery,
            'company' => Tools::htmlentitiesUTF8($address_delivery->company),
            'lastname' => Tools::htmlentitiesUTF8($address_delivery->lastname),
            'firstname' => Tools::htmlentitiesUTF8($address_delivery->firstname),
            'vat_number' => Tools::htmlentitiesUTF8($address_delivery->vat_number),
            'dni' => Tools::htmlentitiesUTF8($address_delivery->dni),
            'address1' => Tools::htmlentitiesUTF8($address_delivery->address1),
            'address2' => Tools::htmlentitiesUTF8($address_delivery->address2),
            'postcode' => Tools::htmlentitiesUTF8($address_delivery->postcode),
            'city' => Tools::htmlentitiesUTF8($address_delivery->city),
            'other' => Tools::htmlentitiesUTF8($address_delivery->other),
            'phone' => Tools::htmlentitiesUTF8($address_delivery->phone),
            'phone_mobile' => Tools::htmlentitiesUTF8($address_delivery->phone_mobile),
            'alias' => Tools::htmlentitiesUTF8($address_delivery->alias),
            'id_country' => (int) ($address_delivery->id_country),
            'id_state' => (int) ($address_delivery->id_state),
            'id_gender' => (int) $customer->id_gender,
            'sl_year' => $birthday[0],
            'sl_month' => $birthday[1],
            'sl_day' => $birthday[2]
        );

        if (((int) self::$cart->id_address_invoice != (int) self::$cart->id_address_delivery)) {
            $address_invoice = new Address((int) self::$cart->id_address_invoice);
            $customers_address = ((int) (self::$cookie->id_customer) == $address_invoice->id_customer) ? true : false;
            $invoice = array(
                'id_country_invoice' => (int) ($address_invoice->id_country),
                'id_state_invoice' => (int) ($address_invoice->id_state),
            );
            
             if (Configuration::get('VATNUMBER_MANAGEMENT') AND
                    file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') && 
                    VatNumber::isApplicable($address_invoice->id_country) && 
                    Configuration::get('VATNUMBER_COUNTRY') != $address_invoice->id_country)
                $allow_eu_vat = 1;
            else
                $allow_eu_vat = 0;
            
            // fill in customer's invoice address fields only when this address is "created"
            // otherwise, it's only estimation address with "temp" fields.
            if ($customers_address) {
                $addr = array(
                    'company_invoice' => Tools::htmlentitiesUTF8($address_invoice->company),
                    'lastname_invoice' => Tools::htmlentitiesUTF8($address_invoice->lastname),
                    'firstname_invoice' => Tools::htmlentitiesUTF8($address_invoice->firstname),
                    'vat_number_invoice' => Tools::htmlentitiesUTF8($address_invoice->vat_number),
                    'dni_invoice' => Tools::htmlentitiesUTF8($address_invoice->dni),
                    'address1_invoice' => Tools::htmlentitiesUTF8($address_invoice->address1),
                    'address2_invoice' => Tools::htmlentitiesUTF8($address_invoice->address2),
                    'postcode_invoice' => Tools::htmlentitiesUTF8($address_invoice->postcode),
                    'city_invoice' => Tools::htmlentitiesUTF8($address_invoice->city),
                    'other_invoice' => Tools::htmlentitiesUTF8($address_invoice->other),
                    'phone_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone),
                    'phone_mobile_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
                    'alias_invoice' => Tools::htmlentitiesUTF8($address_invoice->alias),
                    'allow_eu_vat_invoice' => $allow_eu_vat
                );
            } else {
                $addr = array(
                    
                );
            }

            $invoice = array_merge($invoice, $addr);
            $ret = array_merge($ret, $invoice);
        }

        return $ret;
    }

    protected function _assignCarrier() {
        $carriers = Carrier::getCarriersForOrder(Country::getIdZone((int) Configuration::get('PS_COUNTRY_DEFAULT')));
        if ($this->isLogged) {
            $address_delivery = new Address((int) (self::$cart->id_address_delivery));
            if (!Address::isCountryActiveById((int) (self::$cart->id_address_delivery)))
                unset($address_delivery);
            elseif (!Validate::isLoadedObject($address_delivery) OR $address_delivery->deleted)
                unset($address_delivery);
        }
        self::$smarty->assign(array(
            'checked' => $this->_setDefaultCarrierSelection($carriers),
            'carriers' => $carriers,
            'default_carrier' => (int) (Configuration::get('PS_CARRIER_DEFAULT')),
            'HOOK_EXTRACARRIER' => (isset($address_delivery) ? Module::hookExec('extraCarrier', array('address' => $address_delivery)) : NULL),
            'HOOK_BEFORECARRIER' => Module::hookExec('beforeCarrier', array('carriers' => $carriers))
        ));
    }

    protected function _assignPayment() {
        self::$smarty->assign(array(
            'HOOK_TOP_PAYMENT' => ($this->isLogged ? Module::hookExec('paymentTop') : ''),
            'HOOK_PAYMENT' => self::_getPaymentMethods()
        ));
    }

    private $payment_mod_id = 0;
    private function _genPaymentModId($matches) {
        return $matches[1].' id="opc_pid_'.$this->payment_mod_id++.'"'.$matches[2];
    }
    
    protected function _getPaymentMethods()
    {
//		if (!$this->isLogged)
//			return '<p class="warning">'.Tools::displayError('Please sign in to see payment methods').'</p>';


        if (self::$cart->OrderExists()) {
            $ret = '<p class="warning">' . Tools::displayError('Error: this order is already validated') . '</p>';
            return array("orig_hook" => $ret, "parsed_content" => $ret);
        }
//		if (!self::$cart->id_customer OR !Customer::customerIdExistsStatic(self::$cart->id_customer) OR Customer::isBanned(self::$cart->id_customer))
//			return '<p class="warning">'.Tools::displayError('Error: no customer').'</p>';

        
        /*TODO: zmazat, uz viac nepotrebujeme! vyber adresy sa uz na zaciatku pri prvej navsteve checkout formulara potvrdi a tym sa ulozia ID-cka.
        $opc_config = self::$smarty->tpl_vars["opc_config"]->value;

        $tmp_address_id_1 = (isset($opc_config) && isset($opc_config["delivery_address_id"])) ? intval($opc_config["delivery_address_id"]) : 0;

        $parse_payment_methods = (isset($opc_config) && isset($opc_config["payment_radio_buttons"])) ? true : false;


        
         
          * 
          *        $reset_id_address = false;

        # Simulate address for payment methods selection
       
        * 
        *  if (!self::$cart->id_address_delivery) {
            // try to get actual data from Address form, stored in OPCKT table by cart id
            // if nothing relevant is there, just simulate some address 
            // TODO: get actual correct ID instead of 3
            $simulated_delivery_address_id = ($tmp_address_id_1 > 0) ? $tmp_address_id_1 : 3;
           
            self::$cart->id_address_delivery = $simulated_delivery_address_id;
            self::$cart->id_address_invoice = $simulated_delivery_address_id;
            $reset_id_address = true;
        }*/

        $ret = "";
        $address_delivery = new Address(self::$cart->id_address_delivery);
        $address_invoice = (self::$cart->id_address_delivery == self::$cart->id_address_invoice ? $address_delivery : new Address(self::$cart->id_address_invoice));
        if (!self::$cart->id_address_delivery OR !self::$cart->id_address_invoice OR !Validate::isLoadedObject($address_delivery) OR !Validate::isLoadedObject($address_invoice) OR $address_invoice->deleted OR $address_delivery->deleted)
            $ret = '<p class="warning">' . Tools::displayError('Error: please choose an address') . '</p>';
        elseif (!self::$cart->id_carrier AND !self::$cart->isVirtualCart())
            $ret = '<p class="warning">' . Tools::displayError('Error: please choose a carrier') . '</p>';
        elseif (self::$cart->id_carrier != 0) {
            $carrier = new Carrier((int) (self::$cart->id_carrier));
            if (!Validate::isLoadedObject($carrier) OR $carrier->deleted OR !$carrier->active)
                $ret = '<p class="warning">' . Tools::displayError('Error: the carrier is invalid') . '</p>';
        }
        if (!self::$cart->id_currency)
            $ret .= '<p class="warning">' . Tools::displayError('Error: no currency has been selected') . '</p>';
        /*elseif (!self::$cookie->checkedTOS AND Configuration::get('PS_CONDITIONS'))
            $ret = '<p class="warning">' . Tools::displayError('Please accept Terms of Service') . '</p>';*/

        /* If some products have disappear */
        elseif (!self::$cart->checkQuantities())
            $ret .= '<p class="warning">' . Tools::displayError('An item in your cart is no longer available, you cannot proceed with your order.') . '</p>';

        /* Check minimal amount */
        $currency = Currency::getCurrency((int) self::$cart->id_currency);

        $orderTotal = self::$cart->getOrderTotal(false);
        $minimalPurchase = Tools::convertPrice((float) Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        if ($orderTotal < $minimalPurchase)
            $ret .= '<p class="warning">' . Tools::displayError('A minimum purchase total of') . ' ' . Tools::displayPrice($minimalPurchase, $currency) .
                    ' ' . Tools::displayError('is required in order to validate your order.') . '</p>';

 
        if (trim($ret) != "") {
 /* TODO: moze sa zmazat.
           if ($reset_id_address) {
                self::$cart->id_address_delivery = 0;
                self::$cart->id_address_invoice = 0;
            }
 */
            return array("orig_hook" => $ret, "parsed_content" => $ret);
        }



        $opc_config = self::$smarty->tpl_vars["opc_config"]->value;
        $tmp_customer_id_1 = (isset($opc_config) && isset($opc_config["payment_customer_id"])) ? intval($opc_config["payment_customer_id"]) : 0;
        
        $reset_id_customer = false;

        if (!self::$cookie->id_customer) {
            // if no customer set yet, use OPCKT default customer - created during installation
            $simulated_customer_id = ($tmp_customer_id_1 > 0) ? $tmp_customer_id_1 : Customer::getFirstCustomerId();
            self::$cookie->id_customer = $simulated_customer_id;
            $reset_id_customer = true;

	    if (!self::$cart->id_customer) {
		self::$cart->id_customer = $simulated_customer_id;
	    }
        }

        /* Bypass payment step if total is 0 */
        if (self::$cart->getOrderTotal() <= 0) {
	  $return = self::$smarty->fetch('free-order-payment.tpl');
	} else {

	  $ship2pay_support = (isset($opc_config) && isset($opc_config["ship2pay"]) && $opc_config["ship2pay"] == "1") ? true : false;
	  if ($ship2pay_support) {
	    $tmp_id_carrier = (self::$cart->id_carrier > 0)?self::$cart->id_carrier:Configuration::get('PS_CARRIER_DEFAULT');
            $return = $this->_hookExecPaymentShip2pay($tmp_id_carrier);
	  }
          else
	    $return = Module::hookExecPayment();
	}


        # restore cookie's id_customer
        if ($reset_id_customer)
            self::$cookie->id_customer = 0;
      /* zmazat
       *   if ($reset_id_address) {
       
            self::$cart->id_address_delivery = 0;
            self::$cart->id_address_invoice = 0;
        }*/

        # fix Moneybookers relative path to images
        $return = preg_replace('/src="modules\//', 'src="' . __PS_BASE_URI__ . 'modules/', $return);
        
        # OPCKT fix Paypal relative path to redirect script
        $return = preg_replace('/href="modules\//', 'href="' . __PS_BASE_URI__ . 'modules/', $return);

        if (!$return) {
            $ret = '<p class="warning">' . Tools::displayError('No payment method is available') . '</p>';
            return array("orig_hook" => $ret, "parsed_content" => $ret);
        }
        
       
        
        // if radio buttons as payment turned on, parse payment methods, generate radio buttons and
        // hide original buttons; add large green checkout / submit button and also ensure on onepage.js
        // that after clicking that button appropriate payment button is pressed.
        $parsed_content = "";
        $parse_payment_methods = (isset($opc_config) && isset($opc_config["payment_radio_buttons"])
                && $opc_config["payment_radio_buttons"] == "1") ? true : false;
        if ($parse_payment_methods) {

            $content = $return;

            $payment_methods = array();
            $i = 0;
            // regular payment modules
            preg_match_all('/<a.*?>.*?<img.*?src="(.*?)".*?\/?>(.*?)<\/a>/ms', $content, $matches1, PREG_SET_ORDER);
            // moneybookers
            preg_match_all('/<input .*?type="image".*?src="(.*?)".*?>.*?<span.*?>(.*?)<\/span>/ms', $content, $matches2, PREG_SET_ORDER);
            $matches = array_merge($matches1, $matches2);
            foreach ($matches as $match) {
                $payment_methods[$i]['img'] = preg_replace('/(\r)?\n/m', " ", trim($match[1]));
                $payment_methods[$i]['desc'] = preg_replace('/\s/m', " ", trim($match[2])); // fixed for Auriga payment
                $payment_methods[$i]['link'] = "opc_pid_$i";

                $i++;
            }
            
            // Mark original payment modules with special id
            $tmp_return = preg_replace_callback('/(<a)(.*?>.*?<img.*?src)/ms', array($this, "_genPaymentModId"), $return);
	    if ($tmp_return != null)
	      $return = $tmp_return;
            $tmp_return = preg_replace_callback('/(<input.*?type="image")(.*?<span.)/ms', array($this, "_genPaymentModId"), $return);
	    if ($tmp_return != null)
	      $return = $tmp_return;

            self::$smarty->assign("payment_methods", $payment_methods);
            $parsed_content = self::$smarty->fetch("payment-methods.tpl");
	    $parsed_content = str_replace("&amp;", "&", $parsed_content);
        }


        return array("orig_hook" => $return, "parsed_content" => $parsed_content);
    }

    protected function _getCarrierList() {
        $address_delivery = new Address(self::$cart->id_address_delivery);
        if (self::$cookie->id_customer) {
            $customer = new Customer((int) (self::$cookie->id_customer));
            $groups = $customer->getGroups();
        }
        else
            $groups = array(1);
        if (!Address::isCountryActiveById((int) (self::$cart->id_address_delivery)))
            $this->errors[] = Tools::displayError('This address is not in a valid area.');
        elseif (!Validate::isLoadedObject($address_delivery) OR $address_delivery->deleted)
            $this->errors[] = Tools::displayError('This address is invalid.');
        else {
            $carriers = Carrier::getCarriersForOrder((int) Address::getZoneById((int) ($address_delivery->id)), $groups);
            $result = array(
                'checked' => $this->_setDefaultCarrierSelection($carriers),
                'carriers' => $carriers,
                'HOOK_BEFORECARRIER' => Module::hookExec('beforeCarrier', array('carriers' => $carriers)),
                'HOOK_EXTRACARRIER' => Module::hookExec('extraCarrier', array('address' => $address_delivery))
            );
            return $result;
        }
        if (sizeof($this->errors))
            return array(
                'hasError' => true,
                'errors' => $this->errors
            );
    }

    protected function _setDefaultCarrierSelection($carriers) {
        if (sizeof($carriers)) {
            $defaultCarrierIsPresent = false;
            if (self::$cart->id_carrier != 0)
                foreach ($carriers AS $carrier)
                    if ($carrier['id_carrier'] == self::$cart->id_carrier) {
                        $defaultCarrierIsPresent = true;
                        self::$cart->id_carrier = $carrier['id_carrier'];
                    }
            if (!$defaultCarrierIsPresent)
                foreach ($carriers AS $carrier)
                    if ($carrier['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT')) {
                        $defaultCarrierIsPresent = true;
                        self::$cart->id_carrier = $carrier['id_carrier'];
                    }
            if (!$defaultCarrierIsPresent)
                self::$cart->id_carrier = $carriers[0]['id_carrier'];
        }
        else
            self::$cart->id_carrier = 0;
        if (self::$cart->update())
            return self::$cart->id_carrier;
        return 0;
    }

	// update in PS 1.4.1 and again in 1.4.2.5
	protected function _processAddressFormat()
	{
	   if (version_compare(_PS_VERSION_, '1.4.1') >= 0) {

		$selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));

		$address_delivery = new Address((int)self::$cart->id_address_delivery);
		$address_invoice = new Address((int)self::$cart->id_address_invoice);

		$inv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_delivery->id_country);
		$dlv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_invoice->id_country);

		$inv_all_fields = array();
		$dlv_all_fields = array();

		foreach (array('inv','dlv') as $adr_type)
		{
			foreach (${$adr_type.'_adr_fields'} as $fields_line)
				foreach(explode(' ',$fields_line) as $field_item)
					${$adr_type.'_all_fields'}[] = trim($field_item);

			self::$smarty->assign($adr_type.'_adr_fields', ${$adr_type.'_adr_fields'});
			self::$smarty->assign($adr_type.'_all_fields', ${$adr_type.'_all_fields'});

		}
	   }// if(version_compare())
	}//_processAddressFormat()


    private function _hookExecPaymentShip2pay($carrier) {
    // ship2pay function, get only active for current shiping payment modules 
		global $cart, $cookie;
		$sql='SELECT * FROM `'._DB_PREFIX_.'shiptopay`';
		$result = Db::getInstance()->ExecuteS($sql);
        
		if(count($result)==0){
            		return Module::hookExecPayment();
		}else{
    		$hookArgs = array('cookie' => $cookie, 'cart' => $cart);
    		$billing = new Address(intval($cart->id_address_invoice));
    		$output = '';
    		$sql='
    		SELECT distinct(stp.id_carrier),h.`id_hook`, m.`name`, hm.`position`
    		FROM `'._DB_PREFIX_.'module_country` mc
    		LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
    		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
    		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
    		LEFT JOIN `'._DB_PREFIX_.'shiptopay` stp ON hm.`id_module` = stp.`id_payment`
    		WHERE h.`name` = \'payment\'
    		AND stp.id_carrier='.intval($carrier).'
    		AND mc.id_country = '.intval($billing->id_country).'
    		AND m.`active` = 1
    		ORDER BY hm.`position`, m.`name` DESC';
    		$result = Db::getInstance()->ExecuteS($sql);
    		if ($result)
    			foreach ($result AS $k => $module)
    				if (($moduleInstance = Module::getInstanceByName($module['name'])) AND is_callable(array($moduleInstance, 'hookpayment')))
    					if (!$moduleInstance->currencies OR ($moduleInstance->currencies AND sizeof(Currency::checkPaymentCurrencies($moduleInstance->id))))
    						$output .= call_user_func(array($moduleInstance, 'hookpayment'), $hookArgs);
    		return $output;
	}
    }//_hookExecPaymentShip2pay()

}

