<?php
// Este archivo se encarga de traducir las peticiones

if (!isset($_GET['peticion']))
    $_GET['peticion'] = 'portada';

ob_start();
switch ($_GET['peticion'])
{
    case 'portada':
        require_once('PHP/_portada.php');
        break;
    case 'inicio':
        require_once('PHP/_inicio.php');
        break;
    case 'fin':
        require_once('PHP/_fin.php');
        break;
    case 'info':
        require_once('PHP/_info.php');
        break;
    case '~cargo':
        require_once('PHP/-cargo.php');
        break;
    case '~categoria':
        require_once('PHP/-categoria.php');
        break;
    case '~cese':
        require_once('PHP/-cese.php');
        break;
    case '~empleado':
        require_once('PHP/-empleado.php');
        break;
    case '~empresa':
        require_once('PHP/-empresa.php');
        break;
    case '~historial':
        require_once('PHP/-historial.php');
        break;
    case '~usuario':
        require_once('PHP/-usuario.php');
        break;
    case '~menu':
        require_once('PHP/-menu.php');
        break;
    case '~submenu':
        require_once('PHP/-submenu.php');
        break;
    case '~contenido':
        require_once('PHP/-contenido.php');
        break;
    case '~inicio':
        require_once('PHP/-inicio.php');
        break;
    case '~codigo-laboral':
        require_once('PHP/-codigo_laboral.despidos.php');
        break;
    case '~consulta':
        require_once('PHP/+consulta.php');
        break;
    case '~antecedente':
        require_once('PHP/+antecedente.php');
        break;
    case '~su':
        require_once('PHP/-su.php');
        break;
    case '~pago':
        require_once('PHP/-pago.php');
        break;
    default:
        header('Location: ' . PROY_URL);
        exit;
}
$BODY = ob_get_clean();
?>
<?php ob_start(); ?>
<body>
<div id="contenedor">
<div id="wrapper-head"><div id="header"><?php GENERAR_CABEZA(); ?></div></div> <!-- wrapper head !-->
<?php GENERAR_MENU(); ?>
<div id="wrapper-centro">
<div id="wrapper">
<div id="wrapper-contenido">
<?php echo $BODY; ?>
<?php //GENERAR_PIE(); ?>
</div> <!-- wrapper-contenido !-->
</div> <!-- wrapper !-->
</div> <!-- wrapper-centro !-->
</div> <!-- contenedor !-->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-15289753-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
<?php $BODY = ob_get_clean(); ?>

<?php
/* CAPTURAR <head> */
ob_start();
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta http-equiv="Content-Style-type" content="text/css" />
    <meta http-equiv="Content-Script-type" content="text/javascript" />
    <meta http-equiv="Content-Language" content="es" />
    <title><?php echo $HEAD_titulo; ?></title>
    <meta name="description" content="<?php echo $HEAD_descripcion; ?>" />
    <meta name="keywords" content="regalos originales, regalos empresariales, regalos de navidad, flores a domicilio, regalos de empresa, envio de flores, regalos san valentin, regalos de boda, regalos personalizados, regalos a domicilio, ramos de flores, regalos de cumpleaños, regalos promocionales, regalos especiales, regalos de aniversario, regalos romanticos, cuadros de flores, regalos para mujeres, regalos corporativos, flores artificiales, regalos para bebes, flores secas, regalos curiosos, regalos dia de la madre, flores rosas, arreglos de flores, regalos para mama, regalos navideños, arreglos florales para bodas, regalos mujer, fotos de arreglos florales, regalos para novios, pinturas de flores, regalos para baby shower, regalos para chicos, centros de mesa con flores, regalos bautizo, regalos de comunion, regalos divertidos, regalos empresarios, regalos sorpresa, regalos baratos, venta de flores, regalos para papa, regalos por internet, regalos sorprendentes, envolver regalos, regalos diferentes, regalos para niños, regalos de reyes, regalos originales para mujeres, regalos para el 14 de febrero, fotografias de flores, regalar flores, regalos online, regalos originales para san valentin, regalos originales amor, regalos para el dia de los enamorados, centros de flores, venta de casas en el salvador, regalos originales baratos, flores bonitas, regalos originales romanticos, arreglos florales para matrimonio, cursos de flores de bach, regalos con fotos, regalos para recien nacidos, comprar flores, envio de regalos, viveros de flores, regalos para ella, regalos amigo invisible, flores colombianas, regalos para enamorados, regalos novedosos, coronas de flores, regalos originales para hombres, flores por internet, regalos nacimiento, regalos infantiles, regalos artesanales." />
    <meta name="robots" content="index, follow" />
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
<?php HEAD_CSS(); ?>
<?php HEAD_JS(); ?>
<?php HEAD_EXTRA(); ?>
<script language="JavaScript" type="text/javascript">
if (top.location != location) {top.location.href = document.location.href ;}
</script>
</head>
<?php $HEAD = ob_get_clean(); ?>

<?php echo  $HEAD.$BODY; ?>
