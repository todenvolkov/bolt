<?php

class bankform extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public  $compname;
	public  $schet;
	public $inn;
	public $kpp;
	public $bankname;
	public $korschet;
	public $bik;

	public function __construct()
	{
		$this->name = 'bankform';
		$this->tab = 'others';
		$this->version = '0.1';
		

		$config = Configuration::getMultiple(array('BANK_FORM_compname', 'BANK_FORM_schet', 'BANK_FORM_inn', 'BANK_FORM_kpp', 'BANK_FORM_bankname', 'BANK_FORM_korschet', 'BANK_FORM_bik'));
			$this->compname = $config['BANK_FORM_compname'];
			$this->schet = $config['BANK_FORM_schet'];
			$this->inn = $config['BANK_FORM_inn'];
			$this->kpp = $config['BANK_FORM_kpp'];
			$this->bankname = $config['BANK_FORM_bankname'];
			$this->korschet = $config['BANK_FORM_korschet'];
			$this->bik = $config['BANK_FORM_bik'];

		parent::__construct();

		$this->displayName = $this->l(' Банковская квитанция');
		$this->description = $this->l('Печать банковской квитанции');
	}

	public function install()
	{
		if (!parent::install())
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('BANK_FORM_compname')
				OR !Configuration::deleteByName('BANK_FORM_schet')
				OR !Configuration::deleteByName('BANK_FORM_inn')
				OR !Configuration::deleteByName('BANK_FORM_kpp')
				OR !Configuration::deleteByName('BANK_FORM_bankname')
				OR !Configuration::deleteByName('BANK_FORM_korschet')
				OR !Configuration::deleteByName('BANK_FORM_bik')
				OR !parent::uninstall())
			return false;
		return true;
	}

	private function _postProcess()
	{
		if (isset($_POST['btnSubmit']))
		{
			Configuration::updateValue('BANK_FORM_compname', $_POST['compname']);
			Configuration::updateValue('BANK_FORM_schet', $_POST['schet']);
			Configuration::updateValue('BANK_FORM_inn', $_POST['inn']);
			Configuration::updateValue('BANK_FORM_kpp', $_POST['kpp']);
			Configuration::updateValue('BANK_FORM_bankname', $_POST['bankname']);
			Configuration::updateValue('BANK_FORM_korschet', $_POST['korschet']);
			Configuration::updateValue('BANK_FORM_bik', $_POST['bik']);
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Настройки обновлены').'</div>';
	}


	private function _displayForm()
	{
		$this->_html .=
		'
		<form style="float:right; width:200px; margin:15px; text-align:center;">
			<fieldset>
				<a href="http://prestalab.ru/"><img src="http://prestalab.ru/upload/banner.png" alt="Модули и шаблоны для PrestaShop" width="174px" height="100px" /></a>
			</fieldset>
		</form>
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Банковские реквизиты').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Введите реквизиты получателя платежа').'.<br /><br /></td></tr>
					<tr><td width="200" style="height: 35px;">Получатель платежа: </td><td><input type="text" name="compname" value="'.htmlentities(Tools::getValue('compname', $this->compname), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">Счет: </td><td><input type="text" name="schet" value="'.htmlentities(Tools::getValue('schet', $this->schet), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">ИНН: </td><td><input type="text" name="inn" value="'.htmlentities(Tools::getValue('inn', $this->inn), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">КПП: </td><td><input type="text" name="kpp" value="'.htmlentities(Tools::getValue('kpp', $this->kpp), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">Наименование банка: </td><td><input type="text" name="bankname" value="'.htmlentities(Tools::getValue('bankname', $this->bankname), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">Кор. счет: </td><td><input type="text" name="korschet" value="'.htmlentities(Tools::getValue('korschet', $this->korschet), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">БИК: </td><td><input type="text" name="bik" value="'.htmlentities(Tools::getValue('bik', $this->bik), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>

					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Обновить').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			$this->_postProcess();
		}
		else
			$this->_html .= '<br />';

		$this->_displayForm();

		return $this->_html;
	}
}
