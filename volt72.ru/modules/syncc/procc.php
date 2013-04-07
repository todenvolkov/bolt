<?
//sync1c
//created Krox
// при удалении скрипт оставляет только 
// категории товаров опт и розница 
// (включая один уровень категорий в рознице)
//

set_time_limit(60 * 5);
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/../../images.inc.php');
require_once "KTools.class.php";

$path = Tools::getValue('path');
$uploadImage = Tools::getValue('uploadImage') ? Tools::getValue('uploadImage') : 'on';
$countString = Tools::getValue('countString') ? Tools::getValue('countString') : 10; // сколько читать за раз (строки)
$timeOut = Tools::getValue('timeOut') ? Tools::getValue('timeOut') : 5000; // сколько времени скрипту (секунды)
$clearBaseNow = Tools::getValue('clearBaseNow') ? Tools::getValue('clearBaseNow') : 'off';
$clearBaseOnly = Tools::getValue('clearBaseOnly') ? Tools::getValue('clearBaseOnly') : 'off';
$updateCat = Tools::getValue('updateCat') ? Tools::getValue('updateCat') : 'off';
$zipFile = Tools::getValue('zipfile') ? Tools::getValue('zipfile') : 'off';
$delfiles = Tools::getValue('delfiles') ? Tools::getValue('delfiles') : 'off';

$login = Tools::getValue('login');
$pass = Tools::getValue('pass');

//if(!auth($login,$pass)){
//	header('HTTP/1.1 403 Forbidden');
//	exit;
//}

$categsOnly = true; // обновить дерево каталогов
$catOpt = 8; // id оптового каталога
$idLang = 6; // id языка
$catR = 7;
$deleteOpt = true;
$gcatlevel=2; //level depth в category мин 2
$totalquery = 0;
//$path = "/demo/import/vigruzka/";
$path = "/sync-data/11072012155717/";
//$path = '11072012155717/';

// cat struct
$catStruct = array(
	'root'=>'NSystems/DATA/',
	'img'=>'NSystems/exp_images/',
	'rimg'=>'NSystems/'
);
$fileNames = array(
	'catalog' => 'import_groups.csv',
	'goods' => 'import_tov.csv'
);


$arrHeadsName=array(
	'id',
	'artic',
	'usl',
	'nabor',
	'NDS',
	'parentId',
	'name',
	'fullname',
	'quantity',
	'mIzm',
	'tprice',
	'price',
	'ost',
	'val',
	'edIzm',
	'img',
	'comment',
	'date',
	'f'
);
$arrCatHeadsName=array(
	'idcat',
	'namecat',
	'nameparent',
	'idparent',
	'level'
);

//
//define('_PS_PROD_PIC_DIR_', dirname($_SERVER['DOCUMENT_ROOT']).'/demo/'.$path);
define('_PS_PROD_PIC_DIR_', dirname( __FILE__ ) . $path);
//define('_SYNC_ROOT_DIR_', dirname($_SERVER['DOCUMENT_ROOT']).'/demo/');
define('_SYNC_ROOT_DIR_', dirname( __FILE__ ) . $path);
$fileCat = _PS_PROD_PIC_DIR_.$catStruct['root'].$fileNames['catalog'];
$file = _PS_PROD_PIC_DIR_.$catStruct['root'].$fileNames['goods'];
$zipFileName = "NSystemsImp.zip";



/***********************************
 * general functions 
 */

function auth($login,$pass){
	if($pass == Configuration::get('SYNCC_PASSWORD') && $login == Configuration::get('SYNCC_LOGIN')){
		return true;
	}
	return false;
}

