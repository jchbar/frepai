function foco(elem) {
	document.getElementById(elem).focus();
}
function borrar_cuenta() {
	return confirm("¿Está seguro de que quiere borrar esta Cuenta?")
}

function borrar_asiento() {
	return confirm("¿Está seguro de que quiere borrar este Asiento?")
}

function borrar_justificante() {
	return confirm("¿Está seguro de que quiere borrar este Justificante?")
}

function borrar_apunte() {
	return confirm("¿Está seguro de que quiere borrar este Apunte?")
}

function borrar_reg_820() {
	return confirm("¿Está seguro de borrar este Registro?")
}

function borrar_cuenta() {
	if (form1.cuenta.value > 0)
	{
	return confirm("¿Está seguro de que quiere borrar este Subgrupo, Cuenta o Subcuenta?")
	}
	form1.cuenta.focus();
	return false
}

function oculta(div)
{
div.style.display="none";
} 
function amplred(div)
{
	if (document.getElementById(div).style.display == "none")
	{
		document.getElementById(div).style.display="";
	} else {
		document.getElementById(div).style.display="none";
	}
}

function redondea(n, deci) {
	var n1 = n * Math.pow(10, deci)
	var n2 = Math.round(n1)
	var n3 = n2 / Math.pow(10, deci)
	return decimales(n3, deci)
}

function decimales(n, deci) {
	
	var cadena = n.toString()
	var pos = cadena.indexOf(".")
	if (pos == -1) {
		decimal = 0
		cadena += deci > 0 ? "." : ""
	}
	else {
		decimal = cadena.length - pos - 1
	}
	var total = deci - decimal
	if (total > 0) {
		for (var conta = 1; conta <= total; conta++)
		cadena += "0"
	}
	return cadena
}

function compruebafecha(form1) {
	var mes31dias = /^([1-3]0|[0-2][1-9]|31|[0-9])\/(1|01|3|03|5|05|7|07|8|08|10|12)\/([0-1][0-9]|20)$/
	var mes30dias = /^([1-3]0|[0-2][1-9]|[0-9])\/(4|04|6|06|9|09|11)\/([0-1][0-9]|20)$/
	var mes28dias = /^([1-2]0|[0-2][1-8]|[0-1]9|[0-9])\/(02|2)\/(0[1-3]|0[5-7]|09|1[0-1]|1[3-5]|1[7-9])$/
	var mes29dias = /^([1-2]0|[0-2][1-9]|[0-9])\/(02|2)\/(00|04|08|12|16|20)$/
	if (!(mes31dias.test(form1.lafecha.value) ||
		mes30dias.test(form1.lafecha.value) ||
		mes29dias.test(form1.lafecha.value) ||
		mes28dias.test(form1.lafecha.value))) {
		alert("Contenido del campo FECHA no válido. Formato: dd/mm/aa ("+form1.lafecha.value+")")
		form1.lafecha.focus()
		form1.lafecha.select()
		return false
	}
	return true
}

function gceditcli(form1) {
	
	if (form1.cliente.value == "" || form1.domicilio.value == "" || form1.ciudad.value == "" || form1.telefono.value == "")
	{
		alert("Completar todos los campos")
		form1.cliente.focus()
		return false
	}
	return true
}

function gccli(form1) {
	if (vacio(form1.codigo.value) == "" || vacio(form1.nombre.value) == "" || vacio(form1.saldoi.value) == "" )
	{
		alert("Complete todos los campossss")
		form1.cliente.focus()
		return false
	}
	return true
}

function vacio(q) {  
        for ( i = 0; i < q.length; i++ ) {  
                if ( q.charAt(i) != " " ) {  
                        return true  
                }  
        }  
        return false  
}  

