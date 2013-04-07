<?php
class ParentOrderController extends ParentOrderControllerCore {

        private $opc_enabled = false;

        public function init() {
		global $cookie;
                $opc_mod_script = _PS_MODULE_DIR_.'onepagecheckout/onepagecheckout.php';

                // check whether OPC mod is installed and active
                if (file_exists($opc_mod_script)) {
                  require_once($opc_mod_script);
                  $opc_mod = new OnePageCheckout();
                  // verification keys: VK##1
                  $this->opc_enabled = $opc_mod->active;
		  if ($this->opc_enabled) {
		    // override default php_self because of cannonical redirect would result in endless loop
                    $this->php_self = 'modules/onepagecheckout/order-opc.php';
		  }
                }
                parent::init();
        }


        public function preProcess()
        {
                global $isVirtualCart;

		if ($this->opc_enabled) {
		  if (strpos($_SERVER['PHP_SELF'], 'onepagecheckout/order-opc.php') === false)
                    Tools::redirect('modules/onepagecheckout/order-opc.php');
		}
		else {
		  parent::preProcess();
		}
		# TODO: Add .htaccess rewrite for nicer URL

	}//preProcess()

	
	// overriden, so that carrier selection works also for non logged customers
	protected function _processCarrier()
	{
		self::$cart->recyclable = (int)(Tools::getValue('recyclable'));
		self::$cart->gift = (int)(Tools::getValue('gift'));
		if ((int)(Tools::getValue('gift')))
		{
			if (!Validate::isMessage($_POST['gift_message']))
				$this->errors[] = Tools::displayError('Invalid gift message');
			else
				self::$cart->gift_message = strip_tags($_POST['gift_message']);
		}
		
		// OPCKT - change here
		if (isset(self::$cart) AND (int)(self::$cart->id_address_delivery) > 0)
		{
			$address = new Address((int)(self::$cart->id_address_delivery));
			if (!($id_zone = Address::getZoneById($address->id)))
				$this->errors[] = Tools::displayError('No zone match with your address');
		}
		else
			$id_zone = Country::getIdZone((int)Configuration::get('PS_COUNTRY_DEFAULT'));
			
                $x = Validate::isInt(Tools::getValue('id_carrier'));
                $x1 = Carrier::checkCarrierZone((int)(Tools::getValue('id_carrier')), (int)($id_zone));
                
		if (Validate::isInt(Tools::getValue('id_carrier')) AND sizeof(Carrier::checkCarrierZone((int)(Tools::getValue('id_carrier')), (int)($id_zone))))
			self::$cart->id_carrier = (int)(Tools::getValue('id_carrier'));
		elseif (!self::$cart->isVirtualCart() AND (int)(Tools::getValue('id_carrier')) == 0)
			$this->errors[] = Tools::displayError('Invalid carrier or no carrier selected');
		
		Module::hookExec('processCarrier', array('cart' => self::$cart));
		
		return self::$cart->update();
	}//_processCarrier()
}
