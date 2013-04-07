<?php
           
class Csync extends Module
{
	private $_html = '';

	function __construct()
	{
		$this->name = 'Csync';
		$this->tab = 'Tools';
		$this->version = '1.0.6';

		parent::__construct();

		$this->displayName = 'Синхронизация с 1С Управление Торговлей УТ11';
		$this->description = 'Синхронизация товаров, цен и заказов с 1С 8.х';
                        $price_id = Db::getInstance()->getValue("SELECT `price` FROM `"._DB_PREFIX_."cuser`");
                        if (empty($price_id)) $this->warning = 'Не установлен GUID набора цен "Розница"';
			$this->confirmUninstall = 'Удаление модуля в дальнейшем сделает невозможным дифференциальную синхронизацию без проведения полной синхронизации, ';
	}
    
     
		  
		   	
	public function install()
	{       
                global $cookie;
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'category_group` DROP INDEX `category_group_index` , ADD UNIQUE `category_group_index` ( `id_category` , `id_group` )');
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'category_product` DROP INDEX `category_product_index` , ADD UNIQUE `category_product_index` ( `id_category` , `id_product` );');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'category` ADD `xml` VARCHAR( 36 ) NOT NULL');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'image` ADD `xml` VARCHAR( 36 ) NOT NULL');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `xml` VARCHAR( 36 ) NOT NULL');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'feature` ADD `xml` VARCHAR( 36 ) NOT NULL');
                Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_lang`');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'feature_value` ADD `xml` VARCHAR( 36 ) NOT NULL');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'feature_value_lang` ADD `xml` VARCHAR( 36 ) NOT NULL');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'manufacturer` ADD `xml` VARCHAR( 36 ) NOT NULL');
		

		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product_attribute` ADD `xml` VARCHAR( 36 ) NOT NULL');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'manufacturer` ADD UNIQUE  `UNIQUE` (  `name` )');
		Db::getInstance()->Execute('CREATE TABLE `'._DB_PREFIX_.'cuser` (login varchar(32),pass varchar(32), fix varchar(8) , gen varchar(8), ves varchar(8),home varchar(8),test varchar(8))');	
				
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'cuser` ADD `price` VARCHAR( 36 ) NOT NULL');
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'cuser` ADD `price1` VARCHAR( 36 ) NOT NULL');
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'cuser` ADD `price2` VARCHAR( 36 ) NOT NULL');
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'cuser` ADD `price3` VARCHAR( 36 ) NOT NULL');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'specific_price` ADD `xml` VARCHAR( 36 ) NOT NULL');
		
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'group`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'group_lang`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price`');
		function addPRICEgroup ($i, $name){
                Db::getInstance()->Execute ("
				INSERT INTO `"._DB_PREFIX_."group_lang` (`id_group`,`id_lang`,`name`)
				VALUES ('".$i."', ".Configuration::get('PS_LANG_DEFAULT').", '".$name."' )
			    ");
			
				Db::getInstance()->Execute ("
				INSERT INTO `"._DB_PREFIX_."group` (`price_display_method`,`date_add`,`date_upd`)
				VALUES (0, NOW(), NOW() )
			    ");	  

        }
            addPRICEgroup (1, 'По-умолчанию');
            addPRICEgroup (2, 'Опт1');
	    addPRICEgroup (3, 'Опт2');
	    addPRICEgroup (4, 'Опт3');
		Db::getInstance()->Execute ("INSERT INTO `"._DB_PREFIX_."cuser` (`home`,`ves`) VALUES (1,1)");   
		parent::install();
		return true;
	}

	public function uninstall()
	{
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'category`  DROP `xml`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product`  DROP `xml`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'image`  DROP `xml`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'feature`  DROP `xml`');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'feature_value`  DROP `xml`');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'feature_value_lang`  DROP `xml`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'manufacturer` DROP INDEX `UNIQUE`');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'manufacturer`  DROP `xml`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product_attribute`  DROP `xml`');
		//Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'cuser`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_lang`');
                Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_value_lang`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'group`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'group_lang`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price`');
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'specific_price`  DROP `xml`');
		
		if (!Configuration::deleteByName('_PRICE_ID_') OR !parent::uninstall())
			return false;
		return true;
	}
	
	
	private function _postProcess()
	{
		return 'Пока недоступно';
	}

	private function _displayForm()
	{   
	    
	 
		$this->_html .=
			'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset class="space">
					<legend><img src="../img/admin/warning.gif" alt="" class="middle" />Дополнительные опции</legend>
					
					
					<p class="clear">1.Загрузить каталог номенклатуры из магазина в 1С</p>
					<input name="submitExport" class="button" type="submit" value="Выгрузить товары в 1С" />
					
					
					
					<p class="clear">2.Удалить  товары из каталога номенклатуры в магазине</p>
					<input name="submitClear" class="button" type="submit" value="Удалить товары" />
					
					
					<p class="clear">3.Удалить заказы товара из магазина</p>
					<input name="submitClearZakaz" class="button" type="submit" value="Удалить заказы" />
					
					<p class="clear">4.Очистить папку <strong>Upload</strong></p>
					<input name="submitClearUpload" class="button" type="submit" value="Очистить Upload" onclick="if(confirm(\'Продолжить?\'))submit();else return false;"/>
					
					
					
					
				</fieldset>
			</form>';
	}
	private function _displayabout()
	{  
	
	$this->_html .='<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset class="space">
	<legend><img src="../img/admin/email.gif" /> ' . $this->l('Информация') . '</legend>
	<div id="dev_div">
		<span><b>' . $this->l('Версия') . ':</b> ' . $this->version . '</span><br>
		<span><b>' . $this->l('Разработчик') . ':</b> <a class="link" href="mailto:A_Dovbenko@mail.ru" target="_blank">SAVVATO  </a>
		<span><b>' . $this->l('Исходный код') . ':</b> <a class="link" href="mailto:admin@prestalab.ru" target="_blank">ORS</a><br>
        <span><b>' . $this->l('Описание') . ':</b> <a class="link" href="http://elcommerce.com.ua" target="_blank">ElCommerce.com.ua</a><br><br>
		<p style="text-align:center"><a href="http://elcommerce.com.ua/"><img src="http://elcommerce.com.ua/img/m/logo.png" alt="Электронный учет коммерческой деятельности" /></a>
		<a href="http://prestalab.ru/"><img src="http://prestalab.ru/upload/banner.png" alt="Модули и шаблоны для PrestaShop" /></a>
		
	</div>
</fieldset>
	';
	}
	private function _displayHelp()
	{
                        global $ves;
						global $home;
						$cdoble = Db::getInstance()->getValue("SELECT `login` FROM `"._DB_PREFIX_."cuser`");
                        $cpass = Db::getInstance()->getValue("SELECT `pass` FROM `"._DB_PREFIX_."cuser`");
                        $price_id = Db::getInstance()->getValue("SELECT `price` FROM `"._DB_PREFIX_."cuser`");
                        $price_id_1 = Db::getInstance()->getValue("SELECT `price1` FROM `"._DB_PREFIX_."cuser`");
                        $price_id_2 = Db::getInstance()->getValue("SELECT `price2` FROM `"._DB_PREFIX_."cuser`");
                        $price_id_3 = Db::getInstance()->getValue("SELECT `price3` FROM `"._DB_PREFIX_."cuser`");
						$home = Db::getInstance()->getValue("SELECT `home` FROM `"._DB_PREFIX_."cuser`");
						$ves = Db::getInstance()->getValue("SELECT `ves` FROM `"._DB_PREFIX_."cuser`");
						switch ($home){						
						case 1: $home1 = 'checked'; $home2 = ''; $home3 = '';
						break;
						case 2: $home1 = ''; $home2 = 'checked'; $home3 = '';
						break;
						case 3: $home1 = ''; $home2 = ''; $home3 = 'checked';
						break;
						}
						if ($ves == 1) {$ves1 = "checked"; $ves2 = "";}
						if ($ves == 2) {$ves1 = ""; $ves2 = "checked";}
		$this->_html .='
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			
				<fieldset class="space">
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />Настройки</legend>
					<label>Логин</label>
					<div class="margin-form">
						<input type="text" name="1c_login" value="'.Tools::getValue('1c_login',$cdoble).'"/>
						<p class="clear">Веедите логин (Лантинскими буквами, цифрами, исключая спецсимволы)</p>
					</div>
					<label>Пароль</label>
					<div class="margin-form">
						<input type="password" name="1c_pass"  value="'.Tools::getValue('1c_pass',$cpass).'"/>
						<p class="clear">Введите пароль (Лантинскими буквами, цифрами, исключая спецсимволы)</p>
						
					</div>

                    <label>Порт</label>
					<div class="margin-form">
						<input type="text" name="port"  value="'.Tools::getValue('port',Configuration::get('_PORT_')).'"/>
						<p class="clear">Введите порт на котором работает <strong>Apache</strong></p>
						
					</div>

					<label>GUID набора цен "Розница"</label>
					<div class="margin-form">
						<input type="text" name="price_id" value="'.Tools::getValue('price_id', $price_id).'" />
						<p class="clear">Например 3800b1b1-c381-11dd-aa6f-993de20ae8cf</p>
					</div>
        <label>GUID набора цен "Опт1"</label>


					<div class="margin-form">
						<input type="text" name="price_id_1" value="'.Tools::getValue('price_id_1', $price_id_1).'" />
						<p class="clear">Например 3800b1b1-c381-11dd-aa6f-993de20ae8cf</p>
					</div>
<label>GUID набора цен "Опт2"</label>



					<div class="margin-form">
						<input type="text" name="price_id_2" value="'.Tools::getValue('price_id_2', $price_id_2).'" />
						<p class="clear">Например 3800b1b1-c381-11dd-aa6f-993de20ae8cf</p>
					</div>
<label>GUID набора цен "Опт3"</label>
					<div class="margin-form">
						<input type="text" name="price_id_3" value="'.Tools::getValue('price_id_3', $price_id_3).'" />
						<p class="clear">Например 3800b1b1-c381-11dd-aa6f-993de20ae8cf</p>
					</div>

					<label>id языка</label>
					<div class="margin-form">
						<input type="text" name="id_lang" value="'.Tools::getValue('id_lang', Configuration::get('_ID_LANG_')).'" />
						<p class="clear">Веедите id языка </p>
					</div>

				<label>Режим обмена с 1С</label>
				<div class="margin-form">
					<input type="checkbox" name="fix" value="1" ' . (Tools::getValue('fix', Configuration::get('_FIX_'))? 'checked="checked" ' : '' ) . ' />
          <p class="clear"> Использовать <strong>zip</strong> сжатие для обмена данными с 1С</p>
</div>
                                <label>Генерирование изображений</label>
<div class="margin-form">
					<input type="checkbox" name="gen" value="1" ' . (Tools::getValue('gen', Configuration::get('_GEN_'))? 'checked="checked" ' : '' ) . ' />
          <p class="clear"> Выберите "да" для автоматического генерирования изображений при выгрузке, выберите "нет" для перегенерирования изображений вручную через админ-панель</p>

				</div>
				
				
				<label>Сортировка категорий</label>
		<div class="margin-form">
		<input type="radio" name="1home" value="1"  '.$home1.' />'.$this->l('Как в 1С').' &nbsp;&nbsp;
		<input type="radio" name="1home" value="2"  '.$home2.'/> '.$this->l('Как в 1С + Home').' &nbsp;&nbsp; 
		<input type="radio" name="1home" value="3"  '.$home3.'/> '.$this->l('Только Home').'&nbsp;&nbsp
		</div>
		
		<label>Единица измерения веса</label>
		<div class="margin-form">
		<input type="radio" name="1ves" value="1"  ' .$ves1.' />'.$this->l('грамм').' &nbsp;&nbsp;
		<input type="radio" name="1ves" value="2"  ' .$ves2.'/> '.$this->l('килограмм').' 
		
		</div>
				
					<center><input type="submit" name="submitGUID" value="Обновить" class="button" /></center>
				</fieldset>
				
			</form>
			<fieldset class="space"><legend><img src="../img/admin/unknown.gif" alt="" class="middle" />Справка</legend><ol>
			<h3>Настройки 1С</h3>
			<li>Указать путь к скрипту <strong>http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').':'.Configuration::get('_PORT_').'/modules/Csync/connect.php</strong></li>
			<li>Ввести логин и пароль, указанный Вами выше в настройках </li>
			<li>Указать порт на котором работает веб-сервер <strong>Apache</strong>, обычно: <strong>80</strong>. Если <strong>Apache</strong> работает в связке с  <strong>Ngnix</strong>, уточнить у провайдера хост услуг.</li>
			<li>Для выгрузки производетеля создайте в свойствах номенклатуры 1С свойство с названием <strong>"Производитель"</strong></li>
			<li>Для обмена данными с 1С отменить выбор тестового режима в разделе <strong>"ТЕСТИРОВАНИЕ"</strong>, для импорта каталога монеклатуры и пакета предложений из файлов обмена- включить.</li>
			<li>Выполнить полную синхронизацию (режим обмена может быть как с использованием zip сжатия так и без)</li>
			<li>Настроить 1С на выполнение дифференциальной синхронизации <b style="color: red;">(<strong>Внимание:</strong>  использовать только режим обмена с zip сжатием!!!) </b></li>
			</ol><br />
			<h3>Особенности синхронизации</h3>
			<ul>
			<li>При полной синхронизации выполняется очистка категорий, продуктов, папки с картинками</li>
			<li>При дифференциальной синхронизации производится обновление/добавление названий категорий, обновление/добавление названий товаров, картинок, изменяется родитель, rewrite_rule остается прежним</li>
			<li>При любой синхронизации проводится обновление цен и количества товара</li>
			<li>При перемещении товара между категориями в 1С в магазине из старой категории товар удален не будет (пока)</li>
			<li>Выгрузка производителя реализована через свойства номенклатуры 1С (особенность формата обмена CommerceML )</li>
			</ul></fieldset>
			<fieldset class="space"><legend><img src="../img/admin/warning.gif" alt="" class="middle" />ТЕСТИРОВАНИЕ</legend><ol>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			
			  <label>Использовать тестовый режим</label>
				     <div class="margin-form">
					<input type="checkbox" name="test" value="1" ' . (Tools::getValue('test', Configuration::get('_TEST_'))? 'checked="checked" ' : '' ) . ' />
                    <p class="clear">Выбрать для загрузки каталога в базу интернет -магазина из файлов обмена , без обмена данными с 1С</p>
                    </div>
			<li>Проверка добавления товаров: <a href="http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').':'.Configuration::get('_PORT_').'/modules/Csync/connect.php?type=catalog&mode=import&filename=import.xml"><strong>test</strong></a></li>
			<li>Проверка экспорта заказов: <a href="http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').':'.Configuration::get('_PORT_').'/modules/Csync/connect.php?type=sale&mode=query"><strong>test</strong></a></li>
			<center><input type="submit" name="submitTEST" value="Обновить" class="button" /></center>
			</form>
			</fieldset>
			';
	}
    
	function getContent()
	{   
	    
		$this->_html .= '<h2>Синхронизация с 1С Управление Торговлей 11</h2>';
		if (Tools::isSubmit('submitExport'))
		{
			$this->_html .= '<div class="alert error">'.$this->_postProcess().'</div>';
		}
		
		if (Tools::isSubmit('submitClear'))
		{
	Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'category` WHERE id_category <> 1');
    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'category_lang` WHERE id_category <> 1');
    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'category_group` WHERE id_category <> 1');
    //Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'category_shop` WHERE id_category != 1');
    Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'category` AUTO_INCREMENT = 2 ');
    
    /*foreach (scandir(_PS_CAT_IMG_DIR_) as $d)
        if (preg_match('/^[0-9]+\-(.*)\.jpg$/', $d))
            unlink(_PS_CAT_IMG_DIR_ . $d);*/
    
    
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'category_product');
    
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_lang');
    
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer');
    //Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_shop');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_lang');
    
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_lang');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_product');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_value');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_value_lang');
    //Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_shop');
	Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price');
    
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_lang');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image');
    //Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_shop');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_tag');
    
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_lang');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_lang');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute');
    Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_impact');
    foreach (scandir(_PS_PROD_IMG_DIR_) as $d)
        if (preg_match('/^[0-9]+\-[0-9]+\-(.*)\.jpg$/', $d))
            unlink(_PS_PROD_IMG_DIR_ . $d);
	foreach (scandir(_PS_TMP_IMG_) as $d)
        if (preg_match('/^[0-9]+\-[0-9]+\-(.*)\.jpg$/', $d))
            unlink(_PS_TMP_IMG_ . $d);		
		
		
			$this->_html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				Каталог номенклатуры очищен
			</div>';
		}
		
		if (Tools::isSubmit('submitClearZakaz'))
		{
		       

				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'orders');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'order_detail');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'order_discount');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'order_history');
				
		
		
			$this->_html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				Заказы удалены из магазина
			</div>';
		}
		if (Tools::isSubmit('submitTEST'))
		{
		       
            $test = ((isset($_POST['test']))&&($_POST['test'] == '1'))? 1 : 0;
			Configuration::updateValue('_TEST_', $test);
			
			 Db::getInstance()->Execute("
			     UPDATE `"._DB_PREFIX_."cuser`
			     SET `test`='".$_POST['test']."'
			          ");	
		
		
			$this->_html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				Режим импорта изменен
			</div>';
		}
if (Tools::isSubmit('submitClearUpload'))
		{
	       

function recRMDir($path){ 
if (substr($path, strlen($path)-1, 1) != '/') $path .= '/'; 
if ($handle = @opendir($path)){ 
while ($obj = readdir($handle)){ 
if ($obj != '.' && $obj != '..'){ 
if (is_dir($path.$obj)){ 
if (!recRMDir($path.$obj)) return false; 
}elseif (is_file($path.$obj)){ 
if (!unlink($path.$obj)) return false; 
} 
} 
} 
closedir($handle); 
if (!@rmdir($path)) return false; 
return true; 
} 
return false; 
} 
recRMDir(_PS_PROD_PIC_DIR_);
	
		
			$this->_html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				Папка <strong>Upload</strong> очищена
			</div>';
		}
			
		if (Tools::isSubmit('submitGUID') )
		{
            $fix = ((isset($_POST['fix']))&&($_POST['fix'] == '1'))? 1 : 0;
            Configuration::updateValue('_FIX_', $fix);
			$gen = ((isset($_POST['gen']))&&($_POST['gen'] == '1'))? 1 : 0;
			Configuration::updateValue('_GEN_', $gen);
			
			
			
			
			
			//$home = ((isset($_POST['home']))&&($_POST['home'] == '1'))? 1 : 0;
			//Configuration::updateValue('_HOME_', $home);
			if ($idlang = Tools::getValue('id_lang')){
			Configuration::updateValue('_ID_LANG_', $idlang);}
			if ($port = Tools::getValue('port')){
			Configuration::updateValue('_PORT_', $port);}
			
			Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'cuser`');
						
			Db::getInstance()->Execute ("
				INSERT INTO `"._DB_PREFIX_."cuser` (`login`,`pass`,`fix`,`gen`,`home`,`ves`)
				VALUES ('".$_POST['1c_login']."', '".$_POST['1c_pass']."', '".$fix."', '".$gen."', '".$_POST['1home']."', '".$_POST['1ves']."' )
			    ");
			         
            Db::getInstance()->Execute("
			     UPDATE `"._DB_PREFIX_."cuser`
			     SET `price`='".$_POST['price_id']."', `price1`='".$_POST['price_id_1']."', `price2`='".$_POST['price_id_2']."', `price3`='".$_POST['price_id_3']."'
			          ");
			      
			$this->_html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				Настройки обновлены
			</div>';
		}
		$cdoble = Db::getInstance()->getValue("SELECT `login` FROM `"._DB_PREFIX_."cuser`");
	
        $cpass = Db::getInstance()->getValue("SELECT `pass` FROM `"._DB_PREFIX_."cuser`");
	
	    if ((empty ($cdoble)) or (empty ($cpass))) {
			
			$this->_html .= '<div class="alert error">
			<img src="../img/admin/error2.png" alt="" title="" />
			Не указан логин или пароль для синхронизации с 1С!!!</div>';
			
			}
		$this->_displayHelp();
		$this->_displayForm();
		$this->_displayabout();
		return $this->_html;
	}
}


?>