function valsoc(form1) {
	// || (form1.lafechaingreso.value == "" ) 
	if ((form1.elcodigo.value == "") || (form1.lacedula.value == "") || (form1.elnombre.value == "") || (form1.elapellido.value == "" )
		|| (form1.elcargo.value == "" ) || (form1.elnrocuenta.value == "" ) 
		|| (form1.elsueldo.value == "" )
		|| (form1.laescuela.value == "" )|| (form1.eldpto.value == "" ) || (form1.eltipoafiliado.value == "" ) 
		|| (form1.elestatus.value == "" ) || (form1.eltipo.value == "" )
		)
	{
		valor = parseInt(form1.elsueldo.value) 
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.elcodigo.value == "") form1.elcodigo.focus()
		if (form1.lacedula.value == "") form1.lacedula.focus()
		if (form1.elapellido.value == "") form1.elapellido.focus()
		if (form1.elnombre.value == "") form1.elnombre.focus()
		if (form1.elcargo.value == "") form1.elcargo.focus()
		if (form1.lafechaingreso.value == "") form1.lafechaingreso.focus()
		if (form1.elnrocuenta.value == "") form1.elnrocuenta.focus()
		if (isNaN(valor)) form1.elsueldo.focus()
		if (form1.laescuela.value == "") form1.laescuela.focus()
		if (form1.eldpto.value == "") form1.eldpto.focus()
		if (form1.eltipoafiliado.value == "") form1.eltipoafiliado.focus()
		if (form1.elestatus.value == "") form1.elestatus.focus()
		if (form1.eltipo.value == "") form1.eltipo.focus()
/*
*/
return false
	}
	return true
}

function altaasi(form1) {
	
	if (form1.asiento.value == "" || form1.fecha.value == "" || form1.cuenta1.value == "" || form1.cuenta2.value == "" || form1.tipo.value == "" || form1.concepto.value == "" || form1.importe.value == "")
	{
		alert("Completa todos los campos.")
		form1.asiento.focus()
		return false
	}
/*
	if (form1.asiento.value > 9999999000) {
		alert("Nº Asiento ha de ser menor o igual que 9.999.999.000.")
		form1.asiento.focus()
		return false
	}
*/
	if (isNaN(form1.importe.value))
	{
		alert ("Importe ha de ser numérico.")
		form1.importe.focus()
		form1.importe.select()
		return false
	}

	form1.importe.value = redondea(form1.importe.value, 2)
	/*form1.proveedor.value = form1.proveedor.substring(7,50)
	form1.cuengas.value = form1.cuengas.substring(7,50)*/
	return true
}

function altaasigral(form1) {
	if ((form1.asiento.value == "") || (form1.fecha.value == "") || (form1.cuenta1.value == "") || (form1.concepto.value == "") || (form1.elmonto.value == ""))
	{
		alert("Completa todos los campos.")
		form1.asiento.focus()
		return false
	}
/*
	if (form1.asiento.value > 9999999000) {
		alert("Nº Asiento ha de ser menor o igual xxxx que 9.999.999.000.")
		form1.asiento.focus()
		return false
	}
*/
	if (isNaN(form1.elmonto.value))
	{
		alert ("Debe ser numérico.")
		form1.elmonto.focus()
		form1.elmonto.select()
		return false
	}
/*
if (isNaN(form1.haber.value))
	{
		alert ("Haber ha de ser numérico.")
		form1.haber.focus()
		form1.haber.select()
		return false
	}
*/
	/*form1.importe.value = redondea(form1.importe.value, 2)
	form1.debe.value = redondea(form1.debe.value, 2)
	form1.haber.value = redondea(form1.haber.value, 2)
	*/
	form1.elmonto.value = redondea(form1.elmonto.value, 2)
	return true
}

function altaasim(form1) {
	if ((form1.asiento.value == "") || (form1.fecha.value == "") || (form1.cuenta1.value == "") || (form1.cuenta2.value == "") || (form1.concepto.value == "") || (form1.elmonto.value == ""))
	{
		alert("Completa todos los campos.")
		form1.asiento.focus()
		return false
	}
/*
	if (form1.asiento.value > 9999999000) {
		alert("Nº Asiento ha de ser menor o igual que 9.999.999.000.")
		form1.asiento.focus()
		return false
	}
*/
	if (isNaN(form1.elmonto.value))
	{
		alert ("Debe ha de ser numérico.")
		form1.elmonto.focus()
		form1.elmonto.select()
		return false
	}
	form1.elmonto.value = redondea(form1.elmonto.value, 2)
	return true
}

function seleccionacuenta1(form1)
{
	form1.cuenta1.value = form1.cuenta11.value
}

function seleccionacuenta2(form1) {
	form1.cuenta2.value = form1.cuenta22.value
}

function seleccionacuentaeditasi2(form1) {
	form1.cuenta1.value = form1.cuenta.value
}

