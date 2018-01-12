<?php

error_log(serialize($_REQUEST));

header('Content-Type: application/json');//esta lleva la cantidad de registros que se encontraron en la consulta
$arregloPermitidos = array("niko" => array("usuario"=>"nikolasPro","ciudad"=>"Bogota D.C."),"nikolas"=>array("usuario"=>"niko","ciudad"=>"Amazonas"));
$arregloInfo = array();

//echo serialize($arregloPermitidos);
if(!empty($_REQUEST['login']) && !empty($_REQUEST['pass']) && $_REQUEST['login'] == "nikolas"  /*&& in_array($_REQUEST['login'], $arregloPermitidos*/){
	$arregloInfo['usuario']  = array('ingreso' => 'true' , 'login' => $_REQUEST['login'], 'pass' => $_REQUEST['pass'] , "usuario"=>$arregloPermitidos['nikolas']["usuario"],"ciudad" => $arregloPermitidos['nikolas']["ciudad"]);
		echo json_encode($arregloInfo);
	//echo json_encode(array("login" => "one" , "pass" => "****"));
}
else
{
	$arregloInfo['usuario']  = array('ingreso' => 'false');
	echo json_encode($arregloInfo);
}


?>