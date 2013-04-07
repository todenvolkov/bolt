<?php

class OpcktHelper {

    public function __construct() {
        
    }
/*
    public function getAddressIdByCartId($cart_id) {

        $result = Db::getInstance()->getRow('
		SELECT `id_address`
		FROM `' . _DB_PREFIX_ . 'opckt_cart_address`
		WHERE `id_cart` = ' . intval($cart_id));
        if (is_array($result) && count($result)) {
            return $result['id_address'];
        } else {
            return 0;
        }
    }

    public function addAddressIdAndCartId($address_id, $cart_id) {

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'opckt_cart_address` 
                (`id_cart`, `id_address`) values (' . $cart_id . ', ' . $address_id . ')';
        Db::getInstance()->Execute($query);
    }
 * */
 

}

?>
