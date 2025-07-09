$(document).ready(function(){
  
});

function showValue(pandangan){
	var nilai = document.getElementById(pandangan).value;
	//alert(nilai);
	document.getElementById(pandangan+'b2').style.width = (nilai*90/100)+'%';
	document.getElementById(pandangan+'b1').innerHTML = nilai;
}

