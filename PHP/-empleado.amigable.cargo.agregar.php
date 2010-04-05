<?php
$arrCSS[] = 'overcast/jquery-ui-1.8rc3.custom';
$arrJS[] = 'jquery-ui-1.8rc3.min';
$arrJS[] = 'jquery.ui.datepicker-es';
$arrHEAD[] = JS_onload("$('#fecha_inicio').datepicker({inline: true, maxDate: '+0', dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});");
$comprobacion_fallos = array();
$buffer = '';

if ( isset($_POST['cancelar']) )  {
    header('Location: '.PROY_URL.'~empleado?cargo='.$empleado['ID_empleado']);
    exit;
}

if(isset($_POST['agregar']))
{
    // Esta bien el cargo?
    if (!preg_match('/\w{2,50}/',$_POST['cargo']))
        $comprobacion_fallos[] = 'El cargo no parece valido, asegurese de ingresar solamente letras (a-z, A-Z) y usar entre 2 y 50 letras para describirlo.';
   
   // Esta bien el salario?
    if (!is_numeric($_POST['salario']))
        $comprobacion_fallos[] = 'El salario no parece un número válido, no incluya simbolos (como $) ni letras.';


    if (!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$_POST['fecha_inicio']))
        $comprobacion_fallos[] = 'La fecha no parece válida, por favor utilice el formato año-mes-dia [aaaa-mm-dd]';
    elseif (!empleado_validar__fecha_dentro_de_periodo_laboral_activo($empleado['ID_empleado'],$_POST['fecha_inicio']))
        $comprobacion_fallos[] = 'Ud. esta intentando agregar un cargo con fecha anterior al ultimo cargo o cese laboral registrado para este empleado.';
    elseif (strtotime($_POST['fecha_inicio']) > time())
        $comprobacion_fallos[] = 'Ud. esta intentando agregar un cargo con fecha posterior a la fecha actual.';

    if (!count($comprobacion_fallos))
    {
        $mensaje['mensaje'] = 'El usuario <strong>'.usuario_cache('nombre').'</strong>, añadió un nuevo cargo [<strong>'.@$_POST['cargo'].'</strong>] para el empleado <strong>'. $empleado['apellidos'] . ', ' . $empleado['nombres'].'</strong>.';
        $mensaje['tipo'] = 'info';
        mensaje(array(usuario_cache('ID_empresa')),array($mensaje));
        empleado_difundir_actualizaciones($empleado['DUI'],$empleado['NIT'],'tiene ahora un cargo diferente en la empresa <strong>'.usuario_cache('razon_social').'</strong>, el nuevo cargo laboral es <strong>'.$_POST['cargo'].'</strong>.');
        
        $datos['ID_empresa'] = usuario_cache('ID_empresa');
        $datos['ID_usuario'] = usuario_cache('ID_usuario');
        $datos['ID_empleado'] = $empleado['ID_empleado'];
        $datos['fecha_ingreso'] = mysql_date();
        $datos = array_merge($datos, array_intersect_key( $_POST,array_flip(array('fecha_inicio','cargo','salario')) ) );
        $ID_historial = db_agregar_datos(db_prefijo.'historial',$datos);
        $buffer = '<h2>Resultados</h2><p>Cargo añadido exitosamente.</p>';
        unset($_POST);
    }
}

?>

<h1>Agregar cargo laboral a <?php echo $empleado['apellidos'] . ', ' . $empleado['nombres'] . ' @ ' . $empleado['razon_social']; ?></h1>

<p><strong>DUI:</strong> <?php echo $empleado['DUI']; ?> / <strong>NIT:</strong> <?php echo $empleado['NIT']; ?></p>

<form autocomplete="off" action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">
<table class="tfija t100">
    <tr><th>Cargo</th><th>Fecha de inicio</th><th>salario</th><th>Accion</th></tr>
    <tr><td><input name="cargo" type="text" class="ancho" value="<?php echo @$_POST['cargo']; ?>"/></td><td><input name="fecha_inicio" id="fecha_inicio" type="text" value="<?php echo @$_POST['fecha_inicio']; ?>" /></td><td><input name="salario" type="text" value="<?php echo @$_POST['salario']; ?>" /></td><td><input name="agregar" value="Agregar" type="submit" /> <input name="cancelar" value="Cancelar" type="submit" /></td></tr>
</table>
</form>

<?php
if (count($comprobacion_fallos))
    echo '<h2>Errores encontrados en la comprobación</h2><p class="error">'.join('</p><p class="importante">',$comprobacion_fallos).'</p>';
else
    echo $buffer;
?>

<hr />

Opciones para este empleado: 
<a href="<?php echo PROY_URL; ?>~empleado?cargo=<?php echo $empleado['ID_empleado']; ?>" alt="Trayectoria laboral de este empleado">trayectoria laboral de este empleado en <?php echo $empleado['razon_social']; ?></a> /
<a href="<?php echo PROY_URL; ?>~empleado?cargo=<?php echo $empleado['ID_empleado']; ?>&agregar" alt="Agregar nuevo cargo laboral para este empleado">agregar cargo laboral</a> /
<a href="<?php echo PROY_URL; ?>~empleado?cese=<?php echo $empleado['ID_empleado']; ?>" alt="Agregar cese laboral a este empleado">crear cese laboral</a> / 
<a href="<?php echo PROY_URL; ?>~consulta?DUI='<?php echo $empleado['DUI']; ?>&NIT=<?php echo $empleado['NIT']; ?>" title="Ver antecedente laboral del empleado">ver antecedente laboral global</a>

<?php echo $enlaces_rapidos ?>
