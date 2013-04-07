<?php

//require_once(dirname(__FILE__) . '/../../modules/onepagecheckout/OpcktHelper.php');
//set_include_path(dirname(__FILE__) . '/../../classes/');

if(Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(dirname(__FILE__).'/../../modules/vatnumber/vatnumber.php'))
	include_once(dirname(__FILE__).'/../../modules/vatnumber/vatnumber.php');


class AddressController extends AddressControllerCore {
    # modified for OPCKT module

    public function init() {

	// verification keys: VK##1
        if (Tools::isSubmit('partialSubmitAddress'))
            $this->auth = 0;
        parent::init();
    }

    private function saveAddress($cart_id_address, $id_country, $id_state) {

        $dummy = "dummyvalue";
        if ($cart_id_address > 0) { // update existing one
            /* @var $tmp_addr AddressCore */
            $tmp_addr = new Address($cart_id_address);
            $tmp_addr->deleted = 0;
        } else { // create a new address 
            $tmp_addr = new Address();
            $tmp_addr->alias = $dummy;
            $tmp_addr->lastname = $dummy;
            $tmp_addr->firstname = $dummy;
            $tmp_addr->address1 = $dummy;
            //$tmp_addr->postcode = "00000";
            $tmp_addr->city = $dummy;
        }

        if (trim($id_country) != "") {
            $tmp_addr->id_country = $id_country;
            if (trim($id_state) != "")
                $tmp_addr->id_state = $id_state;
            else
                $tmp_addr->id_state = 0;
            
            // Reset VAT number when address is non-EU (otherwise, taxes won't be added when VAT address changes to non-VAT!)
            if (Configuration::get('VATNUMBER_MANAGEMENT') AND
                    file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') && 
                    !VatNumber::isApplicable($id_country))
                $tmp_addr->vat_number = "";

            if ($cart_id_address > 0) {
                $tmp_addr->update();
            } else {
                $tmp_addr->add();
                // $opckt_helper->addAddressIdAndCartId($tmp_addr->id, self::$cookie->id_cart);
            }
            return $tmp_addr->id;
        } else
            return 0;
    }

//saveAddress()