function ClearBase(){
	global $catOpt,$catR,$edump;
	/*
	Db::getInstance()->Execute(
		'DELETE FROM `'._DB_PREFIX_.'category` 
		WHERE id_category != 1 
			AND id_category != '.$catOpt.' 
			AND id_category != '.$catR.' 
			AND id_parent != '.$catR.' '
	);
	*/
	Db::getInstance()->Execute(
		'DELETE FROM `'._DB_PREFIX_.'category` 
		WHERE id_category != 1' 
			
	);
	// delete category_lang
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'category_lang 
			WHERE id_category NOT IN (
			SELECT id_category
				FROM '._DB_PREFIX_.'category
				WHERE '._DB_PREFIX_.'category_lang.id_category = '._DB_PREFIX_.'category.id_category)'
		);
	// delete category_group
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'category_group
			WHERE id_category NOT IN (
			SELECT id_category
				FROM '._DB_PREFIX_.'category
				WHERE '._DB_PREFIX_.'category_group.id_category = '._DB_PREFIX_.'category.id_category)'
		);
		
	//products
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'product
			WHERE id_product NOT IN(
				SELECT id_product
				FROM '._DB_PREFIX_.'category
				WHERE '._DB_PREFIX_.'category.id_category = '._DB_PREFIX_.'product.id_category_default)'
		);
	//product_lang
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'product_lang
			WHERE id_product NOT IN (
				SELECT id_product
				FROM '._DB_PREFIX_.'product AS p
				WHERE p.id_product = '._DB_PREFIX_.'product_lang.id_product)'
		); 
	//image
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'image
		WHERE id_product NOT IN (
			SELECT id_product
			FROM '._DB_PREFIX_.'product AS p
			WHERE p.id_product = '._DB_PREFIX_.'image.id_product)'
	); 
	//image_lang
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'image_lang
		WHERE id_image NOT IN (
			SELECT id_image
			FROM '._DB_PREFIX_.'image AS i
			WHERE i.id_image = '._DB_PREFIX_.'image_lang.id_image)'
	); 
	//category_product
	Db::getInstance()->Execute('
		DELETE FROM '._DB_PREFIX_.'category_product
		WHERE id_product NOT IN (
			SELECT id_product
			FROM '._DB_PREFIX_.'product p
			WHERE p.id_product = '._DB_PREFIX_.'category_product.id_product)'
	); 
	
	//exit;
	//foreach (scandir(_PS_CAT_IMG_DIR_) AS $d)
	//if (preg_match('/^[0-9]+\-(.*)\.jpg$/', $d) OR preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d))
	//	unlink(_PS_CAT_IMG_DIR_.$d);

	//foreach (scandir(_PS_PROD_IMG_DIR_) AS $d)
	//	if (preg_match('/^[0-9]+\-[0-9]+\-(.*)\.jpg$/', $d) OR preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d))
	//		unlink(_PS_PROD_IMG_DIR_.$d);
	echo "done\n\n\n\n";
	echo ("edump:".$edump."\n");
}
function clearCatDB(){
	global $catOpt,$catR;
	//Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category != 1 AND id_category != '.$catOpt.' AND id_category != '.$catR.' ');
	//Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category != 1 AND id_category != '.$catOpt.' AND id_category != '.$catR.' ');
	//Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE id_category != 1 AND id_category != '.$catOpt.' AND id_category != '.$catR.' ');
	Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category != 1');
	Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category != 1');
	Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE id_category != 1');

	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 2');
}

function unzipWpclzip($file,$toPath){
	//var_dump($file,$toPath);
	require_once('pclzip.lib.php');
	$archive = new PclZip($file);
	$list = $archive->extract(PCLZIP_OPT_PATH, $toPath);
	if ($list == 0){
		die("Error : ".$archive->errorInfo(true));
	}
	echo "done\n";
}

