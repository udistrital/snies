<?php
/**
 * IMPORTANTE
 * Este archivo debe generarse en el momento de la instalación
 * de SARA.
 *
 * Mientras se realiza el instalador se deben colocar de forma
 * manual los datos.
 *
 * El acceso a este archivo debe ser denegado. En servidores apache,
 * en el archivo .htaccess colocar:
 * # Proteger config.inc.php
 * <files config/config.inc.php>
 * order allow,deny
 * deny from all
 * </files>
 *
 * @todo En sistemas UNIX/Linux este archivo debe tener permisos 640, una
 *       medida adicional en sistemas en producción, podría ser el colocar
 *       este archivo en una carpeta que no esté en el directorio de
 *       documentos de apache; y encriptarlo utilizando las llaves de usuario
 *       registradas en el sistema operativo.
 *
 */
class ArchivoConfiguracion {
	private static $instance;

	/**
	 * Contiene los datos básicos de acceso a la base de datos principal del
	 * framework.
	 * Este usuario debe tener solo permisos de lectura.
	 *
	 * @var string[]
	 */
	var $conf;
	var $ruta;
	private function __construct() {
		$this->conf = array ();
	}
	public static function singleton() {
		if (! isset ( self::$instance )) {
			$className = __CLASS__;
			self::$instance = new $className ();
		}
		return self::$instance;
	}
	public function setConectorDB($objeto) {
		$this->fabricaConexiones = $objeto;
	}
	function getConf() {
		return $this->conf;
	}

	/**
	 * Rescata las variables de configuración que se encuentran en config.inc.php y
	 * en la base de datos principal (cuyos datos de conexión están en config.inc.php).
	 * Los datos son cargados en el arreglo $configuracion
	 *
	 * @param
	 *        	Ninguno
	 * @return number
	 */
	function variable() {
		require_once ($this->ruta . "crypto/Encriptador.class.php");
		require_once ($this->ruta . "crypto/aes.class.php");
		require_once ($this->ruta . "crypto/aesctr.class.php");

		$this->cripto = Encriptador::singleton ();
		$this->abrirArchivoConfiguracion ();
		return 0;
	}
	function setRuta($valor) {
		$this->ruta = $valor;
		return true;
	}
	private function abrirArchivoConfiguracion($ruta = "") {
		$respuesta = false;
		if ($ruta == '') {
			$ruta = 'config/';
		}

		$fp = fopen ( $ruta . 'config.inc.php', "r" );
		if ($fp) {

			$i = 0;
			while ( ! feof ( $fp ) && $i < 10 ) {
				$linea [$i] = $this->cripto->decodificar ( fgets ( $fp, 4096 ), "" );
				$i ++;
			}

			fclose ( $fp );

			if ($i > 9) {

				$this->conf ["dbsys"] = $linea [2];
				$this->conf ["dbdns"] = $linea [3];
				$this->conf ["dbpuerto"] = $linea [4];
				$this->conf ["dbnombre"] = $linea [5];
				$this->conf ["dbusuario"] = $linea [6];
				$this->conf ["dbclave"] = $linea [7];
				$this->conf ["dbprefijo"] = $linea [8];
				$this->conf ["dbesquema"] = $linea [9];
				//var_dump($this->conf);

				$respuesta = true;
			}
		}

		return $respuesta;
	}
}
?>
