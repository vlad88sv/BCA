<?php
if( usuario_cache('ui_rrhh_extendido') == 'no' )
{
    echo '<p class="error">Lo sentimos, Ud. no dispone de esta caracteristica, contacte a BCA.</p>';
    echo $enlaces_rapidos;
    return;
}

$empleado = empleado_obtener_datos($_GET['anexo']);

if(!$empleado || ($empleado['ID_empresa'] != usuario_cache('ID_empresa')))
{
    echo '<p class="error">Lo sentimos, no se le pueden mostrar los datos de este empleado</p>';
    echo $enlaces_rapidos;
    return;
}

if (empleado_estado($empleado['ID_empleado']) == 'recontratable')
{
    echo '<p class="error">Lo sentimos, no se pueden añadir nuevas acciones de personal a empleados inactivos.</p>';
    echo $enlaces_rapidos;
    return;   
}

if (isset($_POST['anexar']))
{
    switch ($_POST['tipo'])
    {
        case 'tardia':
            $_POST['valor_fecha'] = mysql_datetime($_POST['valor_fecha'] . ' ' . $_POST['tardia_Tiempo'] . ' ' . $_POST['tardia_TiempoExtra']);
            unset ($_POST['valor'],$_POST['tardia_TiempoExtra'],$_POST['tardia_Tiempo'],$_POST['subcategoria']);
            break;
    }
    
    if (empty($_POST['valor_fecha']))
    {
        $errores[] = 'Por favor especifique una fecha';
    }
    elseif (strtotime($_POST['valor_fecha']) > time())
    {
        $errores[] = 'La fecha especificada es posterior a la fecha actual';
    }
    elseif (!empleado_validar__fecha_es_mayor_a_ultima_fecha($empleado['ID_empleado'],$_POST['valor_fecha']))
    {
        $errores[] = 'La fecha espeficada no es válida';
    }
    
    if (isset($errores))
    {
        echo '<h2>Errores encontrados en la comprobación</h2><p class="error">'.join('</p><p class="error">',$errores).'</p>';
    }
    else
    {
        $datos['ID_empleado'] = $empleado['ID_empleado'];
        $datos['fecha_registro'] = mysql_datetime();   
        $datos['categoria'] = $_POST['tipo'];
        $datos = array_merge($datos, array_intersect_key( $_POST, array_flip( array('subcategoria', 'valor', 'valor_fecha' ) ) ) );
        
        $ID_empleado_anexo = db_agregar_datos(db_prefijo.'empleado_anexo',$datos);
        
        if ($ID_empleado_anexo)
        {
            echo '<h1>Registro anexado</h1>';
            echo '<p>El registro ha sido ingresado a la base de datos.</p>';
            echo '<p><input type="button" onclick="javascript:window.close()" value="Cerrar ventana"/></p>';
            echo '<hr class="consulta" />';
        }
        else
        {
            echo '<h1>Error</h1>';
            echo '<p class="error">Lo sentimos, sucedio un error desconocido y su solicitud no pudo ser procesada, puede intentarlo nuevamene si lo desea</p>';
        }
    }
    
}
$arrCSS[] = 'overcast/jquery-ui-1.8rc3.custom';
$arrJS[] = 'jquery-ui-1.8rc3.min';
$arrJS[] = 'jquery.ui.datepicker-es';

$strJSAjax = '$(document).ajaxStart(function(){$("#ajax").html("<p class=\"destacado\">Cargando...</p>");$("#control").hide()});';
$strJSCambiarAnexo = "\$('#select_opciones_envio').change(function(){var opcion=\$('#select_opciones_envio').val(); if(opcion != '') \$.post('/ajax', {ajax: 'anexo_tipo', tipo: opcion},function(data){\$('#ajax').html(data);$('#control').show();});});";
$arrHEAD[] = JS_onload($strJSAjax.$strJSCambiarAnexo);

$cargos = cargo_obtener_para(usuario_cache('ID_empresa'),$empleado['ID_empleado'],'','','cargo_obtener_para__vista_cargo_amigable','ASC');
?>
<h1>Cargos laborales para este empleado</h1>
<?php echo $cargos; ?>
<h1>Agregar acción de personal para <?php echo $empleado['apellidos'].','.$empleado['nombres']; ?></h1>
<p class="paso">Seleccione el tipo de acción de personal a agregar</p>
<select id="select_opciones_envio">
    <option value="">Escojer opción</option>
    <option value="falta">Falta</option>
    <option value="tardia">Llegada tarde</option>
    <option value="ausencia">Ausencia</option>
</select>
<form autocomplete="off" action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">
<div id="ajax">
Por favor escoja un tipo de acción personal.
</div>
<div id="control" style="display:none">
<input type="submit" value="Anexar" name="anexar" /> <input type="button" onclick="javascript:window.close()" value="Cancelar"/>
</div>
</form>

<hr />
<?php echo $enlaces_rapidos ?>
