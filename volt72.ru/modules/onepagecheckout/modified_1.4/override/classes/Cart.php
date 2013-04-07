<?php


class Cart extends CartCore {
        
        // reset discount cache so that discount can be added and new results retrieved in single HTTP request
        public function resetCartDiscountCache() {
           self::$_discounts = NULL; 
           self::$_discountsLite = NULL;
        }

}//Cart

?>
