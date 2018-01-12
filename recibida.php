<?php

header('Content-Type: application/json');//esta lleva la cantidad de registros que se encontraron en la consulta
$arregloRecibida = array() ;

$login  = ""; $tipo  = ""; $id = "";

$login	= (!empty($_POST['login']))	? $_POST['login']	: '' ;
$tipo	= (!empty($_POST['tipo']))	? $_POST['tipo']	: '' ;
$id		= (!empty($_POST['id']))	? $_POST['id']		: '' ;

if($tipo == "listar"){
	for($i = rand(1,10) ; $i < rand(298,944); $i++ ){
		if($i %rand(1,5)==0  ){
			$arregloRecibida[] = array("id"=>$i,"resumen"=>"el resumen que se le da a la aplicacion ".$i." valores ".rand(1,841484581));
		}
	}
}
else if($tipo == "detalle" || $_REQUEST['tipo'] == "niko"){
	$arregloRecibida[] = array(
		"Resumen"=>"el resumen que se le da a la aplicacion ".$i." valores ".rand(1,841484581),
		"Detalle"=>"detalle ".generateRandomString(rand(1,50)),
		"Fecha Inicial"=>date("Y/m/d h:m:s"),
		"Dependencia"=>"valores radom".rand(185,94851),
		"Imagenes"=>array(
			"http://3.bp.blogspot.com/-HiWHxDwh6TE/TrlrCVirYbI/AAAAAAAAAdU/-8d44aYfaVo/s1600/Mangekyou+Sharingan.png",
			"http://i.blogs.es/e2c088/mr_icons/650_1200.png",
			"http://www.interdigital.es/portals/0/img/icono_drupal.png",
			"https://www.ecured.cu/images/9/95/DIABLE.JPG",
			"https://gutech.files.wordpress.com/2008/11/iconpack.jpg",
			"http://congresorh.com/wp-content/uploads/2014/10/contenedor-iconos_256-copy.png",
			"http://www.mancera.org/wp-content/uploads/2011/03/TUX-terminal-y-volumen-500x487.jpg",
			"http://ivanmiranda.me/html/images/blog/angular.png",
			"http://blog.falafel.com/wp-content/uploads/2015/01/JS6_Logo.png",
			"https://lenguajehtml.com/img/html5-logo.png",
			"http://webdesign.konstantin-peterson.de/images/icons/jquery-logo.png",
			"https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/PHP-logo.svg/200px-PHP-logo.svg.png",
			"https://articles-images.sftcdn.net/wp-content/uploads/sites/2/2014/11/Android-Broken.jpg",
			"https://pbs.twimg.com/profile_images/762369348300251136/5Obhonwa.jpg"),
		"Datos nikolas "=>generateRandomString(589419)."||".generateRandomStringFor(34593445,100)
		);
}

function generateRandomString($length = 10) { 
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
} 

function generateRandomStringFor($length,$cantidad) { 
	$valore = "";
    for ($i=0; $i < $cantidad; $i++) { 
    	$valore .= generateRandomString($length);
    }
    return $valore;
} 

//echo str_replace($$search, $replace,json_encode($arregloRecibida)); 
echo json_encode($arregloRecibida);

?>