<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class HomeFeaturez extends Module
{
	
 	private $conf = array(
		'HOME_FEATURED_NBR' => 10,
		'HOME_FEATURED_CATALOG' => 1,
		'HOME_FEATURED_RANDOM' => 1,
		'HOME_FEATURED_TITLE' => 1,
		'HOME_FEATURED_DESCR' => 1,
		'HOME_FEATURED_VIEW' => 1,
 		'HOME_FEATURED_CART' => 1,
		'HOME_FEATURED_PRICE' => 1,
 	);	

	function __construct()
	{
		$this->name = 'homefeaturez';
		$this->version = '2.0.1';
		$this->tab = 'front_office_features';
		$this->author = 'zapalm';
		$this->need_instance = 0;		

		parent::__construct();
		$this->displayName = $this->l('Featured Products on the homepage');
		$this->description = $this->l('Displays Featured Products in the middle of your homepage. In this version you will found two new options: you can turn on/off the showing random products and select the category, witch products will show on the homepage.');
	}

	function install()
	{
	 	foreach($this->conf as $c => $v)
	 		Configuration::updateValue($c, $v);
		@file_get_contents('http://modulez.ru/support.php?new='.$this->name.'-'.$this->version.'&h='.$_SERVER['SERVER_NAME']);
		return parent::install() && $this->registerHook('home');
	}
	
	public function uninstall()
	{
	 	foreach($this->conf as $c => $v)
	 		Configuration::deleteByName($c);
		
	 	return parent::uninstall();
	}	

	public function getContent()
	{
		global $cookie;
		
		$iso_code = Language::getIsoById($cookie->id_lang);
		
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submit_save')) {
			$res = 1;
		 	foreach($this->conf as $c => $v) {
		 		$res &= Configuration::updateValue($c, intval(Tools::getValue($c)));
		 	}
		 	$output .= $res ? $this->displayConfirmation($this->l('Settings updated')) : $this->displayError($this->l('Some setting not updated'));
		}
		elseif (Tools::isSubmit('submit_translation')) {
			$dir = dirname(__FILE__);
			$file = $this->name.'-'.$this->version.'-'.$iso_code.'.txt';
			$addr = 'http://'.$_SERVER['SERVER_NAME']._MODULE_DIR_.$this->name.'/'.$file;
			@copy($dir.'/'.$iso_code.'.php', $dir.'/'.$file);
			$res = false;
			if (file_exists($dir.'/'.$file)) {
				$res = @file_get_contents('http://modulez.ru/support.php?translation='.$addr);
				@unlink($dir.'/'.$file);
			}
			if ($res) {
				$output .= $res==1 ? $this->displayConfirmation($this->l('Translation file was sent. Thank you.')) : $this->displayConfirmation($this->l('Module translation to your language already exists. Thank you.'));
			}
			else {
				$output .= $this->displayError($this->l('Unsuccessfull.'));
			}
		}
		elseif (Tools::isSubmit('submit_subscribe')) {
			$res = @file_get_contents('http://modulez.ru/support.php?subscribe='.Tools::getValue('email').'&h='.$_SERVER['SERVER_NAME']);
			if ($res) {
				$output .= $res==1 ? $this->displayConfirmation($this->l('You are subscribed to news.')) : $this->displayConfirmation($this->l('You are unsubscribed from news.'));
			}
			else {
				$output .= $this->displayError($this->l('Unsuccessfull.'));
			}
		}		
		
		$cols = Configuration::getMultiple(array_keys($this->conf));
	
		$output .= '		
			<fieldset style="width: 800px">
				<legend><img src="'._PS_ADMIN_IMG_.'cog.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<label>'.$this->l('Number of product displayed').'</label>
					<div class="margin-form">
						<input type="text" size="5" name="HOME_FEATURED_NBR" value="'.($cols['HOME_FEATURED_NBR'] ? $cols['HOME_FEATURED_NBR'] : '10').'" />
						<p class="clear">'.$this->l('The number of products displayed on homepage (default: 10).').'</p>					
					</div>
					<label>'.$this->l('ID category').'</label>
					<div class="margin-form">
						<input type="text" name="HOME_FEATURED_CATALOG" value="'.($cols['HOME_FEATURED_CATALOG'] ? $cols['HOME_FEATURED_CATALOG'] : '1').'"/>
						<p class="clear">'.$this->l('Enter the ID category of products, which will show on the homepage (default : 1 - Home category).').'</p>
					</div>				
					<label>'.$this->l('Show products randomly').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_RANDOM" value="1" '.($cols['HOME_FEATURED_RANDOM'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you whant to show products randomly.').'</p>
					</div>								
					<label>'.$this->l('Show title of a product').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_TITLE" value="1" '.($cols['HOME_FEATURED_TITLE'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you whant to show a product title.').'</p>
					</div>	
					<label>'.$this->l('Show description of a product').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_DESCR" value="1" '.($cols['HOME_FEATURED_DESCR'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you whant to show a product description.').'</p>
					</div>	
					<label>'.$this->l('Show a "View" button').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_VIEW" value="1" '.($cols['HOME_FEATURED_VIEW'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you whant to show a "View" button.').'</p>
					</div>		
					<label>'.$this->l('Show a "Add to cart" button').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_CART" value="1" '.($cols['HOME_FEATURED_CART'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you whant to show a "Add to cart" button. If prestashop catalog mode is enable than the button will not display.').'</p>
					</div>
					<label>'.$this->l('Show product price').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_PRICE" value="1" '.($cols['HOME_FEATURED_PRICE'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you whant to show product price. If prestashop catalog mode is enable than a price will not display.').'</p>
					</div>					
					<center><input type="submit" name="submit_save" value="'.$this->l('Save').'" class="button" /></center>
				</form>
			</fieldset>
			<br class="clear">
		';
		
		$output .= '
				<fieldset style="width: 400px;">
					<legend><img src="../img/admin/manufacturers.gif" /> '.$this->l('Module info').'</legend>
					<div id="dev_div">
						<span><b>'.$this->l('Version').':</b> '.$this->version.'</span><br/>
						<span><b>'.$this->l('License').':</b> <a class="link" href="'.__PS_BASE_URI__.'modules/'.$this->name.'/'.'license.html'.'" target="_blank">'.$this->l('free and open').'</a></span><br/>
						<span><b>'.$this->l('Forums').':</b> <a class="link" href="http://www.prestashop.com/forums/viewthread/66896/" target="_blank">'.$this->l('english').'</a>, <a class="link" href="http://prestadev.ru/forum/tema-1509.html" target="_blank">'.$this->l('russian').'</a></span><br/>
						<span><b>'.$this->l('Website').':</b> <a class="link" href="http://modulez.ru'.($iso_code == 'ru' ? '' : '/en/').' " target="_blank">modulez.ru'.($iso_code == 'ru' ? '' : '/en/').'</a><br/>
						<span><b>'.$this->l('Contact').':</b> <a class="link" href="http://modulez.ru'.($iso_code == 'ru' ? '/feedback.php' : '/en/feedback.php').' " target="_blank">modulez.ru'.($iso_code == 'ru' ? '/feedback.php' : '/en/feedback.php').'</a><br/>
						<span style="font-style:italic">('.$this->l('please, send me a message in russian or english only').')</span></span><br/>
						<br/>
						<span style="font-style:italic">'.$this->l('Thank you for the using this module').'</span>&nbsp;&nbsp;<img src="../modules/'.$this->name.'/zapalm24x24.jpg" />
					</div>
				</fieldset>
				<br class="clear">
		';		
		
		if ($iso_code != 'ru') {
			$output .= '
					<fieldset style="width: 400px;">
						<legend><img src="../img/admin/manufacturers.gif" /> '.$this->l('Translation').'</legend>
						<div id="translation_div">
							<span><b>'.$this->l('If you translate this module to your native language, you may give your translation to community. Just click to a button to send a translation file to modulez.ru site.').'</b></span><br/><br/>
							<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
								<center><input type="submit" name="submit_translation" value="'.$this->l('Submit').'" class="button" /></center>
							</form>
						</div>
					</fieldset>
					<br class="clear">
			';
		}
		
		$output .= '
				<fieldset style="width: 400px;">
					<legend><img src="../img/admin/manufacturers.gif" /> '.$this->l('Subscribe').'</legend>
					<div id="subscribe_div">
						<span><b>'.$this->l('You may subscribe to modulez.ru news. Don\'t worry, i will send very few news messages to your e-mail :)').'</b></span><br/><br/>
						<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
							<input type="text" value="'.Configuration::get('PS_SHOP_EMAIL').'" name="email">
							<input type="submit" name="submit_subscribe" value="'.$this->l('Subscribe / Unsubscribe').'" class="button" />
						</form>
					</div>
				</fieldset>
				<br class="clear">
		';			
		
		return $output;
	}

	function hookHome($params)
	{
		global $smarty;
		
		$conf = Configuration::getMultiple(array_keys($this->conf));
		$cat = $conf['HOME_FEATURED_CATALOG'];
		$category = new Category($cat ? $cat : 1);
		$nb = $conf['HOME_FEATURED_NBR'];
		if ($conf['HOME_FEATURED_RANDOM']) {
			$products = $category->getProducts(intval($params['cookie']->id_lang), 1, ($nb ? $nb : 10), NULL, NULL, false, true, true, ($nb ? $nb : 10));
		}
		else {
			$products = $category->getProducts(intval($params['cookie']->id_lang), 1, ($nb ? $nb : 10), 'date_add', 'DESC');
		}
		
		$smarty->assign(array(
			'products' => $products,
			'homeSize' => Image::getSize('home'),
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'conf' => $conf
		));

		return $this->display(__FILE__, 'homefeaturez.tpl');
	}
}