function crearemp(form2){
	if (form2.empresa11.value == "" || form2.nuevaemp.value == "" || form2.anocont.value == "" || form2.usuario.value == "" || form2.clave.value == "" || form2.clave1.value == "")
	{
		alert("Completa todos los campos")
		return false
	}

	if (form2.clave.value != form2.clave1.value)
	{
		alert("No coincide clave con la confirmación de clave")
		return false
	}

	if (form2.anocont.value < 2000)
	{ 
		alert ("El año contable debe de ser mayor que 1999")
		return false 
	}
	return true
}

function confirma(){
	if (form1.nuevosubcuenta.value == "" || form1.nuevodescripci3_.value == "")
	{
		alert("Nº de Subcuenta y Descripción no pueden estar vacíos")
		return false 
	}
	if (form1.nuevosubcuenta.value != form1.subcuenta.value)
	{
		return confirm("¿Está seguro de que quiere modificar la Subcuenta en fichero de Subcuentas y en los Asientos correspondientes?")
	}
	return true
}
function confirma1(){
	if (form1.nuevosubgrupo.value == "" || form1.nuevodescripci1_.value == "")
	{
		alert("Nº de Subgrupo y Descripción no pueden estar vacíos")
		return false 
	}
	
	if (form1.nuevosubgrupo.value != form1.subgrupo.value)
	{
	return confirm("¿Está seguro de que quiere modificar el Subgrupo en fichero de Subgrupos?")
	}
	return true
}
function confirma2(){
	if (form1.nuevocuenta.value == "" || form1.nuevodescripci2_.value == "")
	{
		alert("Nº de Subgrupo y Descripción no pueden estar vacíos")
		return false 
	}
	
	if (form1.nuevocuenta.value != form1.cuenta.value)
	{
		return confirm("¿Está seguro de que quiere modificar la Subcuenta en fichero de Cuentas?")
	}
	return true
}

function validar(form1){
	if (form1.cuenta1.value.length < 1)
	{
		alert ("No puedes dejar el campo CUENTA vacío.")
		form1.cuenta1.focus()
		return false 
	}
	
	if (form1.concepto.value.length < 1)
	{
		alert ("No puedes dejar el campo CONCEPTO vacío.")
		form1.concepto.focus()
		return false
	}
	if (form1.referencia.value.length < 1)
	{
		alert ("No puedes dejar el campo REFERENCIA vacío.")
		form1.referencia.focus()
		return false
	}
	if ((form1.elmonto.value.length < 1) || (form1.elmonto.value < 0))
	{
		alert ("Uno de los dos campos DEBE o HABER ha de tener un valor positivo, el otro ha de ser cero.")
		form1.elmonto.focus()
		return false 
	}
	/*form1.importe.value = redondea(form1.importe.value, 2)*/
/*	form1.debe.value = redondea(form1.debe.value, 2)
	form1.haber.value = redondea(form1.haber.value, 2) */
//	form1.elmonto.value = redondea(form1.elmonto.value, 2)
// || isNaN(form1.elmonto.value) 	
// (form1.elmonto.value < 0) || 
}

function hide(div)
{
document.getElementById(div).style.display = "none";
}

function show(div)
{
document.getElementById(div).style.display = "";
} 


window.onerror=myErrorHandler
function myErrorHandler() {
return true
}

function calcula(key, calcu) {

if (key == 'C'){
	calcu.valor1.value = "";
	calcu.vent.value = "";
	calcu.listado.value = calcu.listado.value + "\n";
	calcu.vent.focus();
	calcu.listado.scrollTop = 1000000;
	return true;
}

if (key == '='){
	if (calcu.vent.value == "") {return;}
	calcu.listado.value = calcu.listado.value + "\n" + calcu.vent.value + " = ";
	calcu.vent.value = eval (calcu.vent.value);
	calcu.listado.value = calcu.listado.value + calcu.vent.value;
	calcu.vent.focus();
	calcu.listado.scrollTop = 1000000
	return true;
}

calcu.vent.value = calcu.vent.value + key
calcu.vent.focus();
calcu.listado.scrollTop = 1000000
return true;
}

function conMayusculas(field) {
    field.value = field.value.toUpperCase()
}

function isEmailAddress(theElement, nombre_del_elemento )
{
var s = theElement.value;
var filter=/^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z0-9_.]$/;
if (s.length == 0 ) return true;
if (filter.test(s))
return true;
else
alert("Ingrese una dirección de correo válida");
theElement.focus();
return false;
}

