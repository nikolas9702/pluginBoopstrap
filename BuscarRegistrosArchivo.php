<?php
// ----------------------------
// Registra variables de sesión
// ----------------------------
session_start();
// ----------------------------
include_once ($_SERVER["DOCUMENT_ROOT"].'/inicio_sevenet.php');
include (DIRECTORIOINICIO.'/docusevenet/classbd.php');
include (DIRECTORIOINICIO.'/docusevenet/classestilo.php');
include (DIRECTORIOINICIO.'/docusevenet/admon/valida_reging.php');

// ----------------------------
// crea instancia de objeto  a la clase Bd()
// ----------------------------
include (DIRECTORIOINICIO.'/docusevenet/conexion.php');
include (DIRECTORIOINICIO.'/docusevenet/funciones_fecha.php');
include (DIRECTORIOINICIO.'/docusevenet/miscelanea.php');
require_once (DIRECTORIOINICIO."/docusevenet/general/clase/Cadena.php");
//Clases de Archivo
include_once (DIRECTORIOINICIO.'/docusevenet/archivo/clase/SeriesDocumentales.php');
include_once (DIRECTORIOINICIO.'/docusevenet/archivo/clase/SeriesAsuntos.php');
//----------------------------//


/**
 * Controlador para cargar los listados de Archivo
 *
 * @package archivo
 * @access 	public
 * @version	0.17
 * @author 	Mauricio Peña
 * @since  	2014/04/09
 *
 */
	
function traerNombrePlantilla($idArchivo)
{
	global $conex_fich, $idDoce;

	$nombrePlantilla = "";
	$conex_fich->campos = "d.id_doce, d.nombre";
	$plantillas = $conex_fich->consultaSql_Condi("d.id_doce = di.id_doce and di.id_indice = ai.id_indice and ai.id_archivo = '".$idArchivo."' group by d.id_doce, d.nombre","documentos_especiales d,documentos_especiales_indices di,archivo_indices ai");
	$plas = $conex_fich->numRegistros($plantillas);
	while($arrayDocE = $conex_fich->arraySql($plantillas))
	{
		$idDoce = $arrayDocE["id_doce"];
		$nombrePlantilla =  $arrayDocE["nombre"];
	}
	return $nombrePlantilla;
}

function verificarMasElementos($idArchivo)
{
	global $conex_fich;

	if (!strcmp($conex_fich->tipo, "mssql"))
	{
		$conex_fich->campos = "top 1 id_archivo";
	}
	else
	{
		$conex_fich->campos = "id_archivo";
	}

	if (!strcmp($conex_fich->tipo, "oracle"))
	{
		$resultadoCantidad  = $conex_fich->consultaSql_Condi("id_padre = ".$idArchivo." and rownum < 2","archivo");
	}
	elseif(!strcmp($conex_fich->tipo, "mssql"))
	{
		$resultadoCantidad  = $conex_fich->consultaSql_Condi("id_padre = ".$idArchivo,"archivo");
	}
	elseif(!strcmp($conex_fich->tipo, "mysql"))
	{
		$resultadoCantidad  = $conex_fich->consultaSql_Condi("id_padre = ".$idArchivo." limit 0,1","archivo");
	}
	else
	{
		$resultadoCantidad  = $conex_fich->consultaSql_Condi("id_padre = ".$idArchivo." limit 1","archivo");
	}
	//$resultadoCantidad  = $conex_fich->consultaSql_Condi("id_padre = ".$idArchivo,"archivo");
	return $conex_fich->numRegistros($resultadoCantidad);
}


