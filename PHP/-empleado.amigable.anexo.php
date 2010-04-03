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
    if (empty($_POST['tiempo']))
        $_POST['tiempo'] = date('H:i a');

    if (empty($_POST['fecha']))
    {
        $_POST['fecha'] = date('Y-m-d');
    }
    elseif (strtotime($_POST['fecha'] . ' ' . $_POST['tiempo'] . ' ' . $_POST['tiempo2']) > time())
    {
        $errores[] = 'La fecha especificada es posterior a la fecha actual';
    }
    elseif (!empleado_validar__fecha_es_mayor_a_ultima_fecha($empleado['ID_empleado'],$_POST['fecha']))
    {
        $errores[] = 'La fecha espeficada no es permitida puesto que no corresponde con el periodo laboral activo del empleado.';
    }
    
    if (isset($errores))
    {
        echo '<h2>Errores encontrados en la comprobación</h2><p class="error">'.join('</p><p class="error">',$errores).'</p>';
    }
    else
    {
        $mensaje[] = array('tipo' => 'info', 'mensaje' => 'El usuario <strong>'.usuario_cache('nombre').'</strong>, añadió una acción de personal de tipo <strong>'.@$_POST['categoria'].'</strong> con intensidad "<strong>'.@$_POST['intensidad'].'</strong>" y con justificacion "<strong>'.@$_POST['justificacion'].'</strong>" para el empleado <strong>'. $empleado['apellidos'] . ', ' . $empleado['nombres'].'</strong>.');
        mensaje(array(usuario_cache('ID_empresa')),$mensaje);
        
        $datos['grupo'] = "accion_de_personal";
        $datos['ID_empleado'] = $empleado['ID_empleado'];
        $datos['fecha_registro'] = mysql_datetime();   
        $_POST['valor_fecha'] = mysql_datetime($_POST['fecha'] . ' ' . $_POST['tiempo'] . ' ' . $_POST['tiempo2']);
        $datos = array_merge($datos, array_intersect_key( $_POST, array_flip( array('categoria','detalle1', 'detalle2', 'valor', 'valor_fecha' ) ) ) );
        
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

$strJSDatePicker = "$('.calendario').datepicker({inline: true, maxDate: '+0', dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});";
$strJSMaskTiempo = '$("#tiempo").mask("99:99");';
$strJSCambiarAnexo = '$("#categoria").change( function(){$("#control").toggle($("#categoria").val() != "");} );';
$arrHEAD[] = JS_onload($strJSDatePicker.$strJSMaskTiempo.$strJSCambiarAnexo );
$cargos = cargo_obtener_para(usuario_cache('ID_empresa'),$empleado['ID_empleado'],'','','cargo_obtener_para__vista_cargo_amigable','ASC');
?>
<h1>Cargos laborales para este empleado</h1>
<?php echo $cargos; ?>
<h1>Agregar acción de personal para <?php echo $empleado['apellidos'].','.$empleado['nombres']; ?></h1>
<p class="paso">Seleccione el tipo de acción de personal a agregar</p>
<form autocomplete="off" action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">
<table class="tija">
    <tr><th>Tipo de acción de personal</th><td>
    <select name="categoria" id="categoria">
        <option value="">Escojer opción</option>
        <option value="falta">Falta</option>
        <option value="tardia">Llegada tarde</option>
        <option value="ausencia">Ausencia</option>
    </select>
    </td></tr>
</table>
<div id="control" style="display:none">
<table class="tija">
    <tr><th>Fecha del suceso</th><td><input name="fecha" class="calendario" value="" type="text" style="width:150px"/></td></tr>
    <tr><th>Hora del suceso</th><td><input name="tiempo" id="tiempo" value="" type="text" style="width:70px"/><select name="tiempo2" style="width:80px"><option value="a.m.">a.m.</option><option value="p.m.">p.m.</option></select></td></tr>
    <tr><th>Intensidad</th><td><select name="detalle1" style="width:150px"><option value="n/a">No aplica [n/a]</option><option value="leve">Leve</option><option value="moderada">Moderada</option><option value="grave">Grave</option></select></td></tr>
    <tr><th>Justificación</th><td><select name="detalle2" style="width:150px"><option value="n/a">No aplica [n/a]</option><option value="justificada">Justificada</option><option value="injustificada">Injustificada</option></select></td></tr>
    <tr><th>Detalle</th><td><textarea name="valor" style="width:300px;height:100px;"></textarea></td></tr>
</table>
<input type="submit" value="Anexar" name="anexar" /> <input type="button" onclick="javascript:window.close()" value="Cancelar"/>
</div>
</form>
<p>Nota: si no especifica la fecha entonces se utilizará la fecha de hoy, si no especifica la hora entonces se utilizará la hora actual</p>
<hr />
<?php echo $enlaces_rapidos ?>
