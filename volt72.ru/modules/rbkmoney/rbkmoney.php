<?php
if (!defined('_CAN_LOAD_FILES_'))
	exit;

class rbkmoney extends PaymentModule {

    function __construct() {
        $this->name = 'rbkmoney';
        $this->tab = 'payments_gateways';
        $this->version = '0.2';

        parent::__construct();

        $this->displayName = $this->l('RBK Money');
        $this->description = $this->l('Оплата через RBK Money');
    }

    function install() {
        if (!parent::install()
                OR !$this->registerHook('payment')
                OR !$this->registerHook('paymentReturn')
        )
            return false;
        return true;
    }

    function uninstall() {
        return (
        parent::uninstall() AND
        Configuration::deleteByName('rbkmoney_PURSE') AND
        Configuration::deleteByName('rbkmoney_KEY')
        );
    }

    function getContent() {
        global $currentIndex, $cookie;

        if (Tools::isSubmit('submitrbkmoney')) {
            if ($rbkmoney_PURSE = Tools::getValue('rbkmoney_PURSE'))
                Configuration::updateValue('rbkmoney_PURSE', $rbkmoney_PURSE);
            if ($rbkmoney_KEY = Tools::getValue('rbkmoney_KEY'))
                Configuration::updateValue('rbkmoney_KEY', $rbkmoney_KEY);
            echo '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('ok') . '" /> ' . $this->l('Настройки обновлены') . '</div>';
        }

        $html = '<h2>' . $this->displayName . '</h2>
		<form style="float:right; width:200px; margin:15px; text-align:center;">
			<fieldset>
				<a href="http://prestalab.ru/"><img src="http://prestalab.ru/upload/banner.png" alt="Модули и шаблоны для PrestaShop" width="174px" height="100px" /></a>
			</fieldset>
		</form>
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
			<fieldset>
			<legend><img src="' . __PS_BASE_URI__ . 'modules/rbkmoney/logo.gif" />' . $this->l('Настройки') . '</legend>
				<label>
					' . $this->l('Номер сайта продавца') . '
				</label>
				<div class="margin-form">
					<input type="text" name="rbkmoney_PURSE" value="' . Tools::getValue('rbkmoney_PURSE', Configuration::get('rbkmoney_PURSE')) . '" />
				</div>
				<label>
					' . $this->l('Секретный ключ') . '
				</label>
				<div class="margin-form">
					<input type="text" name="rbkmoney_KEY" value="' . Tools::getValue('rbkmoney_KEY', Configuration::get('rbkmoney_KEY')) . '" />
				</div>

				<div class="clear center"><input type="submit" name="submitrbkmoney" class="button" value="' . $this->l('Сохранить') . '" /></div>
			</fieldset>
		</form>
		<br /><br />
		<fieldset>
			<legend><img src="../img/admin/warning.gif" />' . $this->l('Информация') . '</legend>
			<p>Адрес для оповещений: '.Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/rbkmoney/validation.php</p>
			<p>' . $this->l('Инструкцию по установке и настройке смотрите на сайте') . ' <a href="http://prestalab.ru/moduli-oplaty/26-modul-rbk-money.html">PrestaLab.ru</a></p>
		</fieldset>';

        return $html;
    }

    function hookPayment($params) {
        if (!$this->active)
            return;

        global $smarty;

        $smarty->assign(array(
            'id_cart' => $params['cart']->id
        ));

        return $this->display(__FILE__, 'payment.tpl');
    }

    function hookPaymentReturn($params) {
        global $cookie, $smarty;
        if (!$this->active)
            return;

        if (!$order = $params['objOrder'])
            return;

        if ($cookie->id_customer != $order->id_customer)
            return;
        if (!$order->hasBeenPaid())
            return;
        $smarty->assign(array(
            'products' => $order->getProducts()
        ));

        return $this->display(__FILE__, 'payment_return.tpl');
    }

}

?>
