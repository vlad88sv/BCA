<?php
error_reporting             (E_STRICT | E_ALL);
ob_start                    ("ob_gzhandler");
setlocale                   (LC_ALL, 'es_AR.UTF-8', 'es_ES.UTF-8', 'es_AR', 'es_ES');
date_default_timezone_set   ('America/El_Salvador');
ini_set                     ('session.gc_maxlifetime', '6000');
ini_set                     ("session.cookie_lifetime","36000");
$base = dirname(__FILE__);
require_once ("$base/__ui.php"); // Generación de objetos UI HTML
require_once ("$base/__stubs.php"); // Generación de objetos UI desde la base de datos [depende de ui.php]
require_once ("$base/__const.php"); // Conexión hacia la base de datos
require_once ("$base/__sesion.php");
require_once ("$base/__db-conexion.php"); // Datos de conexión hacia la base de datos
require_once ("$base/__db.php"); // Conexión hacia la base de datos [depende de db-conexion.php]
require_once ("$base/__db-stubs.php"); // Generación de objetos UI desde la base de datos [depende de ui.php]
require_once ("$base/__db-ui.php"); // Generación de objetos UI desde la base de datos [depende de ui.php]
require_once ("$base/__usuario.php");
require_once ("$base/__empleado.php");
require_once ("$base/__cargo.php");
require_once ("$base/__index_stubs.php");
require_once ("$base/PME/phpMyEdit.class.php");

function DEPURAR($s,$f=0){if($f||isset($_GET['depurar'])){echo '<pre>'.$s.'</pre><br /><pre>'.parse_backtrace().'</pre><br />';}}
?>
