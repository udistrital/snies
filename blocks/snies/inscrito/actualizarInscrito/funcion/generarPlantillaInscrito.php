<?php
include_once ('component/GestorInscritoAdmitido/Componente.php');
include_once ('blocks/snies/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/funcion/procesadorExcepcion.class.php');

use sniesInscritoAdmitido\Componente;
use bloqueSnies\procesadorExcepcion;
use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miComponente = new Componente ();
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	}
	/**
	 * Esta función realiza las siguientes acciones
	 * 1.consulta en la académica inscritos pregrado
	 * 2.consulta en la académica inscritos postgrado
	 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL tabla inscrito
	 * 2.Procesar los datos obtenidos, cambiar acentos.
	 * 3.Registrar errores de la fuente para reportarlos
	 * 5.Insertar los registros en el SNIES LOCAL
	 * 6.Redireccionar a lista de variables
	 */
	function procesarFormulario() {
		$annio = $_REQUEST ['annio'];
		$semestre = $_REQUEST ['semestre'];
		/**
		 * Asegure de que todos los programas estén registrados en la tabla accra_snies
		 * para buscar los que no están registrados utilice la consulta
		 * SELECT * FROM ACCRA WHERE CRA_COD NOT IN (SELECT AS_CRA_COD FROM ACCRA_SNIES  )AND CRA_ESTADO='A'order by cra_cod asc
		 */

		// CONSULTAS ACADEMICA
		$inscritosPregrado = $this->miComponente->consultarInscritoPregadoAcademica ( $annio, $semestre );
		$inscritosPostgrado = $this->miComponente->consultarInscritoPostgradoAcademica ( $annio, $semestre );



		// Si no realiza la consulta retorna a la pagina inicial
		if ($inscritosPregrado == false or $inscritosPostgrado == false) {
			$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );

			// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;

			header ( "Location:$miEnlace" );
		}

		// PARTE DE INSCRITOS DE PREGRADO

		$miProcesadorNombre = new procesadorNombre ();

		$inscritosPregrado = $miProcesadorNombre->quitarAcento ( $inscritosPregrado, 'APELLIDO' );
		$inscritosPregrado = $miProcesadorNombre->quitarAcento ( $inscritosPregrado, 'NOMBRE' );
		$inscritosPregrado = $miProcesadorNombre->quitarAcento ( $inscritosPregrado, 'PROG' );

		// descompone nombre y apellidos en sus partes y las agrega al final de cada registro
		foreach ( $inscritosPregrado as $clave => $valor ) {
			// echo $inscritosPregrado [$clave] ['DOCUMENTO'] . '<br>';

			// divide los apellidos compuestos en primer apellido y segundo apellido
			$apellido = $miProcesadorNombre->dividirApellidos ( $inscritosPregrado [$clave] ['APELLIDO'] );
			$inscritosPregrado [$clave] ['PRIMER_APELLIDO'] = $apellido ['primer_apellido'];
			$inscritosPregrado [$clave] ['SEGUNDO_APELLIDO'] = $apellido ['segundo_apellido'];

			// divide los nombres compuestos en primer nombre y segundo nombre
			$nombre = $miProcesadorNombre->dividirNombres ( $inscritosPregrado [$clave] ['NOMBRE'] );

			$inscritosPregrado [$clave] ['PRIMER_NOMBRE'] = $nombre ['primer_nombre'];
			$inscritosPregrado [$clave] ['SEGUNDO_NOMBRE'] = $nombre ['segundo_nombre'];
		}

		/////////////////////////////FIN PROCESAR NOMBRE PREGRADO///////////////////////////

