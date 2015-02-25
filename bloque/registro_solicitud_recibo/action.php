<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
  
registro.action.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 2 de junio de 2007

*****************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Action de registro de usuarios
* @usage        
******************************************************************************/

//======= Revisar si no hay acceso ilegal ==============
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}
//======================================================

$acceso_db=new dbms($configuracion);
$enlace=$acceso_db->conectar_db();
if (is_resource($enlace))
{
	/*
	foreach ($_REQUEST as $key => $value) 
	{
	echo $key."=>".$value."<br>";
	
	}
	*/
		
	if(isset($_REQUEST['registro']))
	{
		$variable['id_solicitud']=$_REQUEST['registro'];
		//Verificar que el registro que se esta editando en realidad exista
		$cadena_sql=cadena_sql_solicitud($configuracion,"select",$variable);
		$registro=acceso_db_solicitud($cadena_sql,$acceso_db,"busqueda");
		if(is_array($registro))
		{
			unset ($registro);
			unset($variables);
			$usuario=sesion_solicitud($configuracion,$acceso_db,"id_usuario");
			if(is_array($usuario))
			{
				$variables["id_usuario"]=$usuario[0][0];
				$cadena_sql=cadena_sql_solicitud($configuracion,"update",$variables);
				$resultado=acceso_db_solicitud($cadena_sql,$acceso_db,"");
			}
			else
			{
				$resultado=FALSE;			
			}
			
		}
		else
		{
			
			$resultado=FALSE;
		}		
	}
	else
	{
		unset($variables);
		//Si no existe un id registro valido entonces se trata de un registro nuevo o una correccion
		$sesion=sesion_solicitud($configuracion,$acceso_db,"id_usuario");
		if(is_array($sesion))
		{
			$variables["id_usuario"]=$sesion[0][0];
		}
		else
		{
			$variables["id_usuario"]="0";
		}
		
		
		$variables["codigo"]="'".$_REQUEST["codigo"]."'";
		$cadena_sql=cadena_sql_solicitud($configuracion,"select",$variables);		
		$registro=acceso_db_solicitud($cadena_sql,$acceso_db,"busqueda",$variables);
		if(is_array($registro))
		{
			unset ($registro);
			$opciones="pagina=registro_solicitud";
			$opciones.="&opcion=corregir";
			enrutar_solicitud($configuracion,$opciones);	
		}
		else
		{
			unset ($registro);			
			$cadena_sql=cadena_sql_solicitud($configuracion,"insertar",$variables);
			$resultado=acceso_db_solicitud($cadena_sql,$acceso_db,"");
		}
		
	}
	if($resultado)
	{
		unset ($registro);
		$opciones="pagina=administrar_recibo";
		$opciones.="&accion=1";
		$opciones.="&hoja=1";
		$opciones.="&opcion=lista";
		enrutar_solicitud($configuracion,$opciones);	
	}
	else
	{
		unset ($registro);
		echo $cadena_sql;exit;
		$opciones="pagina=error_ingresar_datos";
		enrutar_solicitud($configuracion,$opciones);
	}
}

//===========================================================================
//                         FUNCIONES
//===========================================================================



function acceso_db_solicitud($cadena_sql,$acceso_db,$tipo)
{
	if($tipo=="busqueda")
	{
		$acceso_db->registro_db($cadena_sql,0);
		$registro=$acceso_db->obtener_registro_db();
		return $registro;
	}
	else
	{
		$resultado=$acceso_db->ejecutar_acceso_db($cadena_sql);
		return $resultado;
	}
}


function enrutar_solicitud($configuracion,$variable)
{
	$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
	$cripto=new encriptar();
	$variable=$cripto->codificar_url($variable,$configuracion);
	echo "<script>location.replace('".$indice.$variable."')</script>";
	
}

function cadena_sql_solicitud($configuracion,$tipo,$variable="")
{
	foreach ($variable as $key => $value) 
	{
	echo $key."=>".$value."<br>";
	
	}
	
	switch($tipo)
	{
		case "insertar":
			$cadena_sql="INSERT INTO backoffice_solicitud_recibo "; 
			$cadena_sql.="( ";
			$cadena_sql.="`id_solicitud_recibo`, ";
			$cadena_sql.="`id_usuario`, ";
			$cadena_sql.="`codigo_est`, ";
			$cadena_sql.="`estado`, ";
			$cadena_sql.="`fecha` ";
			$cadena_sql.=") ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
			$cadena_sql.="NULL, ";
			$cadena_sql.="'".$variable['id_usuario']."', ";
			$cadena_sql.="'".$_REQUEST['codigo']."', ";
			$cadena_sql.="'0', ";
			$cadena_sql.="'".time()."' ";
			$cadena_sql.=")";
			break;
		
		case "update":
			
			break;
		
		case "select":
			$cadena_sql="SELECT ";
			$cadena_sql.="`id_solicitud_recibo`, ";
			$cadena_sql.="`id_usuario`, ";
			$cadena_sql.="`codigo_est`, ";
			$cadena_sql.="`estado`, ";
			$cadena_sql.="`fecha` ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo "; 
			$cadena_sql.="WHERE ";
			$cadena_sql.="`estado`=1 ";
			$cadena_sql.="AND ";
			$cadena_sql.="`id_usuario`='".$variable["id_usuario"]."'";
			//estado=0 solicitud no procesada
			//estado=1 solicitud en proceso
			//estado=2 solicitud procesada
		
		
		default:
			break;
	}

	return $cadena_sql;

}

function sesion_solicitud($configuracion,$acceso_db,$variable)
{
	$nueva_sesion=new sesiones($configuracion);
	$nueva_sesion->especificar_enlace($acceso_db->obtener_enlace());
	$esta_sesion=$nueva_sesion->numero_sesion();
	
	if (strlen($esta_sesion) != 32) 
	{
		return FALSE;
	
	} 
	else 
	{
		$resultado = $nueva_sesion->rescatar_valor_sesion($configuracion,$variable);
		return $resultado;
	}
	
	
}
	
?>
