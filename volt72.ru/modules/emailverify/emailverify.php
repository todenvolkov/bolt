<?php

// Todo : Add a time limit to activation
// Todo : Add an email domain blacklist
// Todo : Add a "email me again" function
// Todo : Manual activation of pending users
// Todo : alternative activation mode (copy/paste code)
// Todo : Add a warning if the module is not in the last position (because of its redirection)
// Todo : obfuscate the customer id in the confirmation link

class EmailVerify extends Module
{
	public function __construct()
	{
		$this->name = 'emailverify';
		$this->tab = 'Tools';
		$this->version = '1.0';
		

        parent::__construct();

        $this->displayName = $this->l('Customer email verification');
        $this->description = $this->l('Send a confirmation email to the new registered customers before activating them');
		$this->confirmUninstall = $this->l('Pending verifications will be lost');
	}
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('createAccount') AND $this->installDb());
	}
	
	private function installDb()
	{
		return Db::getInstance()->Execute('
		ALTER TABLE `'._DB_PREFIX_.'customer`
		ADD `activated` TINYINT(1) NOT NULL DEFAULT 1 AFTER `active`');
	}
	
	private function uninstallDb()
	{
		return Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'customer` DROP `activated`');
	}

	public function uninstall()
	{
		return (parent::uninstall() AND $this->uninstallDb());
	}
	
	public function hookCreateAccount($params)
	{
		global $cookie;
		
		if (Tools::getValue('submitGuestAccount')){
			return true;
		}
		$customer = $params['newCustomer'];
		$link = 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/activate.php?id_customer='.intval($customer->id).'&token='.Tools::encrypt($customer->email);
		if (Mail::Send(intval($cookie->id_lang), 'emailverify', $this->l('Email confirmation'),  array('{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{email}' => $customer->email, '{link}' => $link), $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, NULL, NULL, dirname(__FILE__).'/mails/'))
			Tools::redirect('modules/'.$this->name.'/notify.php');
	}
	
	public function activate()
	{
		global $smarty;
		
		$errors = array();
		if (!($id_customer = intval(Tools::getValue('id_customer'))) OR !($customer = new Customer($id_customer)))
			$errors[] = $this->l('Customer not found');
		elseif (!($token = Tools::getValue('token')) OR $token != Tools::encrypt($customer->email))
			$errors[] = $this->l('Token is not valid');
		else // Keep activated = 0 in where clause, in case a banned user try to activate again
			if (!Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'customer`
			SET activated = 1, active = 1
			WHERE id_customer = '.intval($customer->id).'
			AND activated = 0 LIMIT 1'))
				$errors[] = $this->l('Activation impossible');

		include(dirname(__FILE__).'/../../header.php');
		$smarty->assign('errors', $errors);
		$smarty->display(dirname(__FILE__).'/activate.tpl');
		include(dirname(__FILE__).'/../../footer.php');
	}
	
	public function notify()
	{
		global $smarty, $cookie;
		
		include(dirname(__FILE__).'/../../header.php');
		$smarty->display(dirname(__FILE__).'/notify.tpl');
		include(dirname(__FILE__).'/../../footer.php');
		Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customer` SET activated = 0, active = 0 WHERE id_customer = '.intval($cookie->id_customer).' LIMIT 1');
	}
}

?>