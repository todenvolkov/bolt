function saveShipToPay(){
	


/*
for(i=0;name.length;i++){
alert(name[i]);
}*/
}

function removePayment(carrier,payment){
$.post("shiptopay.php", {carrier:carrier,payment:payment,method:"ajax" },
 function(data){
	 alert(data);
 	$("#shiptopay_"+carrier+payment).remove();
  });

}

function validateFormSTP(){
	if($("#carr").val()==""){
			alert("Wybierz sposób wysyłki");
			return false;
		}else if($("#pay").val()==""){
			alert("Wybierz sposób płatności");
			return false;
		}else{
			
			return true;
		}

}
