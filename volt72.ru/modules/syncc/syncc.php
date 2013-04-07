<?php

class syncc extends Module
	{
	public function __construct(){
		$version_p = explode('.', _PS_VERSION_, 3);
		$version_t = $version_p[0]>0 && $version_p[1]>3;
		$this->name = 'syncc';
		$this->tab = $version_t ? 'others' : 'others';
		if ($version_t)
			$this->author = 'Krox';
		$this->version = '0.1.0';
		parent::__construct();
		$this->displayName = $this->l('Module Synhronis');
		$this->description = $this->l('Import from 1C');
		
		$config = Configuration::getMultiple(array('SYNCC_LOGIN', 'SYNCC_PASSWORD'));
		if (isset($config['SYNCC_LOGIN']))
            $this->syncc_login = $config['SYNCC_LOGIN'];
		if (isset($config['SYNCC_PASSWORD']))
			$this->syncc_password = $config['SYNCC_PASSWORD'];
		if (!isset($this->syncc_login))
            $this->warning = $this->l('Необходимо установить логин и пароль для модуля');
	}
	public function install(){
		parent::install();
		if ($this->registerHook('AdminStatsModules'))
			return false;
		return true;
	} 
	public function uninstall(){
		
		return true;
	}
	
	private function _postValidation()
	{
		if (isset($_POST['btnSubmit']))
		{
			if (empty($_POST['syncc_login']))
				$this->_postErrors[] = $this->l('Login is required');
			elseif (empty($_POST['syncc_password']))
				$this->_postErrors[] = $this->l('Password is required');
		}
	}
	
	private function _postProcess(){
		if (isset($_POST['btnSubmit'])){
			Configuration::updateValue('SYNCC_LOGIN', $_POST['syncc_login']);
			Configuration::updateValue('SYNCC_PASSWORD', $_POST['syncc_password']);
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('OK').'" /> '.$this->l('Settings updated').'</div>';
	}
	
	private function _displayH()
	{
		$this->_html .= '<img src="../modules/syncc/logo.gif" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you export goods from 1C.').'</b><br /><br />';
	}
	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('sync details').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please specify required data').'.<br /><br /></td></tr>
					<tr><td colspan="2">'.$this->l('sync scrypt: ').$_SERVER['HTTP_HOST'].'/modules/syncc/<br /></td></tr>
					<tr><td width="140" style="height: 35px;">'.$this->l('Syncc Login').'</td><td><input type="text" name="syncc_login" value="'.htmlentities(Tools::getValue('syncc_login', $this->syncc_login), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td width="140" style="height: 35px;">'.$this->l('Syncc Password').'</td><td><input type="text" name="syncc_password" value="'.htmlentities(Tools::getValue('syncc_password', $this->syncc_password), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent(){
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		if (!empty($_POST))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
				$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
		$this->_html .= '<br />';
		
		$this->_displayH();
		$this->_displayForm();
		return $this->_html;
	}
}
?>