<?php
$arrCSS = array('estilo');
$arrJS = array('jquery-1.4.2.min');
$arrHEAD = array();

$HEAD_titulo = PROY_NOMBRE . ' - Inicio';
$HEAD_descripcion = 'Buró Laboral Centroamericano';

function HEAD_JS()
{
    global $arrJS;
    require_once 'PHP/terceros/jsmin-1.1.1.php';
    echo "\n";
    $buffer = '';
    foreach ($arrJS as $JS)
    {
        $buffer .= '<script type="text/javascript">'.JSMin::minify(file_get_contents("JS/".$JS.".js"))."</script>\n";
        //$buffer .= '<script type="text/javascript" src="JS/'.$JS.'.js"></script>'."\n";
    }

    echo $buffer;
    echo "\n";
}

function HEAD_CSS()
{
    global $arrCSS;
    $buffer = '';
    foreach ($arrCSS as $CSS)
    {
        //$buffer .= '<style type="text/css">'.file_get_contents($CSS.".css")."</style>\n";
        //$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        //$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
      
        $buffer .= '<link rel="stylesheet" type="text/css" href="CSS/'.$CSS.'.css" />'."\n";
        
    }
    echo $buffer;
    echo "\n";
}

function HEAD_EXTRA()
{
    global $arrHEAD;
    echo "\n";
    echo implode("\n",$arrHEAD);
    echo "\n";
}

function GENERAR_CABEZA()
{
    require_once('PHP/__index_cabeza.php');
}

function GENERAR_MENU()
{
    require_once('PHP/__index_menu.php');
}

function GENERAR_PIE()
{
global $GLOBAL_MOSTRAR_PIE, $HEAD_titulo, $HEAD_descripcion;
if (!$GLOBAL_MOSTRAR_PIE) return;
if (isset($_GET['peticion']) && !in_array($_GET['peticion'],array('vitrina','categoria'))) return;
?>
<h1>¡Comparte <strong>esta</strong> página en la web para que más Salvadoreños alegren su día!</h1>
<center>
<?php
// FaceBook
echo ui_href('',sprintf('http://www.facebook.com/sharer.php?u=%s&t=%s&src=sp',urlencode(PROY_URL_ACTUAL_DINAMICA), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/facebook.gif" title="FaceBook" alt="FaceBook" />','','target="_blank"');
// del.icio.us
echo ui_href('',sprintf('http://del.icio.us/post?url=%s&title=%s',urlencode(PROY_URL), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/delicious.gif" title="del.icio.us" alt="del.icio.us" />','','target="_blank"');
// Digg
echo ui_href('',sprintf('http://digg.com/submit?phase=2&url=%s&title=%s',urlencode(PROY_URL), urlencode(utf8_decode($HEAD_titulo))),'<img class="social" src="IMG/social/digg.gif" title="Digg" alt="Digg" />','','target="_blank"');
// StumbleUpon
echo ui_href('',sprintf('http://www.stumbleupon.com/submit?url=%s&title=%s',urlencode(PROY_URL), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/stumbleupon.gif" title="StumbleUpon" alt="StumbleUpon" />','','target="_blank"');
// Twitter
echo ui_href('',sprintf('http://twitter.com/home?status=Actualmente viendo %s, %s',urlencode(PROY_URL_ACTUAL_DINAMICA), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/twitter.gif" title="Twitter" alt="Twitter" />','','target="_blank"');
?>
</center>
<div id="inscribete">
<form action="<?php echo PROY_URL?>verificar" method="post">
Inscribe tu correo para recibir ofertas especiales <input name="ce" type="text" value="" /> <input name="inscribir" type="submit" class="btnlnk btnlnk-mini" value="Inscribirme" />
</form>
</div>
<?php
cargar_editable('portada');
}

function GENERAR_GOOGLE_ANALYTICS()
{
    return '
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-12744164-1");
pageTracker._trackPageview();
} catch(err) {}</script>
';
}

?>