class BuscarRegistrosArchivo
{
	public static  function main ()
	{
		global $conex_fich, $idDoce;

		if(!is_resource($conex_fich->con)){$conex_fich->conexion();}

		$idDependencia 		= !empty($_POST['idDependencia']) ? $_POST['idDependencia'] : 0;
		$idPadre 			= !empty($_POST['idPadre']) ? $_POST['idPadre'] : 0;
		$archivoActual 		= !empty($_POST['archivoActual']) ? $_POST['archivoActual'] : '';
		$banderaDesdeWF		= !empty($_POST['banderaDesdeWF']) ? $_POST['banderaDesdeWF'] : '';//JOSE CARREÑO: Se recibe dato proveniente desde WF entonces no mostrará los checks ni las propiedades...
		
		if(!isset($idDependencia) || trim($idDependencia) == '')
		{
			exit();
		}

		if($archivoActual == "gestion")
		{
			$idArchivoActual = 1;
		}
		if($archivoActual == "central")
		{
			$idArchivoActual = 2;
		}
		if($archivoActual == "historico")
		{
			$idArchivoActual = 3;
		}
		$pagina 			= isset($_POST['pagina']) ? $_POST['pagina'] : '';
		$cant 				= isset($_POST['cantidadRegistros']) ? $_POST['cantidadRegistros'] : '';

		$_SESSION["cantidadRegistros"] = $cant;
		$_SESSION['archivo']['pagina'] = $pagina;

		$user = "";
		$condicionPermisos = "";
		$pRegistrar = 0;
		$registroIndividual = false;

		if(isset($_SESSION['la']) && $_SESSION['la'] != "")
		{
			$pRegistrar = 1;
			$mostrarPermisos = false;
			$user = $_SESSION['la'];
		}
		if(isset($_SESSION['l']) && $_SESSION['l'] != "")
		{
			$user = $_SESSION['l'];

			//Consulta del permiso de registrar sobre el elemento para mostrar los botones de accion
			$nombreTablaPermisos = "archivo";
			$condicionConsultaPermisos = "id_archivo = '".$idPadre."'";
			if($idPadre == 0)
			{
				$condicionConsultaPermisos = "archivo_actual = '".$archivoActual."' and id_dependencia = '".$idDependencia."'";
				$nombreTablaPermisos = "dependencia";
			}
			$conex_fich->campos = "registrar, administrar ";
			$resPerm = $conex_fich->consultaSql_Condi($condicionConsultaPermisos ,$nombreTablaPermisos);
			while ($regPerm = $conex_fich->arraySql($resPerm))
			{
				if(strpos( $regPerm["registrar"] ,'|'.$user.'|') !== false)
				{
					$pRegistrar = 1;
				}
			}

			$mostrarPermisos = false;
			$condicionPermisos = " and consultar like '%|".$user."|%'";
		}

		$condicion = " where ";

		$tmpCondicion = "";
		if($idArchivoActual != '')
		{
			$tmpCondicion = " id_archivo_actual = '".$idArchivoActual."' ";
		}

		if($idPadre == 0)
		{
			$condicion .= " id_dependencia = ".$idDependencia." and id_padre = '0' and " . $tmpCondicion;
		}
		else
		{
			$condicion .= " id_padre = ".$idPadre." and " . $tmpCondicion;
			//$condicion .= " id_padre = ".$idPadre." and transferido is null";
		}
		$condicion_tabla2 = $condicion;

		//Consulta Paginación
		$limite1 = 0;
		if($pagina > 1)
		{
			$limite1 = $cant*($pagina-1);
		}
		$campo_ordenar = "id_archivo desc";
		$tabla1 = "archivo a";
		if($registroIndividual)
		{
			$campos_tabla2 = "id_archivo";
		}
		else
		{
			$campos_tabla2 = "id_archivo, nombre, fgraba, id_unidad_conservacion, fmodifica, estado, id_serie, ffinal, id_flujo";
		}
		$tabla2 = "archivo a";
		//Traer las imagenes de las unidades
		$arrayUC = array();
		$conex_fich->campos = "id_unidad, imagen";
		$resOI = $conex_fich->consultaSql("unidad_conservacion");
        while ($regOI = $conex_fich->arraySql($resOI))
		{
			$arrayUC[$regOI["id_unidad"]] = $regOI["imagen"];
		}
		$arrayDocStick = array();


		require_once (DIRECTORIOINICIO.'/docusevenet/consulta_paginador_condi.php');
		//Fin Consulta Paginación.

		$cadenaResultados = "";
		$cantidadRegistros = $limite1;
		$ima;
		
		/*traer los flujos que el usuario es responsable*/
		$flujos_usu_resp = array();
		if(isset($user) && trim($user) != '' && !empty($_SESSION['l']))
		{
			$conex_fich->campos = "f.id_flujo";
			$re = $conex_fich->consultaSql_Condi("f.estado = 'Abierto' AND fae.finalizado = 0 AND (fae.login = '" . $user . "' OR (fae.id_actividad IN (SELECT id_actividad FROM flujo_responsables_actividad WHERE login = '" . $user . "')) AND ( fae.login = '' or fae.login is null ))", "flujo f INNER JOIN flujo_act_estado fae ON f.id_flujo = fae.id_flujo INNER JOIN flujo_actividad fa ON fae.id_actividad = fa.id_actividad");
			while($row = $conex_fich->arraySql($re))
			{
				array_push($flujos_usu_resp, $row['id_flujo']);
			}
		}

		while($array = $conex_fich->arraySql($r))
		{
			$cantidadRegistros++;
			$mostrarDetalle = false;
			if($registroIndividual)
			{
				$idArchivo 			= $array["id_archivo"];
			
			
				/*
				JOSE CARREÑO: Se realiza nueva consulta para traer el numero de imagenes que tiene el documento...
				*/
				$conex_fich->campos = "imagenes";
   				$rg_a = $conex_fich->arraySql($conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo ,"archivo"));
    			$numero_imagenes = $rg_a['imagenes'];
				
				$conex_fich->campos = "nombre, fgraba, id_unidad_conservacion, fmodifica, estado,finicial,transferido, id_serie, ffinal, id_flujo, imagenes ";
				$resRegInd = $conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo ,"archivo");
				while ($regRegInd = $conex_fich->arraySql($resRegInd))
				{
					$nombreElemento 	= $regRegInd["nombre"];
					$fGraba 			= $regRegInd["fgraba"];
					$fModifica 			= $regRegInd["fmodifica"];
					$idUnidad 			= $regRegInd["id_unidad_conservacion"];
					$estado 			= $regRegInd["estado"];
					$idSerie 			= $regRegInd["id_serie"];
					$fFinal 			= $regRegInd["ffinal"];
					$fInicial 			= $regRegInd["finicial"];
					$transferido 		= $regRegInd["transferido"];
					$idFlujo 			= $regRegInd["id_flujo"];
				}
			}
			else
			{
				$idArchivo 			= $array["id_archivo"];
				$nombreElemento 	= $array["nombre"];
				$fGraba 			= $array["fgraba"];
				$fModifica 			= $array["fmodifica"];
				$idUnidad 			= $array["id_unidad_conservacion"];
				$estado 			= $array["estado"];
				$idSerie 			= $array["id_serie"];
				$fFinal 			= $array["ffinal"];
				$fInicial 			= isset($array["finicial"]) ? $array["finicial"] : '';
				$transferido 		= isset($array["transferido"]) ? $array["transferido"] : '';
				$idFlujo 			= $array["id_flujo"];
				/*
				JOSE CARREÑO: Se realiza nueva consulta para traer el numero de imagenes que tiene el documento...
				*/
				$conex_fich->campos = "imagenes";
   				$rg_a = $conex_fich->arraySql($conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo ,"archivo"));
    			$numero_imagenes = $rg_a['imagenes'];
			}

			$nombrePlantilla	= traerNombrePlantilla($idArchivo);
			$permisos			= "Permisos";
			$caracteristicas	= "&nbsp;";
			$separador			= "";
			$acciones			= "";
			$claseElemento		= "";
			$claseFila 			= "";
			
			
			
			$iconoDetalle = DIRECTORIOINICIOJS."/docusevenet/img/iconos/verDetalle.png";
			$imgMasElementos = "<img src='".DIRECTORIOINICIOJS."/docusevenet/img/maselementos.gif' border='0'>";
			$cadenaArchivo = strrev(base64_encode(Cadena::encriptarCadena(date("Ymd").";".$idArchivo.";".$user.";".date("H:i:s"))));

			if($idUnidad == 0)
			{
				$claseElemento = "documento";
				$imgUnidad = DIRECTORIOINICIOJS."/docusevenet/img/iconos/documento3.png";
				$imgMasElementos = "&nbsp;";
				$mostrarDetalle = true;
				if($numero_imagenes == 0)//Se establece si se imprime icono sin imagen
				{
					$iconoDetalle = DIRECTORIOINICIOJS."/docusevenet/img/iconos/verDetalleSinImagen.png";
				}
			}
			else
			{
				$idUnidaduc = !empty($arrayUC[$idUnidad]) ? $arrayUC[$idUnidad] : '';
				$claseElemento = "unidad";
				$imgUnidad = DIRECTORIOINICIOJS."/docusevenet/imagenes_unidades/".$idUnidaduc;
				$mostrarDetalle = true;

				if(verificarMasElementos($idArchivo) == 0)
				{
					$imgMasElementos = "&nbsp;";
				}

//Determinar si el archivo N está asociado a una Tabla de valoración
///////////////////////////////////////////////////////////////////////
				$conex_fich->campos = "id_asunto";
				$query = $conex_fich->arraySql($conex_fich->consultaSql_Condi("id_archivo='$idArchivo' AND id_asunto is not null", "archivo_indices"));
				if($query["id_asunto"] != "" && $query["id_asunto"] != 0)
				{
					$claseasunto = new SeriesAsuntos();
					$claseasunto->setIdAsunto($query["id_asunto"]);
					$claseasunto->consultarserieasunto();
					$nombreAsunto = " ".$claseasunto->getNombre();
										
					$caracteristicas .= "<a title='".utf8_encode($nombreAsunto)."' class='link'>TVD </a>";
					
					if($fFinal and $fFinal != "")
					{
						$diferencia = explode(",", calcularDiferencia($fFinal));
						$anosTransc = isset($diferencia[0]) ? $diferencia[0] : 0;
						$mesesTransc = isset($diferencia[1]) ? $diferencia[1] : 0;
						$diasTransc = isset($diferencia[2]) ? $diferencia[2] : 0;
						
						$mesesNecesarios = 0;
						
						if($idArchivoActual == 2)
						{
							$mesesNecesarios = $claseasunto->getTiempoCentral();
						}
						$mesesTransc = $mesesTransc + ($anosTransc * 12);
						
						if(($mesesTransc >= $mesesNecesarios && $diasTransc > 0) and $idArchivoActual != 3)
						{
							if(($claseasunto->getTiempoCentral() != 0 and $idArchivoActual != 1) || ($claseasunto->getConservacionTotal() == 1))
							{
								$claseFila = " class='transferir'";
							}
							if($claseasunto->getEliminacion() == 1 )
							{
								$claseFila = " class='eliminar'";
							}
							$conex_fich->campos = "fmodifica,  finicial,ffinal, transferido, id_archivo, archivo_procedente";
							$resRegInd = $conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo ,"archivo");
							
							$fModifical=0; $fFinall=0; $fIniciall=0; $transferidol=0; $procedentel=0;
							while ($regRegInd = $conex_fich->arraySql($resRegInd))
							{//se añade una l al final del nombre de la variable para que no hallan errores posteriores
								$fModifical 		= $regRegInd["fmodifica"];
								$fFinall 			= $regRegInd["ffinal"];
								$fIniciall 			= $regRegInd["finicial"];
								$transferidol 		= $regRegInd["transferido"];
								$procedentel		= $regRegInd["archivo_procedente"];
							}
							$tiempocentral=0; $disposicionFinalEliminacion=0; $conservacionTotal=0; $microDigitalizacion=0; $seleccion=0; $tiempocentral =0; 
							if($query["id_asunto"] != 0)
							{
								$conex_fich->campos = "*";
								$resRegIn = $conex_fich->consultaSql_Condi("id_asunto = '".$query["id_asunto"]."'","asunto");
								while ($regRegIndss = $conex_fich->arraySql($resRegIn))
								{
									$tiempocentral 	= $regRegIndss["tiempo_central"];
									$disposicionFinalEliminacion 	= $regRegIndss["eliminacion"];
									$conservacionTotal 	= $regRegIndss["conservacion_total"];
									$microDigitalizacion 	= $regRegIndss["micro_digitaliza"];
									$seleccion 	= $regRegIndss["seleccion"];
								}
							}
							if($archivoActual == "central")
							{
								$archivoPasar = "Hist&oacute;rico";
								$fFinall = !empty($fFinall) ? $fFinall : '';
								if($fFinall != '' && $transferido == "")
								{
									if($disposicionFinalEliminacion == 1){
										$caracteristicas .= $separador.'<a title="Disposici&oacute;n Final Eliminaci&oacute;n" class="link">DF. E</a>'; 
									}
									else{
										if($tiempocentral != 0){
											$caracteristicas .= $separador.'<a title="Pendiente para transferir al Archivo '.$archivoPasar.'" class="link">PT</a>'; 
										}
										else{
											if($conservacionTotal == 1 ){
												$caracteristicas .= $separador.'<a title="Pendiente para transferir al Archivo Hist&oacute;rico" class="link">PT</a>'; 
											}else{
												if($microDigitalizacion == 1){
													$caracteristicas .= $separador.'<a title="Disposici&oacute;n Final Microfilmacion/Digitalizaci&oacute;n" class="link">DF. MD</a>'; 
												}
												if($seleccion == 1 ){
													$caracteristicas .= $separador.'<a title="Disposici&oacute;n Final Selecci&oacute;n" class="link">DF. S</a>'; 
												}
											}
										}
									}
								}
								if($procedentel == 'central')
								{$caracteristicas .= $separador.'<a title="Devuelto del Archivo Central" class="link">D</a>'; }
								if($procedentel == 'gestion')
								{$caracteristicas .= $separador.'<a title="Devuelto del Archivo Gesti&oacute;n" class="link">D</a>'; }
							}
							if($transferidol == "pcentral")
							{$caracteristicas .= $separador.'<a title="Pendiente por organizar en el Archivo Central" class="link">PO.S</a>'; }
							if($transferidol == "phistorico")
							{$caracteristicas .= $separador.'<a title="Pendiente por organizar en el Archivo Hist&oacute;rico" class="link">PO.I</a>'; }
						}
					}
				}
			
