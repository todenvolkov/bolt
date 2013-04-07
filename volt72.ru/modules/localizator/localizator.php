<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class localizator extends Module
{

	private $_html = '';
	private $_postErrors = array();
	
	public function __construct()
	{
		$this->name = 'localizator';
		$this->tab = 'i18n_localization';
		$this->version = '2.0';
		$this->author = 'PrestaLab.Ru';
		$this->need_instance = '0';


		parent::__construct();

		$this->displayName = $this->l('Локализатор');
		$this->description = $this->l('Добавляет локальные настройки');
		
	}

	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('backOfficeHeader'))
			return false;
		return true;
	}
	
	public function lng($string, $id_lang=1, $table)
	{
		global $_MODULES, $_MODULE;

		$file = _PS_MODULE_DIR_.$this->name.'/'.Language::getIsoById($id_lang).'.php';
		if (Tools::file_exists_cache($file) AND include_once($file))
			$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

		if (!is_array($_MODULES))
			return (str_replace('"', '&quot;', $string));

		$string2 = str_replace('\'', '\\\'', $string);
		$currentKey = '<{'.$this->name.'}'._THEME_NAME_.'>'.$table.'_'.md5($string2);
		$defaultKey = '<{'.$this->name.'}prestashop>'.$table.'_'.md5($string2);
		if (key_exists($currentKey, $_MODULES))
			$ret = stripslashes($_MODULES[$currentKey]);
		elseif (key_exists($defaultKey, $_MODULES))
			$ret = stripslashes($_MODULES[$defaultKey]);
		else
			$ret = $string;
		return str_replace('"', '&quot;', $ret);
	}

 
	protected function _ExecuteSQL($file, $id_lng=0)
	{		
		if (!file_exists(dirname(__FILE__).'/'.$file))
			return false;
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/'.$file))
			return false;
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'ID_LNG'), array(_DB_PREFIX_, _MYSQL_ENGINE_, $id_lng), $sql);		
		$sql = preg_split("/;\s*[\r\n]+/", trim($sql));

		foreach ($sql as $query)
      Db::getInstance()->Execute(trim($query));
	}
	
	protected function _locTransGen($table, $fields=array('name'))
	{
      $f=implode(',',$fields);
		if($strs=Db::getInstance()->ExecuteS('SELECT '.$f.' FROM `'._DB_PREFIX_.''.$table.'` WHERE `id_lang`=1'))
	{
      $fh=fopen(dirname(__FILE__).'/'.$table.'.tpl', 'w');
      foreach ($strs as $str)
        foreach ($fields as $field)
		if($str[$field])
          		fwrite($fh,'{l s=\''.$str[$field].'\' mod=\'localizator\'}');
      fclose($fh);
	}
	}
	
	protected function _locTransLate($table, $id_lng, $fields=array('name'))
	{		
		  $strs=Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.''.$table.'` WHERE `id_lang`=1');
      Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.''.$table.'` WHERE `id_lang`='.$id_lng);
      foreach ($strs as $str){
        $str['id_lang']=$id_lng;
          foreach ($fields as $field)
		if($str[$field])
            		$str[$field]=$this->lng($str[$field], $id_lng, $table);
        Db::getInstance()->autoExecute(_DB_PREFIX_.$table, $str, 'INSERT');
      }
	}
	
	protected function _postProcess()
	{
    require_once(dirname(__FILE__).'/fields.php');
		if (Tools::isSubmit('submitRegional'))
		{
      $id_lng=(int)Tools::GetValue('id_lng');
      $lng=Language::getIsoById($id_lng);
      //Очистка штатов
	self::_ExecuteSQL($lng.'_units.sql');
      if (Tools::GetValue('drop_states'))
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'state`');
      if (Tools::GetValue('import_states')){
        //Импорт регионов
        self::_ExecuteSQL($lng.'_states.sql');
        //Включение регионов у страны
        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'country` SET `contains_states`=1 WHERE `iso_code`=\''.$lng.'\'');
      }
      //Очистка стран
      if (Tools::GetValue('drop_countries')){
        $id_country=Country::getByIso($lng);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'country` WHERE `id_country`<>'.$id_country);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'country_lang` WHERE `id_country`<>'.$id_country);
      }
      //Очистка налогов
      if (Tools::GetValue('drop_taxes')){
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_lang`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_rule`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_rules_group`');
      }
      //Добавление налогов
      if (Tools::GetValue('import_taxes')){
        self::_ExecuteSQL($lng.'_taxes.sql', $id_lng);
      }
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Региональные настройки применены').'</div>';
		}
		
		elseif (Tools::isSubmit('submitPrepare'))
		{
      //Очистка продуктов, категорий, комбинаций, свойств, тегов, картинок, сцен
      if (Tools::GetValue('drop_products')){
        self::_ExecuteSQL('drop_products.sql');
        $langs=Language::getLanguages();
        foreach ($langs as $lang)
          Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'category_lang` (`id_category`, `id_lang`, `name`, `description`, `link_rewrite`, `meta_title`, `meta_keywords`, `meta_description`) VALUES (1, '.$lang['id_lang'].', \'Home\', \'\', \'home\', NULL, NULL, NULL)');
				//Категории
				foreach (scandir(_PS_CAT_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+\-(.*)\.jpg$/', $d) OR preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d))
						unlink(_PS_CAT_IMG_DIR_.$d);
				//Продукты
				foreach (scandir(_PS_PROD_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+\-[0-9]+\-(.*)\.jpg$/', $d)
								OR preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d)
								OR preg_match('/^[0-9]+\-[0-9]+\.jpg$/', $d))
					{
						unlink(_PS_PROD_IMG_DIR_.$d);
					}
      }
      //Очистка заказов, клиентов, корзин, гостей, сообщений
      if (Tools::GetValue('drop_orders')){
        self::_ExecuteSQL('drop_orders.sql');
      }
      //Очистка поставщиков, производителей
      if (Tools::GetValue('drop_mansup')){
        self::_ExecuteSQL('drop_mansup.sql');
				foreach (scandir(_PS_MANU_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+\-(.*)\.jpg$/', $d) OR preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d))
						unlink(_PS_MANU_IMG_DIR_.$d);
				foreach (scandir(_PS_SUPP_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+\-(.*)\.jpg$/', $d) OR preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d))
						unlink(_PS_SUPP_IMG_DIR_.$d);
      }
      //Очистка соединений, статистики, поискового индекса, магазинов
      if (Tools::GetValue('drop_other')){
        self::_ExecuteSQL('drop_other.sql');
      }
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Очистка завершена').'</div>';
		}
		elseif (Tools::isSubmit('submitTransGen'))
		{
      foreach($locarray as $table=>$fields)
        self::_locTransGen($table,$fields);
      
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Файлы для перевода созданы').'</div>';
		}
		elseif (Tools::isSubmit('submitTransLate'))
		{
      $id_lng=(int)Tools::GetValue('id_lng');
      foreach($locarray as $table=>$fields)
        self::_locTransLate($table,$id_lng,$fields);
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Перевод завершен').'</div>';
		}
	}
	
	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_postProcess();
		return $this->_html.$this->_displayForm();
	}
	
	private function _displayForm()
	{
    global $smarty;
    $smarty->assign('action', $_SERVER['REQUEST_URI']);
    $smarty->assign('langs', Language::getLanguages());
		return $smarty->fetch(dirname(__FILE__).'/localizator.tpl');
	}


	public function hookbackOfficeHeader($params)
	{
    return '<script type="text/javascript" src="http://'.Tools::getHttpHost(false, true)._MODULE_DIR_.'localizator/admin.js"></script>';
	}
}