    public function preProcess() {
	// handle case when already used address (assigned to an order) is being changed - we need to create new one and save it's reference to order
	if (Tools::isSubmit('submitAddress') && Tools::isSubmit('ajax'))
	{
		if (Tools::isSubmit('type'))
                {
                        if (Tools::getValue('type') == 'delivery')
                                $id_address = isset(self::$cart->id_address_delivery) ? (int)self::$cart->id_address_delivery : 0;
                        elseif (Tools::getValue('type') == 'invoice')
                                $id_address = (isset(self::$cart->id_address_invoice) AND self::$cart->id_address_invoice != self::$cart->id_address_delivery) ? (int)self::$cart->id_address_invoice : 0;
                        else
                                exit;
                }
                else
                        $id_address = (int)Tools::getValue('id_address', 0);

	        $address_old = new Address((int)$id_address);
	        if (isset($id_address) && (int)$id_address > 0 &&  Validate::isLoadedObject($address_old) AND Customer::customerHasAddress((int)self::$cookie->id_customer, (int)$address_old->id))
	        {
			if ($address_old->isUsed()) {
				// save as new and assing reference to cart
				$address_1 = new Address();
                        	$this->errors = $address_1->validateControler();
                        	$address_1->id_customer = (int)(self::$cookie->id_customer);

				if ((!Tools::getValue('phone') AND !Tools::getValue('phone_mobile')) ||
                         	    (!$country = new Country((int)$address_1->id_country) OR !Validate::isLoadedObject($country)) ||
				    ($country->isNeedDni() AND (!Tools::getValue('dni') OR !Validate::isDniLite(Tools::getValue('dni'))))||
				    ((int)($country->contains_states) AND !(int)($address_1->id_state)))
			  	{ /* empty */}		
				elseif ($result = $address_1->save())
                                {
					$id_address = $address_1->id;
					if ( Tools::getValue('type') == 'delivery' )
                                        {
                                                if (self::$cart->id_address_delivery == self::$cart->id_address_invoice)
						  self::$cart->id_address_invoice = (int)($address_1->id);
                                                self::$cart->id_address_delivery = (int)($address_1->id);
                                                self::$cart->update();
                                        }
					if ( Tools::getValue('type') == 'invoice' )
                                        {
                                                self::$cart->id_address_invoice = (int)($address_1->id);
                                                self::$cart->update();
                                        }
				}
			}//if ($address_old->isUsed)
		}//if (Validate::isLoaded...)

			parent::preProcess(); // call pre-process anyway, we only wanted to store this new address

	} elseif (Tools::isSubmit('partialSubmitAddress')) {// called separately for delivery country/state change and invoice country/state change
            // self::$cookie->id_cart by mohol byt kluc ku mazaniu adresy pri vytvoreni skutocneho accountu
            // not-null DB fields: id_address, id_country, alias, lastname, firstname, address1, city
             
            $is_separate_invoice_address = Tools::getValue('invoice_address');           
            // $type is 'delivery' or 'invoice'
            $type = Tools::getValue('type');           
            
            // Delivery address
            $id_country = Tools::getValue('id_country');
            $id_state = Tools::getValue('id_state');

            $id_address_delivery = 0;
            $id_address_invoice = 0;
            $create_different_address = 0;
            
            $last_addr_id = 0;
            $last_addr_ids_tmp = Customer::getLastTwoCustomerAddressIds(self::$cart->id_customer);
            
            if ($id_country !== false && $id_state !== false) {
                if ($is_separate_invoice_address) {
                    if (self::$cart->id_address_delivery == self::$cart->id_address_invoice)
                        $create_different_address = 1;    
                    
                    // check whether we have some recently used addresses (excluded actual delivery address)
                    if (isset($last_addr_ids_tmp) && $last_addr_ids_tmp != false && is_array($last_addr_ids_tmp) && count($last_addr_ids_tmp) > 0) {
                        foreach ($last_addr_ids_tmp as $item) {
                            if ($item != self::$cart->id_address_delivery) {
                                $last_addr_id = $item;
                                break;
                            }
                        }
                    }//if (isset($last_addr_ids_tmp)...
                }//if  ($is_separate_invoice_address)
                        
                if ($type == 'delivery')
                    $id_address_delivery = $this->saveAddress(($create_different_address)?0:self::$cart->id_address_delivery, $id_country, $id_state);
                else
                    $id_address_invoice = ($last_addr_id>0 && $create_different_address)?$last_addr_id:$this->saveAddress(($create_different_address)?$last_addr_id:self::$cart->id_address_invoice, $id_country, $id_state);
            }
                
            if ($id_address_delivery > 0) {
                self::$cart->id_address_delivery = $id_address_delivery;
                if ($is_separate_invoice_address == 0) {
                    self::$cart->id_address_invoice = self::$cart->id_address_delivery;
                }
            } elseif ($id_address_invoice > 0)
                self::$cart->id_address_invoice = $id_address_invoice;

            self::$cart->update();

            if (Configuration::get('VATNUMBER_MANAGEMENT') AND
                    file_exists(dirname(__FILE__) . '/../../modules/vatnumber/vatnumber.php') && 
                    VatNumber::isApplicable($id_country) && 
                    Configuration::get('VATNUMBER_COUNTRY') != $id_country)
                $allow_eu_vat = 1;
            else
                $allow_eu_vat = 0;
            
            if (Tools::isSubmit('ajax')) {
                $return = array(
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'id_address_delivery' => self::$cart->id_address_delivery,
                    'id_address_invoice' => self::$cart->id_address_invoice,
                    'allow_eu_vat' => $allow_eu_vat
                );
                die(Tools::jsonEncode($return));
            }
            // TODO: vymazat tuto adresu neskor - pri vytvoreni accountu
        } else {
            # assign pre-guessed address to this customer
            if (Tools::getValue('type') == 'invoice' && 
                    (isset(self::$cart->id_address_invoice) AND self::$cart->id_address_invoice != self::$cart->id_address_delivery) &&
                    isset(self::$cookie->id_customer) AND (int)self::$cookie->id_customer > 0)
            {
		$address_a = new Address(self::$cart->id_address_invoice);
                $address_a->id_customer = (int)self::$cookie->id_customer;
                $address_a->save();
            }
            # then call original preProcess to make standard validations and save to DB 
            parent::preProcess();
        }
    }

//preProcess()
}
