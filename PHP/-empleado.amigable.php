<?php
protegerme(false,array(NIVEL_administrador,NIVEL_empresa));
if( usuario_cache('ui_rrhh') == 'no' )
{
    echo '<p class="error">Lo sentimos, Ud. no dispone de esta caracteristica, contacte a BCA.</p>';
    return;
}
$arrJS[] = 'jquery.maskedinput-1.2.2.min';
$arrHEAD[] = JS_onload('$("#DUI").mask("99999999-9");$("#NIT").mask("9999-999999-999-9");$(".ayuda").hide();$("#cambiar-importante").show().click(function (){$(this).hide();$(".ayuda").show();});');
$enlaces_rapidos = '<p>Enlaces rápidos: <a href="#" onClick="history.go(-1)">regresar a la página anterior</a>, <a href="'.PROY_URL.'~empleado" alt="Empleados de su empresa">búsqueda de mis empleados</a>, <a href="'.PROY_URL.'~empleado?agregar" alt="Agregar empleado a su empresa">agregar nuevo empleado</a> o <a href="'.PROY_URL.'" title="Pagina de inicio de BCA">pagina de inicio de BCA</a></p>';

if (isset($_GET['cargo']))
{
    require_once('-empleado.amigable.cargo.php');
    return;
}

if (isset($_GET['agregar']))
{
    require_once('-empleado.amigable.agregar.php');
    return;
}

if (isset($_GET['cese']))
{
    require_once('-empleado.amigable.cese.php');
    return;
}
if (isset($_GET['anexo']))
{
    require_once('-empleado.amigable.anexo.php');
    return;
}
$resultados_busqueda = '';
if (isset($_POST['buscar']))
{
$op['modo'] = 'no_descontar_creditos';
$op['limite'] = 'solo_activos_si_nombre';
$op['funcion'] = 'empleado_buscar__vista_amigable';
$op['ID_empresa'] = usuario_cache('ID_empresa');
$op['DUI'] = $_POST['DUI'];
$op['NIT'] = $_POST['NIT'];
$op['nombre_completo'] = $_POST['nombre'];
$resultados_busqueda = empleado_buscar($op);
}

if (isset($_POST['ver_activos']))
{
$op['modo'] = 'no_descontar_creditos';
$op['limite'] = 'solo_activos';
$op['funcion'] = 'empleado_buscar__vista_amigable';
$op['ID_empresa'] = usuario_cache('ID_empresa');
$resultados_busqueda = empleado_buscar($op);
}

if (isset($_POST['ver_inactivos']))
{
$op['modo'] = 'no_descontar_creditos';
$op['limite'] = 'solo_inactivos';
$op['funcion'] = 'empleado_buscar__vista_amigable';
$op['ID_empresa'] = usuario_cache('ID_empresa');
$resultados_busqueda = empleado_buscar($op);
}
?>

<h2>Herramienta de búsqueda interna de empleados</h2>
<p class="destacado">La búsqueda por nombre/apellido no incluirá empleados que no laboren actualmente en su empresa</p>
<p><a id="cambiar-importante" style="display:none"> Mostrar ayuda adicional...</a></p>
<p class="ayuda">
    Para <strong>búscar</strong> un empleado <strong>registrado en su empresa</strong> introduzca el DUI, NIT o nombre del empleado (puede especificar solamente uno de los campos si lo desea).
</p>
<p class="ayuda">
    Para gestionar los <strong>cargos</strong> de sus empleados sirvase de la herramienta de búsqueda antes mencionada para encontrar el registro del empleado y posteriormente utilizar el botón "<strong>trayectoria laboral en <?php echo usuario_cache('razon_social'); ?></strong>" que aparecerá contiguo al nombre del empleado en los resultados.
</p>
<p class="ayuda">
    Los <strong>ceses laborales</strong> pueden ser ingresados de forma similar a los cargos, en este caso utilizar el botón "<strong>crear cese laboral</strong>" que aparecerá contiguo al botón "<strong>agregar cargo laboral</strong>".
</p>

<form action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">
<table class="t100 tfija">
<tr><th>DUI</th><th>NIT</th><th>Nombres/Apellidos</th><th>Accion</th></tr>
<tr><td><input id="DUI" name="DUI" type="text" value="<?php echo @$_POST['DUI']; ?>" /></td><td><input id="NIT" name="NIT" type="text" value="<?php echo @$_POST['NIT']; ?>" /></td><td><input name="nombre" type="text" value="<?php echo @$_POST['nombre']; ?>" /></td><td><input type="submit" name="buscar" value="Búscar registro de empleado" /></td></tr>
<tr><td colspan="4"><input type="submit" name="ver_activos" value="Ver todos los empleados activos" /> <input type="submit" name="ver_inactivos" value="Ver todos los empleados inactivos" /></td></tr>
</table>
</form>
<?php echo $resultados_busqueda; ?>
