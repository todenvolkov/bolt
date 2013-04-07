<?php


class Customer extends CustomerCore {
        /**
          * Check if e-mail is already registered in database - OPCKT updated version
          *
          * @param string $email e-mail
          * @param $return_id boolean
          * @param $ignoreGuest boolean, for exclure guest customer
          * @return Customer ID if found, false otherwise
          */
        static public function customerExists($email, $return_id = false, $ignoreGuest = true)
        {
		// verification keys: VK##1
                if (!Validate::isEmail($email))
                        die (Tools::displayError());

		// TODO: configuration variable v OPCKT (nieco ako $allow_guest_checkout_with_registered_email)
		if (Tools::isSubmit('submitAccount')) {
		  return false;
		} else {
		  return parent::customerExists($email, $return_id, $ignoreGuest);
		}

        }
        
                /**
         *
         * @param string $id_customer
         * @param boolean $active
         * @return array 
         */
        static public function getLastTwoCustomerAddressIds($id_customer, $active = true)
        {
            if ($id_customer == 0)
                return 0;
            
                $query = '
                        SELECT `id_address`
                        FROM `'._DB_PREFIX_.'address`
                        WHERE `id_customer` = '.(int)($id_customer).' AND `deleted` = 0'.($active ? ' AND `active` = 1' : '').
                        ' ORDER BY id_address DESC limit 2';
            
                $result = Db::getInstance()->ExecuteS($query);
                $ret = array();
                foreach ($result AS $k => $address)
                {
                    $ret[] = ($address["id_address"]);
                }
                
                return $ret;
        }
        
                        /**
         *
         * @param string $id_customer
         * @param boolean $active
         * @return array 
         */
        static public function getFirstCustomerId($active = true)
        {
                $query = '
                        SELECT `id_customer`
                        FROM `'._DB_PREFIX_.'customer`
                        WHERE `deleted` = 0'.($active ? ' AND `active` = 1' : '').
                        ' ORDER BY id_customer ASC';
            
                $x = Db::getInstance()->getValue($query);
                return $x;
        }


}//Customer

?>