/**
* ESTE SE DIFERENCIA DE PREGADO POR QUE LOS NOMBRES ESTAN EN UN SOLO CAMPO/
*/

		// PARTE DE INSCRITOS DE POSTGRADO
		/**
		 * Esta función realiza las siguientes acciones
		 * 1.consulta en la académica
		 * 2.Procesar los datos obtenidos, cambiar acentos.
		 * 3.Registrar errores de la fuente para reportarlos
		 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL
		 * 5.Insertar los registros en el SNIES LOCAL
		 * 6.Redireccionar a lista de variables
		 */

		$miProcesadorNombre = new procesadorNombre ();

		$inscritosPostgrado = $miProcesadorNombre->quitarAcento ( $inscritosPostgrado, 'NOMBRE' );
		$inscritosPostgrado = $miProcesadorNombre->quitarAcento ( $inscritosPostgrado, 'PROG' );

		// descompone nombre completo en sus partes y las agrega al final de cada registro
		foreach ( $inscritosPostgrado as $clave => $valor ) {
			// echo $inscritosPostgrado [$clave] ['DOCUMENTO'] . '<br>';

			// divide los apellidos compuestos en primer apellido y segundo apellido
			$nombreCompleto = $miProcesadorNombre->dividirNombreCompleto ( $inscritosPostgrado [$clave] ['NOMBRE'] );
			$inscritosPostgrado [$clave] ['PRIMER_APELLIDO'] = $nombreCompleto ['primer_apellido'];
			$inscritosPostgrado [$clave] ['SEGUNDO_APELLIDO'] = $nombreCompleto ['segundo_apellido'];
			$inscritosPostgrado [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$inscritosPostgrado [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
/////////////////////////////FIN PROCESAR NOMBRE POSTGRADO///////////////////////////




/**
* EN ESTA SECCIÓN SE GENERA EL ARCHIVO CSV
*
*/
		$inscritosTodos=array_merge($inscritosPregrado,$inscritosPostgrado);

		$this->generarPlantillaInscrito ( $inscritosTodos );

		echo 'Proceso finalizado';

	}

	function generarPlantillaInscrito($inscrito) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );

		$this->annio=$_REQUEST['annio'];
		$this->semestre=$_REQUEST['semestre'];
		$fp = fopen ( $raizDocumento . '/document/RelacionInscritos_' . $this->annio . $this->semestre . '.csv', 'w' );
		//ENCABEZADO DE LA PLANTILLA
		fputcsv ( $fp, array('Herramienta de Cargue Hecca - V 3.1'));
		fputcsv ( $fp, array('[143]', 'Nombre de la Plantilla: [Inscritos - Relación de Inscritos] Descripcion: [Persona natural que solicita formalmente el ingreso a un programa académico en calidad de estudiante.]'));
		fputcsv ( $fp, array('Licenciado para Ministerio de Educacion Nacional 2014'));
		fputcsv ( $fp, array (
				'AÑO',
				'SEMESTRE',
				'ID_TIPO_DOCUMENTO',
				'NUM_DOCUMENTO',
				'PRIMER_NOMBRE',
				'SEGUNDO_NOMBRE',
				'PRIMER_APELLIDO',
				'SEGUNDO_APELLIDO',
				'ID_SEXO_BIOLOGICO'
		) , ";");
		foreach ( $inscrito as $unInscrito ) {
			 var_dump ( $unInscrito );
			$RelacionInscrito ['AÑO'] = $unInscrito ['INS_ANNIO'];
			$RelacionInscrito ['SEMESTRE'] = $unInscrito ['INS_SEMESTRE'];
			$RelacionInscrito ['ID_TIPO_DOCUMENTO'] = $unInscrito ['TIPO_IDENT_CODE'];
			$RelacionInscrito ['NUM_DOCUMENTO'] = $unInscrito ['DOCUMENTO'];
			$RelacionInscrito ['PRIMER_NOMBRE'] = $unInscrito ['PRIMER_NOMBRE'];
			$RelacionInscrito ['SEGUNDO_NOMBRE'] = $unInscrito ['SEGUNDO_NOMBRE'];
			$RelacionInscrito ['PRIMER_APELLIDO'] = $unInscrito ['PRIMER_APELLIDO'];
			$RelacionInscrito ['SEGUNDO_APELLIDO'] = $unInscrito ['SEGUNDO_APELLIDO'];
			$RelacionInscrito ['ID_SEXO_BIOLOGICO'] = $unInscrito ['GENERO'];

			fputcsv ( $fp, $RelacionInscrito, ";" );
		}

		fclose ( $fp );

	}

}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();