function valida_codarea(theElement, nombre_del_elemento )
{
var s = theElement.value;
// var filter=/^(\(?[0-9]{4,4}\)?|[0-9]{4,4}[-. ]?)[ ][0-9]{4,4}[-. ]?[0-9]{5,5}$/;
var filter=/^[0-9]{3,4}$/;
if (s.length == 0 ) return true;
if (filter.test(s))
return true;
else
if (s.length == 4 )
	return true;
else 
	alert("Ingrese un código de area válido");
//theElement.focus();
theElement.focus();
return false;
}


function valida_telefono(theElement, nombre_del_elemento )
{
var s = theElement.value;
// var filter=/^(\(?[0-9]{4,4}\)?|[0-9]{4,4}[-. ]?)[ ][0-9]{4,4}[-. ]?[0-9]{5,5}$/;
var filter=/^[0-9]{3,4}-? ?[0-9]{6,7}$/;
if (s.length == 0 ) return true;
if (filter.test(s))
return true;
else
alert("Ingrese un numero valido");
theElement.focus();
return false;
}

function valida_celular(theElement, nombre_del_elemento )
{
var s = theElement.value;
var s2 = theElement;
var filter=/^(\(?[0-9]{4,4}\)?|[0-9]{4,4}[-. ]?)[ ][0-9]{4,4}[-. ]?[0-9]{5,5}$/;
// var filter=/^[0-9]{3,4}-? ?[0-9]{6,7}$/;
// var filter=/^0\(\d{4}\)\s\d{7}$/
// var filter=/(0)?-?\(?\s*([0-9]{4})\s*\)?\s*-?([0-9]{3})\s*-?\s*([0-9]{4})\s*/;
alert('entre');
if (s.length == 0 ) return true;
if (filter.test(s))
	if (s.length = 12 ) return true;
else return true;
else
alert("Ingrese un numero valido");
s2.focus();
return false;
}



function validar(e){
tecla_codigo = (document.all) ? e.keyCode : e.which;
if(tecla_codigo==8)return true;
patron =/[0-9\-]/;
tecla_valor = String.fromCharCode(tecla_codigo);
return patron.test(tecla_valor);

}
function validar2(e){
tecla_codigo = (document.all) ? e.keyCode : e.which;
if(tecla_codigo==8)return true;
patron =/[0-9.]/;
tecla_valor = String.fromCharCode(tecla_codigo);
return patron.test(tecla_valor);
}

function borrar_benef() {
	return confirm("¿Está seguro de que quiere borrar este Beneficiario?")
}

function esDigito(sChr){
var sCod = sChr.charCodeAt(0);
return ((sCod > 47) && (sCod < 58));
}
function valSep(oTxt){
var bOk = false;
bOk = bOk || ((oTxt.value.charAt(2) == "-") && (oTxt.value.charAt(5) == "-"));
bOk = bOk || ((oTxt.value.charAt(2) == "/") && (oTxt.value.charAt(5) == "/"));
return bOk;
}
function finMes(oTxt){
var nMes = parseInt(oTxt.value.substr(3, 2), 10);
var nRes = 0;
switch (nMes){
case 1: nRes = 31; break;
case 2: nRes = 29; break;
case 3: nRes = 31; break;
case 4: nRes = 30; break;
case 5: nRes = 31; break;
case 6: nRes = 30; break;
case 7: nRes = 31; break;
case 8: nRes = 31; break;
case 9: nRes = 30; break;
case 10: nRes = 31; break;
case 11: nRes = 30; break;
case 12: nRes = 31; break;
}
return nRes;
}
function valDia(oTxt){
var bOk = false;
var nDia = parseInt(oTxt.value.substr(0, 2), 10);
bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));
return bOk;
}
function valMes(oTxt){
var bOk = false;
var nMes = parseInt(oTxt.value.substr(3, 2), 10);
bOk = bOk || ((nMes >= 1) && (nMes <= 12));
return bOk;
}
function valAno(oTxt){
var bOk = true;
var nAno = oTxt.value.substr(6);
bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));
if (bOk){
for (var i = 0; i < nAno.length; i++){
bOk = bOk && esDigito(nAno.charAt(i));
}
}
return bOk;
}
function valFecha(oTxt){
var bOk = true;
if (oTxt.value != ""){
bOk = bOk && (valAno(oTxt));
bOk = bOk && (valMes(oTxt));
bOk = bOk && (valDia(oTxt));
bOk = bOk && (valSep(oTxt));
if (!bOk){
alert("Fecha inválida");
oTxt.value = "";
oTxt.focus();
}
}
}