function addGoods($data){
	global $uCount,$idLang,$uploadImage,$edump;
	$catid = '';
	$id_product = Db::getInstance()->getValue("SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `xml`='".$data['id']."'");
	
	
	
	
	//update product
	if ($id_product !=''){
		updGoods($data,$id_product);
		return;
	}


	$catid = Db::getInstance()->getValue("SELECT `id_category` FROM `"._DB_PREFIX_."category` WHERE xml='".$data['parentId']."'");
	//add catalogs brunch 
	

	
	if ($catid == ''){
		$catBrunch = array();
		$catBrunch = getCatsBrunch($data['parentId'],$catBrunch);
		//var_dump($catBrunch);
		if(is_array($catBrunch)){
			$catBrunch = array_reverse($catBrunch);
			$catid = addCatBrunch($catBrunch);
			
			
			
		}
		
		
	}
	//else cat - default 
	$catid != '' ? $catid=$catid : $catid = 1;
	
//echo '<PRE>';
//var_dump($catid);
//echo '</PRE>';
//exit;
	
	Db::getInstance()->Execute("
		INSERT INTO `"._DB_PREFIX_."product` (`xml`,`reference`,`id_category_default`,`date_add`,`date_upd`,`active`)
		VALUES ('".$data['id']."', '".$data['artic']."' , ".$catid.", NOW(), NOW(), 1)
		");
	$id_goods=Db::getInstance()->Insert_ID();
		
	Db::getInstance()->Execute("
		INSERT INTO `"._DB_PREFIX_."product_lang` (`id_product`,`id_lang`, `name`,`description_short`, `description`,`link_rewrite`)
		VALUES (".$id_goods.", ".$idLang.", '".$data['name']."', '".$data['fullname']."', '".$data['comment']."', '".Tools::link_rewrite($data['name'])."')
		");
	
	Db::getInstance()->Execute("
		INSERT IGNORE INTO `"._DB_PREFIX_."category_product`(`id_product`,`id_category`) SELECT id_product,id_category_default FROM `"._DB_PREFIX_."product`
		");
	
	addGoodFeats($data);
	if ($uploadImage=='on')
		addImg($data,$id_goods);
	$uCount['prod']++;
}

function updGoods($data,$id_product){
	global $uCount, $idLang, $uploadImage;
	// по иду прода обновляем
	Db::getInstance()->Execute("
		UPDATE `"._DB_PREFIX_."product`
		SET `reference`='".$data['artic']."', `date_upd`=NOW()
		WHERE `id_product`= ".$id_product
	);
	Db::getInstance()->Execute("
		UPDATE `"._DB_PREFIX_."product_lang`
		SET `name`='".$data['name']."', `description_short`='".$data['fullname']."', `description`='".$data['comment']."'
		WHERE `id_lang`=".$idLang." AND `id_product`= ".$id_product
	);
	addGoodFeats($data);
	if ($uploadImage=='on')
		updImg($data,$id_product);
}

function updImg($data,$id_product){
	global $catStruct, $idLang;
	if(!file_exists(_PS_PROD_PIC_DIR_.$catStruct['rimg'].$data['img'])){
		return false;
	}
	
	$filesize = filesize(_PS_PROD_PIC_DIR_.$catStruct['rimg'].$data['img']);
	if($filesize > 5*1024*1024){
		return false;
	}
	
	// если вдруг ее нету добавляем
	$id_image = Db::getInstance()->getValue("SELECT `id_image` FROM `"._DB_PREFIX_."image` WHERE  `cover`=1 AND `id_product`=".$id_product);
	if($id_image==''){
		addImg($data,$id_product);
		return false;
	}
	
	Db::getInstance()->Execute("
		UPDATE IGNORE `"._DB_PREFIX_."image_lang`
		SET `legend`='".$data['name']."'
		WHERE `id_lang`='".$idLang."' AND `id_image`=".$id_image.""
		);
		
	copyImg($id_product, $id_image, _PS_PROD_PIC_DIR_.$catStruct['rimg'].$data['img']);
	return $id_image;
}

function addImg($data,$id_goods){
	global $catStruct, $idLang;
	
	if(!file_exists(_PS_PROD_PIC_DIR_.$catStruct['rimg'].$data['img'])){
		return false;
	}
	
	$filesize = filesize(_PS_PROD_PIC_DIR_.$catStruct['rimg'].$data['img']);
	if($filesize > 5*1024*1024){
		return false;
	}
	
	Db::getInstance()->Execute("
		INSERT INTO `"._DB_PREFIX_."image` (`id_product`,`cover`)
		VALUES (".$id_goods.", 1)
		");
	$id_image=Db::getInstance()->Insert_ID();
		Db::getInstance()->Execute("
		INSERT INTO `"._DB_PREFIX_."image_lang` (`id_image`,`id_lang`, `legend`)
		VALUES (".$id_image.", ".$idLang.", '".$data['name']."')
		");
		
	copyImg($id_goods, $id_image, _PS_PROD_PIC_DIR_.$catStruct['rimg'].$data['img']);
	return $id_image;
}

function copyImg($id_entity, $id_image, $tmpfile){
	global $t;
	$path = _PS_PROD_IMG_DIR_.intval($id_entity).'-'.intval($id_image);
	@imageResize($tmpfile, $path.'.jpg');
	$imagesTypes = ImageType::getImagesTypes('products');
	foreach ($imagesTypes AS $k => $imageType)
		@imageResize($tmpfile, $path.'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
	//unlink ($tmpfile);
	//$t->debug('картинка :'.$path);
	return true;
}

//-------------- price only ------------
function addGoodFeats($data){
	// check
	$data['price']=='' ? $data['price']=0 : $data['price']=$data['price'];
	$data['ost']=='' ? $data['ost']=0 : $data['ost']=$data['ost'];
	// удаляем пробелы из строки.
	// $pattern = chr(20);
	$pattern = "/\xC2\xA0/";
	$price = preg_replace($pattern, '',$data['price']);
	$ost = preg_replace($pattern, '',$data['ost']);
	Db::getInstance()->Execute("
		UPDATE IGNORE `"._DB_PREFIX_."product`
		SET `price`='".$price."', `quantity`=".$ost.", `date_upd`=NOW()
		WHERE `xml`='".$data['id']."'"
	);
}

$firstCat = false;
function addCat($catname,$cat_id,$parentCat,$level){
	global $idLang,$catlevel,$firstCat,$catOpt, $edump;
	
	$updcategid = Db::getInstance()->getValue("SELECT `id_category` FROM `"._DB_PREFIX_."category` WHERE xml='".$cat_id."'");
	if($updcategid!=''){
		updCat($catname,$cat_id,$parentCat,$level,$updcategid);
		return $updcategid;
	}
	
	$parent_id = Db::getInstance()->getValue("SELECT `id_category` FROM `"._DB_PREFIX_."category` WHERE xml='".$parentCat."'");
	$parent_id != '' ? $parent_id=$parent_id : $parent_id = 1;
	
	//echo $parent_id;
	//Db::getInstance()->Execute("
	//		INSERT INTO `"._DB_PREFIX_."category` (`xml`,`id_parent`,`level_depth`,`date_add`,`date_upd`,`active`,`opt`)
	//		VALUES ('".$cat_id."', '".$parent_id."', ".$level.", NOW(), NOW(), 1, 1)
	Db::getInstance()->Execute("
			INSERT INTO `"._DB_PREFIX_."category` (`xml`,`id_parent`,`level_depth`,`date_add`,`date_upd`,`active`)
			VALUES ('".$cat_id."', '".$parent_id."', ".$level.", NOW(), NOW(), 1)
			");
	$id_categ=Db::getInstance()->Insert_ID();
	Db::getInstance()->Execute("
			INSERT INTO `"._DB_PREFIX_."category_lang` (`id_category`,`id_lang`, `name`,`link_rewrite`)
			VALUES (".$id_categ.", ".$idLang.", '".$catname."', '".Tools::link_rewrite($catname)."')
			");
	Db::getInstance()->Execute("
			INSERT INTO `"._DB_PREFIX_."category_lang` (`id_category`,`id_lang`, `name`,`link_rewrite`)
			VALUES (".$id_categ.", 1, '".$catname."', '".Tools::link_rewrite($catname)."')
			");
	Db::getInstance()->Execute("
			INSERT INTO `"._DB_PREFIX_."category_group` (`id_category`,`id_group`)
			VALUES (".$id_categ.",1 )
			");
	return $id_categ;
}

function updCat($catname,$cat_id,$parentCat,$level,$updcategid){
	$cat_group = Db::getInstance()->getValue("SELECT `id_group` FROM `"._DB_PREFIX_."category_group` WHERE id_category='".$updcategid."'");
	if($cat_group!='') return;
	Db::getInstance()->Execute("
		INSERT INTO `"._DB_PREFIX_."category_group` (`id_category`,`id_group`)
		VALUES (".$updcategid.",1 )
	");
}

// get level (recursive)
$catlevel;
function getSelfCatLevel($cats,$parent,$level=0){

	if($parent == '00000000-0000-0000-0000-000000000000'){
		return $level;
	}
	
	foreach ($cats as $cat){
		if($parent===$cat['idcat']){
			if($level = getSelfCatLevel($cats,$cat['idparent'],$level+1)){
				return $level;
			}
		}
	}
}

function getCatList($file,$xml=0,$parentXml=0){
	global $t, $arrCatHeadsName, $catArr, $gcatlevel;
	if(count($catArr)>0)
		return;
	
	$i=0;
	$a=0;
	while ($datac = $t->readString($file,'goods')){
		$datac = $t->stringToAssoc($datac,$arrCatHeadsName);
		$catArr[$datac['idcat']] = $datac;
		$catArr[$datac['idcat']]['level'] = getSelfCatLevel($catArr,$datac['idparent'],$gcatlevel);
		$i++;
	}
}

// add cats brunch (recursive)
function getCatsBrunch($parentId,$catBrunch){
	global $catArr;
	
	
	
	
	if($parentId == '00000000-0000-0000-0000-000000000000'){
		return $catBrunch;
	}
	
	foreach ($catArr as $key => $value){
		if ($key == $parentId){
			$catBrunch[] = $value;
			//if ($catBrunch != getCatsBrunch($value['idparent'],$catBrunch)){
				return $catBrunch;
			//}
		}
		
		
	}
	
	
}

function addCatBrunch($cats){
	global $edump;
	foreach ($cats as $value){
		$id = addCat($value['namecat'],$value['idcat'],$value['idparent'],$value['level']);
		$edump = "ADDCAT: ".$value['namecat']."<br/>";
	}
	return $id;
}

function getCatId($data){
	global $categs;
	if ($categs=='')
		$categs = Db::getInstance()->getValue("SELECT `id_category`,xml FROM `"._DB_PREFIX_."category`");
	$id = $data['id'];
	$catid = array_search($id,$categs);
	return $catid;
}


function RemoveDir($path,$lev=0){
	
	if(file_exists($path) && is_dir($path)){
		$dirHandle = opendir($path);
		while (false !== ($file = readdir($dirHandle))) {
			if ($file!='.' && $file!='..' && $file!='logs'){
				$tmpPath=$path.'/'.$file;
				chmod($tmpPath, 0777);
				
				if (is_dir($tmpPath)){
					RemoveDir($tmpPath,$lev+1);
				}else{
					if(file_exists($tmpPath)){
						// удаляем файл
						if($file == 'imp.log'){
							if(!file_exists(_SYNC_ROOT_DIR_.'/logs')){
								if (mkdir(_SYNC_ROOT_DIR_.'/logs'));
								chmod($tmpPath, 0777);
							}
							copy($tmpPath, _SYNC_ROOT_DIR_.'/logs/'.time().'.log');
						}
						unlink($tmpPath);
					}
				}
			}
		}
		closedir($dirHandle);
		// удаляем текущую папку
		if(file_exists($path) && $lev>0){
			rmdir($path);
		}
	}else{
		//echo "Удаляемой папки не существует или это файл!";
	}
}

/***********************************
 * general loop
 **********************************/

if($zipFile == 'on'){
	unzipWpclzip(_PS_PROD_PIC_DIR_.$zipFileName,_PS_PROD_PIC_DIR_.$catStruct['rimg']);
	exit;
}

$t = new KTools;
$arrconfig = array(
	'writeOffset'=>false
);

$t->configure($arrconfig);
$i = 0;
//----------------------
getCatList($fileCat);

//----------------------

if ($clearBaseOnly == 'on'){ //чистим базу и выходим
	ClearBase();
	exit;
}

if ($delfiles == 'on'){
	RemoveDir(_SYNC_ROOT_DIR_);
	exit;
}


$g = new KTools;
$arrconfigG = array(
	'getPercentEx'=>true
);
$g->configure($arrconfigG);

$countString = 5;
while(!$end){
	if(!$data = $g->readString($file,'goods')){	
		$end = true;
	}
	
	$i++;
	if ($i>=$countString){
		$end = true;
	}
	
	if($g->err){ //can't find file
		echo "Error. Can't find file\n\n";
		exit;
	}
	
	if($data){ //end file
		$data = $g->stringToAssoc($data,$arrHeadsName);
	}else{
		echo "done\n\n";	
		exit;
	}
	
	if (!$data){
		echo "done\n\n";
		exit;
	}

	if ($data['id']==''){
		echo "error\n\n\n\n";
		echo "can't parsing string!";
		exit;
	}
	
	
	
	addGoods($data);
	$g->putOffset($file, 'goods');
};
echo "success\n";
echo ("offset:".$g->offset."\n");
echo ("count:".$i."\n");
echo ("percent:".$g->percentExec."\n");
echo ($edump."\n");
//echo $t->$offset;
//$t->debug('time');
//$t->debug('memory');
//$t->debug('memory_peak');
//$t->debug('count of string: '.$i);
?>