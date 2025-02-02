<?php
namespace gui\menuPrincipal;
if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class Sql extends \Sql {
	
	
	var $miConfigurador;

function __construct() {
    
    $this->miConfigurador = \Configurador::singleton ();

}

function getCadenaSql($tipo, $variable = "") {
    
    /**
     * 1.
     * Revisar las variables para evitar SQL Injection
     */
    $prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
    $idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
    
    switch ($tipo) {
        
        /**
         * Clausulas específicas
         */
        
        case "buscarUsuario" :
            $cadenaSql = "SELECT ";
            $cadenaSql .= "FECHA_CREACION, ";
            $cadenaSql .= "PRIMER_NOMBRE ";
            $cadenaSql .= "FROM ";
            $cadenaSql .= "USUARIOS ";
            $cadenaSql .= "WHERE ";
            $cadenaSql .= "`PRIMER_NOMBRE` ='" . $variable . "' ";
            break;
        
        case "insertarRegistro" :
            $cadenaSql = "INSERT INTO ";
            $cadenaSql .= $prefijo . "registradoConferencia ";
            $cadenaSql .= "( ";
            $cadenaSql .= "`idRegistrado`, ";
            $cadenaSql .= "`nombre`, ";
            $cadenaSql .= "`apellido`, ";
            $cadenaSql .= "`identificacion`, ";
            $cadenaSql .= "`codigo`, ";
            $cadenaSql .= "`correo`, ";
            $cadenaSql .= "`tipo`, ";
            $cadenaSql .= "`fecha` ";
            $cadenaSql .= ") ";
            $cadenaSql .= "VALUES ";
            $cadenaSql .= "( ";
            $cadenaSql .= "NULL, ";
            $cadenaSql .= "'" . $variable ['nombre'] . "', ";
            $cadenaSql .= "'" . $variable ['apellido'] . "', ";
            $cadenaSql .= "'" . $variable ['identificacion'] . "', ";
            $cadenaSql .= "'" . $variable ['codigo'] . "', ";
            $cadenaSql .= "'" . $variable ['correo'] . "', ";
            $cadenaSql .= "'0', ";
            $cadenaSql .= "'" . time () . "' ";
            $cadenaSql .= ")";
            break;
        
        case "actualizarRegistro" :
            $cadenaSql = "UPDATE ";
            $cadenaSql .= $prefijo . "conductor ";
            $cadenaSql .= "SET ";
            $cadenaSql .= "`nombre` = '" . $variable ["nombre"] . "', ";
            $cadenaSql .= "`apellido` = '" . $variable ["apellido"] . "', ";
            $cadenaSql .= "`identificacion` = '" . $variable ["identificacion"] . "', ";
            $cadenaSql .= "`telefono` = '" . $variable ["telefono"] . "' ";
            $cadenaSql .= "WHERE ";
            $cadenaSql .= "`idConductor` =" . $_REQUEST ["registro"] . " ";
            break;
        
        /**
         * Clausulas genéricas.
         * se espera que estén en todos los formularios
         * que utilicen esta plantilla
         */
        
        case "iniciarTransaccion" :
            $cadenaSql = "START TRANSACTION";
            break;
        
        case "finalizarTransaccion" :
            $cadenaSql = "COMMIT";
            break;
        
        case "cancelarTransaccion" :
            $cadenaSql = "ROLLBACK";
            break;
        
        case "eliminarTemp" :
            
            $cadenaSql = "DELETE ";
            $cadenaSql .= "FROM ";
            $cadenaSql .= $prefijo . "tempFormulario ";
            $cadenaSql .= "WHERE ";
            $cadenaSql .= "id_sesion = '" . $variable . "' ";
            break;
        
        case "insertarTemp" :
            $cadenaSql = "INSERT INTO ";
            $cadenaSql .= $prefijo . "tempFormulario ";
            $cadenaSql .= "( ";
            $cadenaSql .= "id_sesion, ";
            $cadenaSql .= "formulario, ";
            $cadenaSql .= "campo, ";
            $cadenaSql .= "valor, ";
            $cadenaSql .= "fecha ";
            $cadenaSql .= ") ";
            $cadenaSql .= "VALUES ";
            
            foreach ( $_REQUEST as $clave => $valor ) {
                $cadenaSql .= "( ";
                $cadenaSql .= "'" . $idSesion . "', ";
                $cadenaSql .= "'" . $variable ['formulario'] . "', ";
                $cadenaSql .= "'" . $clave . "', ";
                $cadenaSql .= "'" . $valor . "', ";
                $cadenaSql .= "'" . $variable ['fecha'] . "' ";
                $cadenaSql .= "),";
            }
            
            $cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
            break;
        
        case "rescatarTemp" :
            $cadenaSql = "SELECT ";
            $cadenaSql .= "id_sesion, ";
            $cadenaSql .= "formulario, ";
            $cadenaSql .= "campo, ";
            $cadenaSql .= "valor, ";
            $cadenaSql .= "fecha ";
            $cadenaSql .= "FROM ";
            $cadenaSql .= $prefijo . "tempFormulario ";
            $cadenaSql .= "WHERE ";
            $cadenaSql .= "id_sesion='" . $idSesion . "'";
            break;
            
            
            /**
         * Clausulas Menú.
         * Mediante estas sentencias se generan los diferentes menus del aplicativo         
         */
                  
       	case "datosMenu" :
			$cadenaSql = "SELECT";
			$cadenaSql .= " item.id_menu as menu, item.parametros,";
			$cadenaSql .= " item.descripcion as descripcion,";
			$cadenaSql .= " grupo.descripcion as grupo,";
			$cadenaSql .= " item.columna as columna,";
			$cadenaSql .= " tipo_item.descripcion as tipo_item,";	
			$cadenaSql .= " item.link as link";
			$cadenaSql .= " FROM";
			$cadenaSql .= " menu.item";
			$cadenaSql .= " inner join menu.menu";
			$cadenaSql .= " on menu.id_menu = item.id_menu";
			$cadenaSql .= " inner join menu.grupo";
			$cadenaSql .= " on grupo.id_grupo = item.id_grupo";
			$cadenaSql .= " inner join menu.tipo_item"; 
			$cadenaSql .= " on item.id_tipo_item = tipo_item.id_tipo_item";			
			$cadenaSql .= " WHERE ";
			$cadenaSql .= " menu.estado_registro= true";
			$cadenaSql .= " and item.estado_registro = true";
			$cadenaSql .= " and menu.perfil_usuario =". $variable;
			$cadenaSql .= " order by grupo.orden_grupo, item.columna, item.id_tipo_item, item.orden_item";
			$cadenaSql .= ";";
			break;
    }
    
    return $cadenaSql;

}
}
?>
