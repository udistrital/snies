<?php

namespace sniesEstudiante;

use sniesEstudiante\Componente;
use sniesEstudiante\Sql;

include_once ('component/GestorEstudiante/Sql.class.php');
require_once ('component/GestorEstudiante/Interfaz/IGestorEstudianteAcademica.php');
include_once ("core/manager/Configurador.class.php");
class estudiante implements IGestorEstudiante {
	// private $miGestorAdmitido;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this -> miSql = new Sql();
		$this -> miConfigurador = \Configurador::singleton();
	}

	function contarMatriculados($annio, $semestre) {
		$this -> miConfigurador = \Configurador::singleton();
		// configuracion es el nombre de la conexión principal de SARA - se crea de forma automática tomando los
		// datos de config.inc.php
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		$variable['annio'] = $annio;
		$variable['semestre'] = $semestre;

		$cadenaSql = $this -> miSql -> cadena_sql('contarMatriculados', $variable);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado[0][0];
	}

	function consultarEstudianteAcademica($annio, $semestre) {
		$conexion = "academica";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		// el semestre 03 de la universidad corresponde al semestre 02 de SNIES
		$periodo['annio'] = $annio;
		if ($semestre == 02) {
			$periodo['semestre'] = 3; ;
		} else {
			$periodo['semestre'] = 1;
		}

		$cadenaSql = $this -> miSql -> cadena_sql('consultarEstudianteAcademica', $periodo);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	function consultarEstudianteBpudc($annio, $semestre) {
		$conexion = "academica";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		// el semestre 03 de la universidad corresponde al semestre 02 de SNIES
		$periodo['annio'] = $annio;
		if ($semestre == 02) {
			$periodo['semestre'] = 3; ;
		} else {
			$periodo['semestre'] = 1;
		}

		$cadenaSql = $this -> miSql -> cadena_sql('consultarEstudianteBpudc', $periodo);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump($error);
			var_dump($estudiante);
		}

		return $resultado;
	}

	// ////PARTICIPANTE SNIES
	function consultarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('consultarParticipante', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	function consultarParticipanteTodos() {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('consultarParticipanteTodos', '');

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \sniesEstudiante\IGestorEstudiante::actualizarParticipante()
	 */
	function actualizarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('actualizarParticipante', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump($error);
			var_dump($estudiante);
		}

		return $resultado;
	}

	function registrarParticipante($estudiante) {
		//var_dump($estudiante);
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('registrarParticipante', $estudiante);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function borrarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('borrarParticipante', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	// ///ESTUDIANTE SNIES
	function consultarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('consultarEstudiante', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	function consultarEstudianteTodos() {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('consultarEstudianteTodos', '');

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	function actualizarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('actualizarEstudiante', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function registrarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('registrarEstudiante', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function borrarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('borrarEstudiante', $estudiante);
		echo $cadenaSql;
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	// ///PRIMER_CURSO SNIES

	function actualizarEstudiantePrimerCurso($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('actualizarEstudiantePrimerCurso', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump($error);
			var_dump($estudiante);
		}
	}

	function consultarEstudiantePrimerCurso($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('consultarEstudiantePrimerCurso', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	function borrarEstudiantePrograma($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('borrarEstudiantePrograma', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		return $resultado;
	}

	function consultarPrimerCursoTodos($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$variable['ANNIO'] = $annio;
		$variable['SEMESTRE'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarPrimerCursoTodos', $variable);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	function consultarPrimerCursoAuditoria($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$variable['ANNIO'] = $annio;
		$variable['SEMESTRE'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarPrimerCursoAuditoria', $variable);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	function registrarEstudiantePrimerCurso($estudiante) {

		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('registrarEstudiantePrimerCurso', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	// //MATRICULADO
	function borrarMatriculado($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('borrarMatriculado', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	function borrarMatriculadoPeriodoTodos($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$estudiante['ANNIO_MATRICULA'] = $annio;
		$estudiante['SEMESTRE_MATRICULA'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('borrarMatriculadoPeriodoTodos', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	function consultarMatriculado($estudiante, $annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar

		$estudiante['ANNIO_MATRICULA'] = $annio;
		$estudiante['SEMESTRE_MATRICULA'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarMatriculado', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function consultarMatriculadoTodos($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar

		$estudiante['ANNIO_MATRICULA'] = $annio;
		$estudiante['SEMESTRE_MATRICULA'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarMatriculadoTodos', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function consultarMatriculadoAuditoria($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar

		$estudiante['ANNIO_MATRICULA'] = $annio;
		$estudiante['SEMESTRE_MATRICULA'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarMatriculadoAuditoria', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function registrarMatriculado($estudiante, $annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar

		$estudiante['ANNIO_MATRICULA'] = $annio;
		$estudiante['SEMESTRE_MATRICULA'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('registrarMatriculado', $estudiante);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function actualizarMatriculado($estudiante, $annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar

		$estudiante['ANNIO_MATRICULA'] = $annio;
		$estudiante['SEMESTRE_MATRICULA'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('actualizarMatriculado', $estudiante);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	// EGRESADO
	function borrarEgresado($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('borrarEgresado', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	// //GRADUADO
	function consultarGraduadoAcademica($annio, $semestre) {
		$conexion = "academica";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		// el semestre 03 de la universidad corresponde al semestre 02 de SNIES
		$periodo['annio'] = $annio;
		if ($semestre == 02) {
			$periodo['semestre'] = 3; ;
		} else {
			$periodo['semestre'] = 1;
		}

		$cadenaSql = $this -> miSql -> cadena_sql('consultarGraduadoAcademica', $periodo);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}
	
	
	function consultarGraduadoTodos($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar

		$variable['ANO'] = $annio;
		$variable['SEMESTRE'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarGraduadoTodos', $variable);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}	

	function borrarGraduadoPeriodoTodos($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$variable['ANNIO_GRADO'] = $annio;
		if ($semestre == 02) {
			$variable['SEMESTRE_GRADO'] = 2; ;
		} else {
			$variable['SEMESTRE_GRADO'] = 1;
		}
		$cadenaSql = $this -> miSql -> cadena_sql('borrarGraduadoPeriodoTodos', $variable);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

	function registrarGraduado($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('registrarGraduado', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function borrarGraduado($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$cadenaSql = $this -> miSql -> cadena_sql('borrarGraduado', $estudiante);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');
		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}
		return $resultado;
	}

}