function reporte_listo() {
	return confirm("¿El reporte esta impreso correctamente?")
}

function valret(form1) {
//  alert(form1.ret_socio.value-form1.ohab_prof.value);
//	alert(form1.ret_empr.value-form1.ohab_empr.value);
//	alert(form1.ret_volu.value-form1.ohab_extr.value);
//	alert(form1.ret_capi.value-form1.ohab_capi.value);
//	alert(form1.ret_socio.value);
//	alert(form1.ohab_prof.value);
	if ((parseFloat(form1.ret_socio.value)+parseFloat(form1.ret_empr.value)+parseFloat(form1.ret_volu.value)+parseFloat(form1.ret_capi.value)) > (parseFloat(form1.ohab_prof.value)+parseFloat(form1.ohab_empr.value)+parseFloat(form1.ohab_extr.value)+parseFloat(form1.ohab_capi.value))) {
		t1=(parseFloat(form1.ohab_prof.value)+parseFloat(form1.ohab_empr.value)+parseFloat(form1.ohab_extr.value)+parseFloat(form1.ohab_capi.value));
		alert(t1);
		alert("El total de ahorros a retirar no puede ser mayor a "+t1);
		return false
	}
	else if (parseFloat(form1.ret_socio.value) > parseFloat(form1.ohab_prof.value)) {
				alert("Existe un error en los montos de retiros individuales de socio");
				return false }
			else if (parseFloat(form1.ret_empr.value) > parseFloat(form1.ohab_empr.value)) {
						alert("Existe un error en los montos de retiros individuales de aportes");
						return false }
				else if (parseFloat(form1.ret_volu.value) > parseFloat(form1.ohab_extr.value)) {
						alert("Existe un error en los montos de retiros individuales voluntarios");
						return false }
					else if (parseFloat(form1.ret_capi.value) > parseFloat(form1.ohab_capi.value)) {
						alert("Existe un error en los montos de retiros individuales de dividendos");
						return false }
					else if (form1.motivo.value == "") {
						alert("Debe especificar un motivo para los retiros de ahorros");
						return false
						}
						else if (form1.observa1.value == "") {
							alert("Debe especificar alguna observacion para los retiros de ahorros");
							return false
						} 
						else return confirm("¿Procede a realizar el retiro por la cantidad de Bs.F "+t1+" ("+num2letras(t1)+"?")
	return true
}

function valret1(form1) {
//	alert(parseFloat(form1.mretirar.value));
//	alert(parseFloat(form1.moretiro.value));
	if (parseFloat(form1.mretirar.value) > parseFloat(form1.moretiro.value)) {
		alert("El total a retirar no puede ser mayor a "+form1.moretiro.value);
		return false;
	}

return true;
}

