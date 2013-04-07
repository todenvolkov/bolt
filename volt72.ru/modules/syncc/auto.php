<?php
header("Content-type:text/html; charset=utf-8");
$_SESSION["N_IMPORT"]["NS"]["STEP"]=0;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />

<style>
.panel{
	display:block;
	width:600;
}
.button_r, .button_b {
	text-align:center;
	text-decoration: none;
	/*font-weight:bold;*/
	display:block;
	width:150px;
	height:20px;
	/*height:30px;*/
	border:1px solid #ADC3D5;
	font-size:12px;
	float:left;
	margin-right:5px;
	margin-top:5px
}

.button_r{
	background-color:#fffee9;
	color:red;
}

.button_b{
	background-color:#f6ffff;
	color:blue;
}

#main {
	display:none;
	width:600px;
	font-size:12px;
	border:1px solid #ADC3D5;
	padding:5
}

#load {
	text-align:right;
}

#settings {
	background-color:#A1C3D1;
	font-size:11px;
}

#progressBar {
	width:1px;
	height:2px;
	background-color:#BCC6C8;
	font-size:12px;
	border:1px solid #7D7D7D
}

</style>
</head>

<div class="panel">
<a class="button_b" href="javascript:start()">импорт</a> 
<a class="button_r" href="javascript:resetdb()">очистить базу данных</a>
<a class="button_b" href="javascript:reccat()">обновить дерево каталогов</a>
<a class="button_r" href="javascript:status='stop'">остановить импорт</a>

</div>
<br />
<br />
<br />
<div id="main">
<div id="progressBar"></div> 
<div id="timer"></div>
<div id="log"></div> 
<div id="load"></div>
<div id="edump"></div>  
</div>


<script>
var 
log=document.getElementById("log"); 
edump=document.getElementById("edump"); 
timer=document.getElementById("timer"); 
load=document.getElementById("load"); 
settings=document.getElementById("settings");
progressBar=document.getElementById("progressBar");

//переменные таймера 
m_second=0; 
seconds=0; 
minute=0; 
//переменные импорта 
countgood=0;
progrW=0;
i=1;
offset=0; 
a=''; 
proccess=true; 
status="continue";
aresponse = new Array();

function getParam(){
	var tmp = new Array(); 
	tmp2 = new Array();
	param = new Array();
	var get = location.search;
	get = decodeURIComponent(get);
	if (get != ''){
		tmp = (get.substr(1)).split('&');
		for(var i=0; i < tmp.length; i++) {
			tmp2 = tmp[i].split('=');	// массив param будет содержать
			param[tmp2[0]] = tmp2[1];	// пары ключ(имя переменной)->значение
		}
	}
	return param;
}

// собираем гет данные.
arrParam = getParam();
path = arrParam['path'];
countString = arrParam['countString'];
function getXmlHttp(){
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(E){
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
  return xmlhttp;
}

function start(){
	init();
	log.innerHTML="Импорт "+offset+" | ";
	rn = 'procc.php?path='+path+'&countString='+countString+'&mode=import&submit';
	query_scr(rn);
}

function resetdb(){
	init();
	log.innerHTML="чистка базы :";
	rn = 'procc.php?path='+path+'&mode=import&clearBaseOnly=on';
	query_scr(rn);
}
function reccat(){
	init();
	log.innerHTML="обновление групп: ";
	rn = 'procc.php?path='+path+'&mode=import&updateCat=on';
	query_scr(rn);
}

function init(){
	document.getElementById("main").style.display='block';
	load.innerHTML = 'в работе...';	
	i=1;
	a="";
	minute=0;
	m_second=0;
	seconds=0;
	process=true;
	start_timer();
	timer.innerHTML="";
	progressBar.style.display = "block";
}

function roundProgBar(num,len){
	len = len/100;
	len = Math.round(num*len);
	return len;
}

function query_scr(request){
	var req = getXmlHttp();
	var r = request; //'procc.php?path='+path+'&countString='+countString+'&mode=import&submit';
	a = log.innerHTML;
	req.onreadystatechange = function(){
		if (req.readyState == 4){
			if(req.status == 200){
				aresponce = new Array();
				aresponse = req.responseText.split("\n");
				if (!aresponse[2])
					aresponse[2]="count:0";
				countgood = countgood + Number(aresponse[2].substr(6,128));
				log.style.display = "block";
				timer.style.display = "block";
				if (aresponse[4]){
					edump.innerHTML = aresponse[4]+"<br />";
				}
				log.innerHTML = "step:"+i+" <br />"+aresponse[1]+"<br />loaded: "+countgood
				//log.innerHTML = a+"timer: "+seconds+":"+m_seconds;
				if (aresponse[0]=="success"){
					progrW++;
					timer.innerHTML = "time:"+minute+":"+m_second;
					progressBar.style.width = roundProgBar(Number(aresponse[3].substr(8,3)),600);
					i++;
					query_scr(r);
				}else if(aresponse[0]=="done"){
					timer.innerHTML = "time:"+minute+":"+m_second;
					log.innerHTML = a+" -- "+req.responseText;
					log.innerHTML = a+"завершено!";
					load.style.display = "none";
				}else{
					timer.innerHTML = "time:"+minute+":"+m_second;
					log.innerHTML = a+" <br />time:"+minute+":"+m_second+" -- "+req.responseText;
					load.style.display = "none";
				}
			}
		}
		if(req.readyState == 4){
			if (req.status == 0){
				timer.innerHTML = "time:"+minute+":"+m_second;
				error_mes = "<div class=\"ferror\">крит ошибка: сервер упал и вовремя не ответил!</div>"
				log.style.dysplay = "block";
				log.innerHtml = a+" : "+i+" "+error_mes;
				load.style.display = "none";
				status = "continue";
			}
		}
	}
	req.open('GET', r, true);
	req.send(null);
	load.style.display="block";
	//document.getElementById('votestatus'  + id).innerHTML = 'загрузка...';
}

function start_timer(){
	if (m_second==60){
		m_second=0;
		minute+=1;
	}
	if (proccess==true){
		seconds+=1;
		m_second+=1;
		setTimeout("start_timer()",1000);
	}
}
</script>