//Determina si el archivo N está asociado a una serie documental
///////////////////////////////////////////////////////////////////////

				if($idSerie and $idSerie > 0)
				{
					$claseSerie = new SeriesDocumentales();
					$claseSerie->setIdSerie ($idSerie);
					$claseSerie->consultarSerieDocumental();
					$nombreSerie = " ".$claseSerie->getNombre();
					$caracteristicas .= "<a title = '".utf8_encode(html_entity_decode($nombreSerie))."' class='link'>TRD</a>";
					$entro_s=1;
					$separador = "-";
					if($fFinal and $fFinal != "")
					{
						$diferencia = explode(",", calcularDiferencia($fFinal));
						//$diferencia = explode(",",$diferencia);
						$anosTransc = isset($diferencia[0]) ? $diferencia[0] : 0;
						$mesesTransc = isset($diferencia[1]) ? $diferencia[1] : 0;
						$diasTransc = isset($diferencia[2]) ? $diferencia[2] : 0;

						$anosNecesarios = 0;
						$mesesNecesarios = 0;

						if($idArchivoActual == 1)
						{
							$mesesNecesarios = $claseSerie->getTiempoGestion();
						}
						if($idArchivoActual == 2)
						{
							$mesesNecesarios = $claseSerie->getTiempoGestion() + $claseSerie->getTiempoCentral();
						}

						$mesesTransc = $mesesTransc + ($anosTransc * 12);

						//if(($anosNecesarios <= $anosTransc) && ($mesesTransc > 0 || $diasTransc > 0) and $idArchivoActual != 3)
						if(($mesesTransc >= $mesesNecesarios && $diasTransc > 0) and $idArchivoActual != 3)
						{
							if(($claseSerie->getTiempoCentral() != 0 and $idArchivoActual != 2) || ($claseSerie->getConservacionTotal() == 1))
							{
								$claseFila = " class='transferir'";
							}
							if($claseSerie->getEliminacion() == 1 )
							{
								$claseFila = " class='eliminar'";
							}
								/*
								*Nicolas gonzalez se agregan if para mostrar procesos faltantes 
								*Actualizaci&ocute;n el archivo 21/12/2015
								*se coloca la impresion despues que halla ingresado en que la fecha ya a trascurrido para una mejor restriccion a lo que se visualiza
								*/
								$conex_fich->campos = "fmodifica,  finicial,ffinal, transferido, id_archivo, archivo_procedente";
								$resRegInd = $conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo ,"archivo");
								$fModifical=0;$fFinall=0;$fIniciall=0;$transferidol=0;$procedentel=0;
								while ($regRegInd = $conex_fich->arraySql($resRegInd))
									{//se añade una l al final del nombre de la variable para que no hallan errores posteriores
										$fModifical 		= $regRegInd["fmodifica"];
										$fFinall 			= $regRegInd["ffinal"];
										$fIniciall 			= $regRegInd["finicial"];
										$transferidol 		= $regRegInd["transferido"];
										$procedentel		= $regRegInd["archivo_procedente"];
									}
								$tiempocentral=0; $disposicionFinalEliminacion=0; $conservacionTotal=0; $microDigitalizacion=0; $seleccion=0; $tiempocentral =0; 
								if($idSerie != 0)
									{
										$conex_fich->campos = "*";
										$resRegIn = $conex_fich->consultaSql_Condi("id_seriedocumental = '".$idSerie."'","serie_documental");
										while ($regRegIndss = $conex_fich->arraySql($resRegIn))
										{
											$tiempocentral 	= $regRegIndss["tiempo_central"];
											$disposicionFinalEliminacion 	= $regRegIndss["eliminacion"];
											$conservacionTotal 	= $regRegIndss["conservacion_total"];
											$microDigitalizacion 	= $regRegIndss["micro_digitaliza"];
											$seleccion 	= $regRegIndss["seleccion"];
										}
									}
								if($archivoActual == "gestion" or  $archivoActual == "central")
								{
									$archivoPasar = ($archivoActual=="gestion") ? $archivoPasar = "central" : $archivoPasar = "Hist&oacute;rico";
									$fFinall = !empty($fFinall) ? $fFinall : '';
									if($fFinall != '' && $transferido == "")
									{
										if($disposicionFinalEliminacion == 1)
										{$caracteristicas .= $separador.'<a title="Disposici&oacute;n Final Eliminaci&oacute;n" class="link">DF. E</a>'; }
										else
										{
											if($tiempocentral != 0)
											{$caracteristicas .= $separador.'<a title="Pendiente para transferir al Archivo '.$archivoPasar.'" class="link">PT</a>'; }
											else
											{
												if($conservacionTotal == 1 )
												{$caracteristicas .= $separador.'<a title="Pendiente para transferir al Archivo Hist&oacute;rico" class="link">PT</a>'; }
												else
												{
													if($microDigitalizacion == 1)
													{$caracteristicas .= $separador.'<a title="Disposici&oacute;n Final Microfilmacion/Digitalizaci&oacute;n" class="link">DF. MD</a>'; }
													if($seleccion == 1 )
													{$caracteristicas .= $separador.'<a title="Disposici&oacute;n Final Selecci&oacute;n" class="link">DF. S</a>'; }
												}
											}
										}
									}
									if($procedentel == 'central')
									{$caracteristicas .= $separador.'<a title="Devuelto del Archivo Central" class="link">D</a>'; }
									if($procedentel == 'gestion')
									{$caracteristicas .= $separador.'<a title="Devuelto del Archivo Gesti&oacute;n" class="link">D</a>'; }
								}
								if($transferidol == "pgestion")
								{$caracteristicas .= $separador.'<a title="Pendiente por organizar en el Archivo Gesti&oacute;n" class="link">PO.G</a>'; }
								if($transferidol == "pcentral")
								{$caracteristicas .= $separador.'<a title="Pendiente por organizar en el Archivo Central" class="link">PO.S</a>'; }
								if($transferidol == "phistorico")
								{$caracteristicas .= $separador.'<a title="Pendiente por organizar en el Archivo Hist&oacute;rico" class="link">PO.I</a>'; }
								#fin modificacion Nicolas Gonzalez 
						}
					}
				}
				else// valida que el registro ya esta para trasferir 
				{
					/**
					*Nicolas gonzalez se agrega Validacion para cuando es a sido transferido muestre a donde se espera que se realice el proceso
					*Actualizaci&oacute;n el archivo 06/09/2016
					*/
					$conex_fich->campos = "transferido,archivo_procedente,archivo_actual";
					$resultadoTransferido = $conex_fich->arraySql($conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo ,"archivo"));
					$transferidol=0;
					$transferidol 		= $resultadoTransferido["transferido"];

					if ($transferidol    == "pgestion" || $transferidol    == "pcentral" || $transferidol    == "phistorico") {
						if($transferidol    == "pgestion")
						{$caracteristicas   .= $separador.'<a title="Pendiente por organizar en el Archivo Gesti&oacute;n" class="link">PO.G</a>'; }
						if($transferidol    == "pcentral")
						{$caracteristicas   .= $separador.'<a title="Pendiente por organizar en el Archivo Central" class="link">PO.S</a>'; }
						if($transferidol    == "phistorico")
						{$caracteristicas   .= $separador.'<a title="Pendiente por organizar en el Archivo Hist&oacute;rico" class="link">PO.I</a>'; }
					}
				}
			}
	
			//Verificación si el elemento esta prestado.
			$conex_fich->campos="fdevolucion";
			$reP = $conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo." AND fdevolucion is null","prestamos");
			if($conex_fich->numRegistros($reP) >= 1)
			{
				$conex_fich->campos="fprestamo, dias";
				$FVence = $conex_fich->arraySql($conex_fich->consultaSql_Condi("id_archivo = ".$idArchivo." AND fprestamo is not null","prestamos"));
				
				$fecha = date_create(fecha_inserta(2, fecha_inserta(2, $FVence["fprestamo"])));
				date_modify($fecha, '+'.$FVence["dias"].' day');
				$fecha_vencida = date_format($fecha, 'Y-m-d');
							
				$mas = "";
				if($fecha_vencida < date("Y-m-d"))
				{
					$claseFila = " class='eliminar'";
					$mas = ", fecha devolución vencida";
				}
				$caracteristicas .= $separador."<a title = 'Prestado$mas' class='link'>P</a>";
			}

			//Construccion de los Iconos de Accion
			$registrar = $pRegistrar;
			$pEnviar = 1;
			if($registrar == 1 and $banderaDesdeWF != 'true')//JOSE CARREÑO: Si es solicitado este archivo desde WF, propiedades no se mostrarán...
			{
				$acciones .= "<a class='propiedad' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/propiedades3a.png' title='Propiedades' width='17' height='17' border='0'></a>&nbsp;";
			}
			//JOSE CARREÑO: Modificación en la validación, si es solicitado desde WF mostrá el detalle en ventana Modal...
			if($mostrarDetalle)
			{
				if ($banderaDesdeWF != 'true'){
					$acciones .= "<a class='detalle' style='cursor:pointer'><img src='".$iconoDetalle."' title='Ver Detalle' width='17' height='17' border='0'></a>&nbsp;";
				}
				else{
					//Se realiza proceso de encriptación del id...
					$user =""; 
					if(isset($_SESSION['la']) && $_SESSION['la'] != "")
					{
						$user = $_SESSION['la'];
					}
					if(isset($_SESSION['l']) && $_SESSION['l'] != "")
					{
					$user = $_SESSION['l'];
					}
					$cadenaIdOrigen = strrev(base64_encode(cadena::encriptarCadena(date("Ymd").";".$idArchivo.";".$user.";".date("H:i:s"))));
					$acciones .='<a style="cursor:pointer"><img src="'.$iconoDetalle.'" title="Ver Detalle" width="17" height="17" border="0" onclick="enviarParametrosDetalle(\'ver_detalle_archivo.php\',\'id_archivo\',\''.$cadenaIdOrigen.'\');"></a>&nbsp;';
				}
			}
			if($pEnviar == 1 and $mostrarDetalle)
			{
				$conex_fich->campos = "id_anotacion";
				$reg_anotacion = $conex_fich->consultaSql_Condi("tipo_correspondencia = 4 and id_correspondencia = ".$idArchivo,"anotaciones");
				if( $row_anotacion = $conex_fich->arraySql($reg_anotacion) )
				{
					$acciones .= "<a class='anotacion' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/con_anotacion.png' title='Agregar Anotaci&oacute;n' width='17' height='17' border='0'></a>&nbsp;";
				}
				else
				{
					$acciones .= "<a class='anotacion' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/anotacion.png' title='Agregar Anotaci&oacute;n' width='17' height='17' border='0'></a>&nbsp;";
				}
				
			}
			if($idFlujo and $idFlujo > 0)
			{
				//JOSE CARREÑO: Se agrega condicional, si este documento es solicitado desde WF la opción Ver detalle del Workflow no se mostrará...
				if($banderaDesdeWF != 'true')
				{
					$acciones .= "<a class='flujo' idFlujo='".$idFlujo."' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/procesos3.png' title='Ver detalle del Workflow' width='17' height='17' border='0'></a>&nbsp;";
				}
				else if(in_array($idFlujo, $flujos_usu_resp))
				{
					$acciones .= "<a onclick='showDialog(".$idFlujo.")' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/wf_pendientes1.png' title='Seguimiento y estado de la actividad' width='17' height='17' border='0'></a>&nbsp;";
				}
			}
			if($mostrarDetalle)
			{
				$acciones .= "<a class='usuarios' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/grupos3.png' title='Ver Usuarios relacionados' width='17' height='17' border='0'></a>&nbsp;";
			}
			if($registrar == 1 and $nombrePlantilla != "")
			{
				if(!isset($arrayDocStick[$idDoce]))
				{
					$conex_fich->campos = "defecto";
					$consultaSticker =  $conex_fich->arraySql($conex_fich->consultaSql_Condi("tabla = 'plantilla_".$idDoce."' and nombre like 'radicado'","perfil_campo"));
					$arrayDocStick[$idDoce] = $consultaSticker["defecto"];
				}
				if($arrayDocStick[$idDoce] == "true")
				{
					$acciones .= "<a idDoce='".$idDoce."' class='sticker' style='cursor:pointer'><img src='".DIRECTORIOINICIOJS."/docusevenet/img/iconos/imprimir3.png' title='Imprimir R&oacute;tulo' width='17' height='17' border='0'></a>&nbsp;";
				}
			}
			//Fin Construccion de los Iconos de Accion

			$fGraba = fecha_inserta(2,$fGraba);
			$fModifica = fecha_inserta(2,$fModifica);

			$cadenaResultados .= "<tr id='".$idArchivo."' ".$claseFila." name='".$cadenaArchivo."' height='24'>";
			//JOSE CARREÑO: Se valida si este archivo es solicitado desde WF no se mostrarán los checks...
			if($banderaDesdeWF != 'true'){
				$cadenaResultados .="<td class='lineas' align='center'><input class='item' id='chk".$idArchivo."'type='checkbox'></td>";
			}
			$nombreElemento = htmlentities(html_entity_decode(html_entity_decode($nombreElemento)));// Se descodifica multiples veces y se codifica una ultima vez y se valida 
			$nombreElemento = preg_replace('/[^a-zA-Z0-9\-\.\,\&\#\;\+\(\)\$\=\%\$\?\¿\@\!\¡\*\ ]/i', ' ', $nombreElemento);// se remplaza los campos que no sean ascii por un espacio

			$cadenaResultados .="<td class='lineas' align='center'>".$imgMasElementos."</td>".
			"<td class='lineas' align='center'><img src='".$imgUnidad."' border='0' width='20' height='20'></td>".
			"<td class='lineas link ".$claseElemento."' align='left' style='cursor:pointer' id='nombre_".$idArchivo."'>".
				utf8_encode(html_entity_decode($nombreElemento))/*Nicolas Gonzalez se descofica el nombre y se valida en utf8 ya que este pasa por ajax para que no tenga problemas */."</td>";
			if($nombrePlantilla != "")
			{
				$cadenaResultados .= "<td class='lineas center tcenter' >".
										"<span class='plantilla link' style='cursor:pointer'>".$nombrePlantilla."</span>".
										"<div id='divPlantilla_".$idArchivo."' style='display:none'></div>&nbsp;".
										"</td>";
			}
			else
			{
				$cadenaResultados .= "<td class='lineas center tcenter' >&nbsp;</td>";
			}
			if(!empty($mostrarPermisos))
			{
				$cadenaResultados .= "<td class='lineas center'>".$permisos."</td>";
			}
				$cadenaResultados .= "<td class='lineas textonormal center'>".$fGraba."</td>".
									"<td class='lineas textonormal center'>".$fModifica."&nbsp;</td>".
									"<td class='lineas textonormal center'>".$caracteristicas."</td>".
									"<td class='lineas textonormal center'><center>".$acciones."</center></td>".//JOSE CARRENO: Se centran las acciones...
								"</tr>";

		}


		//Inicio Tabla Paginación.
		$cadenaPaginador = "";
		if(true)
		{
			$limite = 0;
			$opcionesCantidadRegistros = "<select id='selectCantidadRegistros' class='entradasform'>";
			$valores = array(5, 10, 25, 50, 75, 100, 250);
			for($i=0, $vlen = count($valores); $i < $vlen; $i++)
			{
				if($valores[$i] == $cant) $selected ="selected=\"selected\""; else $selected="";
				$opcionesCantidadRegistros .= "<option value='".$valores[$i]."' ".$selected.">".$valores[$i]."</option>";
			}
			$opcionesCantidadRegistros .= "</select>";
			if($total > 0)
			{
				$limite = $limite1+1;
			}
			$cadenaPaginador.= '<table width="92%" border="0" cellpadding="0" cellspacing="0" class="fondotabla center tcenter" ><tr height="20" class="celdallena"><td width="26%" class="center tcenter" >Registros '.$limite.' al '.$cantidadRegistros.' de '.$total.'</td><td width="7%" class="center tcenter" >';
			if($pagina>1)
			{
				$cadenaPaginador .= '<a id="botonPrimera" style="cursor:pointer" class="link"> < Primera </a>';
			}

			$cadenaPaginador.= '</td><td width="8%" align="center" >';
			if (($limite1-$limite2)>-1)
		 	{
		 		$cadenaPaginador .= '<a id="botonAnterior" style="cursor:pointer" class="link"> < Anterior </a>';
		 	}
			$cadenaPaginador .= '</td><td width="8%" align="center" >';
			if (($limite1+$limite2)<$total)
		 	{
		 		$cadenaPaginador.= '<a id="botonSiguiente" style="cursor:pointer" class="link">Siguiente ></a>';
		 	}
			$cadenaPaginador .= '</td><td width="7%" align="center" >';
            if ($pagina < $paginaT)
			{
				$cadenaPaginador .= '<a id="botonUltima" style="cursor:pointer" class="link"> &Uacute;ltima > </a>';
			}
			$cadenaPaginador .= '</td><td width="21%" align="center"><span>P&aacute;gina '.$pagina.' de '.$paginaT.'</span>&nbsp;&nbsp;&nbsp;';
			$cadenaPaginador .= "<input type='hidden' id='ubicacion_paginas' value='".$pagina."'>";//se adiciona para poder obtener el valor de la ubicacion de la pagina !anotaciones
			$cadenaPaginador .= '</td><td width="23%" align="left">Mostrar '.$opcionesCantidadRegistros.' Registros por P&aacute;gina</td>';
			$cadenaPaginador .= '</tr></table>';

		}
		//Fin Tabla Paginación

		echo json_encode(array('tablaResultados' => $cadenaResultados,
								'tablaPaginador' => $cadenaPaginador,
								'PaginasTotales' => $paginaT,
								'pRegistrar' => $pRegistrar));
	}
}

BuscarRegistrosArchivo::main();

$conex_fich->cierrabd();
?>