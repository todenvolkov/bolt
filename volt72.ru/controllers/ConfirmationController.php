<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14006 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ConfirmationControllerCore extends FrontController
{
	
	public function __construct()
	{
		$this->php_self = Configuration::get('PS_HOMEPAGE_PHP_SELF');
	
		parent::__construct();
	}
	
	public function process()
	{
		parent::process();
		
		
	    if ( isset($_GET['id']) )
        {
		    $customer = new Customer(intval($_GET['id']));
		    $customer->status = 1;
			//$customer->deleted = 0;
			$customer->update();
    		
			//Tools::redirect('/order?step=1');
			self::$smarty->display(_PS_THEME_DIR_.'confirmation2.tpl');
		}
		

	}
	
	public function displayContent()
	{
		parent::displayContent();
		
		if (! isset($_GET['id']) )
        {
		self::$smarty->assign('confirmation', 1);
		self::$smarty->display(_PS_THEME_DIR_.'confirmation.tpl');
		}
	}
}