function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.post("rpc.php", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup
	
function fill(thisValue) {
	$('#inputString').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}

function lookuph(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.post("rpc_hist.php", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup
	
function fillh(thisValue) {
	$('#inputString').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}

function lookup2(inputString2) {
	if(inputString2.length == 0) {
		// Hide the suggestion box.
		$('#suggestions2').hide();
	} else {
		$.post("rpc2.php", {queryString: ""+inputString2+""}, function(data){
			if(data.length >0) {
				$('#suggestions2').show();
				$('#autoSuggestionsList2').html(data);
			}
		});
	}
} // lookup
	
function fill2(thisValue) {
	$('#inputString2').val(thisValue);
	setTimeout("$('#suggestions2').hide();", 200);
}

function lookup3(inputString3) {
	if(inputString3.length == 0) {
		// Hide the suggestion box.
		$('#suggestions3').hide();
	} else {
		$.post("rpc3.php", {queryString: ""+inputString3+""}, function(data){
			if(data.length >0) {
				$('#suggestions3').show();
				$('#autoSuggestionsList3').html(data);
			}
		});
	}
} // lookup
	
function fill3(thisValue) {
	$('#inputString3').val(thisValue);
	setTimeout("$('#suggestions3').hide();", 200);
}

function valact1(form1) {
	if ((form1.nactivo1.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.nactivo1.value == "") form1.nactivo1.focus()
/*
*/
return false
	}
	return true
}

function valact0(form1) {
	if ((form1.observacion.value == "") || (form1.descrip.value == "") || (form1.costo.value == "")|| (form1.eldpto.value == "") || (form1.comprobant.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.observacion.value == "") form1.observacion.focus()
		if (form1.descrip.value == "") form1.descrip.focus()
		if (form1.costo.value == "") form1.costo.focus()
		if (form1.eldpto.value == "") form1.eldpto.focus()
		if (form1.comprobant.value == "") form1.comprobant.focus()
/*
*/
return false
	}
	if (form1.costo.value == 0) {
			alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
				return false } 
	return true
}

function valact2(form1) {
	if ((form1.observacion.value == "") || (form1.descrip.value == "") || (form1.costo.value == "")|| (form1.eldpto.value == "") || (form1.com_fecha.value == "" ) || (form1.comprobant.value == "" ))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio ("+form1.com_fecha.value+")")
		if (form1.observacion.value == "") form1.observacion.focus()
		if (form1.descrip.value == "") form1.descrip.focus()
		if (form1.costo.value == "") form1.costo.focus()
		if (form1.eldpto.value == "") form1.eldpto.focus()
		if (form1.com_fecha.value == "") form1.com_fecha.focus()
		if (form1.comprobant.value == "") form1.comprobant.focus()
/*
*/
return false
	}
		if (form1.costo.value == 0){
			alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
				return false } 
	return true
}

function lookup4(inputString4) {
	if(inputString4.length == 0) {
		// Hide the suggestion box.
		$('#suggestions4').hide();
	} else {
		$.post("rpc4.php", {queryString: ""+inputString4+""}, function(data){
			if(data.length >0) {
				$('#suggestions4').show();
				$('#autoSuggestionsList4').html(data);
			}
		});
	}
} // lookup
	
function fill4(thisValue) {
	$('#inputString4').val(thisValue);
	setTimeout("$('#suggestions4').hide();", 200);
}

	
function lookup5(inputString5) {
	if(inputString5.length == 0) {
		// Hide the suggestion box.
		$('#suggestions5').hide();
	} else {
		$.post("rpc5.php", {queryString: ""+inputString5+""}, function(data){
			if(data.length >0) {
				$('#suggestions5').show();
				$('#autoSuggestionsList5').html(data);
			}
		});
	}
} // lookup
	
function fill5(thisValue) {
	$('#inputString5').val(thisValue);
	setTimeout("$('#suggestions5').hide();", 200);
}

function valact3(form1) {
	if ((form1.observacion1.value == "") || (form1.fechazz.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.observacion1.value == "") form1.observacion1.focus()
		if (form1.fechazz.value == "") form1.fechazz.focus()
/* 
*/
return false
	}
		if ((form1.fechazz.value) == (form1.mini.value)){
			alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
				return false } 
	return true
}


function valpre(form1) {
if (parseFloat(form1.cuota.value) == 0)  {
	alert("Debe calcular primero el monto de la cuota"); return false; }
else 
if (parseFloat(form1.monpre_sdp.value) <= 0)  {
	alert("El monto del prestamo debe ser mayor a cero (0)"); return false; }
else if (parseFloat(form1.monpre_sdp.value) > parseFloat(form1.elmaximo.value)) 
		return confirm("El monto del prestamo sobrepasa el monto disponible. Continua???");
return true
}

function valpreres(form1) {
if (parseFloat(form1.netoarecibir.value) <= 0)  {
	alert("El Neto a recibir no puede ser menor o igual a 0"); return false; }
	else return confirm("Están correctos los montos a descontar del préstamo ???");
return true
}

function valajuste(form1) {
if (parseFloat(form1.montoprestamo.value) < 0)  {
	alert("El monto a adicionar no puede ser menor o igual a 0"); return false; }
	else if (parseFloat(form1.cuota.value) <= 0)  {
		alert("El monto de la cuota no puede ser menor o igual a 0"); return false; }
		else return confirm("Están correctos los montos a ajustar del préstamo ???");
return true
}

function valfiadores(form1) {
if (parseFloat(form1.monto_fianza.value) <= 0)  {
	alert("El monto de la fianza no puede ser menor o igual a 0"); return false; }
	else if (form1._unacedula.value == '') {
		alert("No puede quedar la cedula en blanco"); return false; }
	else return confirm("Están correctos los datos del fiador ???");
return true
}

function conf_elim_fiadores() {
	return confirm("¿Está seguro de que quiere borrar este fiador?")
}

function realizo_abono_banco() {
	return confirm("¿Realizo satisfactoriamente la impresion de todos los listados?")
}

function realizo_abono() {
	return confirm("¿Realizo satisfactoriamente la impresion de todos los listados (Cuotas y Amortizacion/Capital)?")
}

function realizar_abono() {
	return confirm("¿Desea Realizar el abono a los préstamos y los asientos contables?")
}

function realiza_asiento_montepio() {
	return confirm("¿Desea Realizar el asientos contables?")
}

function valfech(form1) {
	if ((form1.MiTimesTamp.value) < (form1.MiTimesTamp1.value))
	{
		alert("No puede adelantar Fecha de Depreciación")
		/*
*/
return false
	}
	return true
}


function imprimir() {
	return confirm("¿Ya imprimio el listado?")
}

function valtipre(form1) {
	if ((form1.descr_pres.value == "") || (form1.desc_cor.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.descr_pres.value == "") { form1.descr_pres.focus();  return false; }
		if (form1.desc_cor.value == "") { form1.desc_cor.focus(); return false; }
	}
	return true
}

function lookup6(inputString6) {
	if(inputString6.length == 0) {
		// Hide the suggestion box.
		$('#suggestions6').hide();
	} else {
		$.post("rpc6.php", {queryString: ""+inputString6+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup
	
function fill6(thisValue) {
	$('#inputString6').val(thisValue);
	setTimeout("$('#suggestions').hide();", 400);
}

function valdep(form1) {
	if ((form1.nombre.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.nombre.value == "") form1.nombre.focus()
/*
*/
return false
	}
	return true
}

function valsaldo(form1) {
	if ((form1.saldo_bancos.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.saldo_bancos.value == "") form1.saldo_bancos.focus()
/*
*/
return false
	}
	return true
}

function explicacion_cheque (form1) {
	if ((form1.explicacion.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.explicacion.value == "") form1.explicacion.focus()
/*
*/
return false
	}
	return true
}


function conceptos(form1) {
	if ((form1.nombre.value == "") || (form1.observacion.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.nombre.value == "") form1.nombre.focus()
		if (form1.observacion.value == "") form1.observacion.focus()
/*
*/
return false
	}
	return true
}

function conciliacion(form1) {
	if ((form1.saldo_bancos.value == "") || (form1.saldo_bancos.value == "0.00"))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio");
		if (form1.saldo_bancos.value == "") form1.saldo_bancos.focus()
			return false;
	}
/*
	alert("hola"); 
*/
	alert("("+form1.diferenciacon.value+")"); 
	alert("("+form1.diferencia.value+")"); 
	if (redondear(parseFloat(form1.diferencia.value),2) != redondear(parseFloat(form1.diferenciacon.value),2))
	{
		alert("No se puede realizar conciliación")
    return false
	}
	return true
}


function redondear(cantidad, decimales) {
var cantidad = parseFloat(cantidad);
var decimales = parseFloat(decimales);
decimales = (!decimales ? 2 : decimales);
return Math.round(cantidad * Math.pow(10, decimales)) / Math.pow(10, decimales);0
}

/*
function redondear(cantidad, decimales) {
{
var original=parseFloat(numero);
var result=Math.round(original*100)/100 ;
return result;
}
*/

function valban(form1) {
	var mensaje = parseFloat(form1.nro_cta_ba.value);
	var micadena=String(mensaje); 
	var numeroLetras = micadena.length; // numeroLetras = 10
	
	if ((form1.nombre.value == "")|| (form1.nro_cta_ba.value == "") ||  (form1.cue_banco.value == ""))
	{
			alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio ("+numeroLetras+")")
		if (form1.nombre.value == "") form1.nombre.focus();  
		if (form1.nro_cta_ba.value == "")  form1.nro_cta_ba.focus(); 
		if (form1.cue_banco.value == "")  form1.cue_banco.focus(); 
		
return false
	}
		if ((numeroLetras < 20)){
			alert("Se debe completar los 20 digitos")
				return false } 
	return true
}

function valche(form1) {
	if ((form1.desde.value == "")|| (form1.hasta.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.desde.value == "") form1.desde.focus()
		if (form1.hasta.value == "") form1.hasta.focus()
/*
*/
return false
	}
        if ((form1.desde.value)>=(form1.hasta.value)){
			alert("NO estan correctos los NUMEROS DE CHEQUES")
				return false } 
	return true
}

function valrangofecha(form1) {
	if ((form1.desde.value == "")||(form1.hasta.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.desde.value == "") form1.desde.focus()
		if (form1.hasta.value == "") form1.hasta.focus()
/*
*/
return false
	}
     return true
}

function valrangocheque(form1) {
	if ((form1.chequedesde.value) > (form1.chequehasta.value))
	{
		alert("NO estan correctos los NUMEROS DE CHEQUES")
/*
*/
return false
	}
     return true
}

function valobs(form1) {
	if (form1.observacion.value == "")
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.observacion.value == "") form1.observacion.focus()
/*
*/
return false
	}
	return true
}

function valben(form1) {
	var mensaje = form1.beneficiario.value;
	var numeroben = mensaje.length; // numeroLetras = 10
	if ((form1.beneficiario.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio ("+numeroben+")")
		if (form1.beneficiario.value == "") form1.beneficiario.focus()
return false
	}
		if ((numeroben < 4)){
			alert("Se debe completar con más de 4 digitos")
				return false } 
	return true
}

function valcar(form1) {
	if ((form1.cuenta1.value == "") || (form1.descripcion.value == "") || (form1.elmonto.value == ""))
	{
		alert("Los datos marcados con asterisco (*) debe ser llenados con caracter obligatorio")
		if (form1.cuenta1.value == "") form1.cuenta1.focus()
		if (form1.descripcion.value == "") form1.descripcion.focus()
		if (form1.elmonto.value == "") form1.elmonto.focus()
/*
*/
return false
	}
    if ((form1.elmonto.value)<= 0){
			alert("El monto debe ser mayor que 0")
				return false } 
	return true
}

function ventana_pdf(numero,codigo,nro,nombre, plantilla)
{
    
//	mi_ventana = window.open("cheimpr_pdf.php?numero=" + numero + "&codigo=" + codigo, "","width=50,height=50,left=5,top=5,scrollbar=no,menubar=no,statusbar=no,status=no,resizable=YES,location=NO,toolbar=NO,personalbar=NO")
	mi_ventana = window.open(plantilla + "?numero=" + numero + "&codigo=" + codigo, "","width=50,height=50,left=5,top=5,scrollbar=no,menubar=no,statusbar=no,status=no,resizable=YES,location=NO,toolbar=NO,personalbar=NO")
	var agree=confirm ("¿La impresión esta correcta?");
    
	if (agree)
	{
	//alert("agree("+agree+")")
	return true
	}
	
	else 
    { 
	//alert("agree("+agree+")") 
	var agree1=confirm ("¿Desea anular el cheque?");
    	  
			if (agree1)
             {
			 history.go(location="http://cappobck/cajaweb/cheimpr.php?accionIn=Anular_Cheque&codigo=" + nro + "&numero=" + numero + "&nombre=" + nombre)
			 //return agree
			 }
			
			else 
			 {
			 ventana_pdf(numero,codigo,nro,nombre); 
			 return false   
			 //alert("agree1("+agree1+")") 
			 }
	}
}


function lookup7(inputString5) {
	if(inputString5.length == 0) {
		// Hide the suggestion box.
		$('#suggestions5').hide();
	} else {
		$.post("rpc7.php", {queryString: ""+inputString5+""}, function(data){
			if(data.length >0) {
				$('#suggestions5').show();
				$('#autoSuggestionsList5').html(data);
			}
		});
	}
} // lookup
	
function fill7(thisValue) {
	$('#inputString5').val(thisValue);
	setTimeout("$('#suggestions5').hide();", 200);
}

function valprecierre(form1) {
	
	if (form1.asiento.value == "" || form1.fecha.value == "")
	{
		alert("Completa todos los campos.")
		form1.asiento.focus()
		return false
	}
/*
	if (form1.asiento.value > 9999999000) {
		alert("Nº Asiento ha de ser menor o igual que 9.999.999.000.")
		form1.asiento.focus()
		return false
	}
*/
	return true
}

function lookup_socios(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.post("rpc_socios.php", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup
	
function fill_socios(thisValue) {
	$('#inputString').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}

