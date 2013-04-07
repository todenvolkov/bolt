<?php
/*
* 2007-2010 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author Prestashop SA <contact@prestashop.com>
*  @copyright  2007-2010 Prestashop SA
*  @version  Release: $Revision: 14089 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

require_once ('../../config/config.inc.php');

$onload_script = 'parent.$.fancybox.close();';
if (Tools::isSubmit('firstcall'))	
	$onload_script = 'document.getElementById(\'socoForm\').submit();';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr"> 
	<head>
	</head>
	<body onload="<?php echo $onload_script; ?>">
		<?php
		echo '<form id="socoForm" name="form" action="'.Configuration::get('SOCOLISSIMO_URL').'" method="POST">';
		foreach($_GET as $key => $val)
			if (Validate::isCleanHtml($key) && Validate::isCleanHtml($val))
				echo '<input type="hidden" name="'.Tools::safeOutput($key).'" value="'.Tools::safeOutput($val).'"/>';
		?>
	</body>
</html>
</form>