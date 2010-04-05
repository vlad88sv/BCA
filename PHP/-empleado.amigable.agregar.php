<?php

$buffer = $texto_busqueda = '';
$comprobacion_fallos = array();

if ( isset($_POST['cancelar']) )  {
    header('Location: '.PROY_URL.'~empleado');
    exit;
}
if ( isset($_POST['enviar']) && (isset($_POST['DUI']) && isset($_POST['NIT']) && isset($_POST['nombres']) && isset($_POST['apellidos'])) )
{
    // Saneamiento
    $_POST['DUI'] = preg_replace('/[^\d|-]/','',$_POST['DUI']);
    $_POST['NIT'] = preg_replace('/[^\d|-]/','',$_POST['NIT']);

    $comprobacion_fallos = array();

    // Esta bien el Nº de DUI?
    if (!preg_match('/\d{8}-\d/',$_POST['DUI']))
        $comprobacion_fallos[] = 'Numero de DUI invalido, asegurese de ingresar los nueve digitos, incluyendo el guion. Ej. <strong>12345678-9</strong>';

    // Esta bien el Nº de NIT?
    if (!preg_match('/\d{4}-\d{6}-\d{3}-\d/',$_POST['NIT']))
        $comprobacion_fallos[] = 'Numero de NIT invalido, asegurese de ingresar los catorce digitos, incluyendo guiones. Ej. <strong>1234-567890-1234-4</strong>';

    // Esta bien el nombre?
    if (!preg_match('/\w{3,25}/',$_POST['nombres']))
        $comprobacion_fallos[] = 'El nombre no parece valido, asegurese de ingresar solamente letras (a-z, A-Z) y usar entre 3 y 25 letras.';

    // Esta bien el apellido?
    if (!preg_match('/\w{3,25}/',$_POST['apellidos']))
        $comprobacion_fallos[] = 'El apellido no parece valido, asegurese de ingresar solamente letras (a-z, A-Z) y usar entre 3 y 25 letras.';

   // Esta bien el cargo?
    if (!preg_match('/\w{2,50}/',$_POST['cargo']))
        $comprobacion_fallos[] = 'El cargo no parece valido, asegurese de ingresar solamente letras (a-z, A-Z) y usar entre 2 y 50 letras para describirlo.';

   // Esta bien el salario?
    if (!is_numeric($_POST['salario']))
        $comprobacion_fallos[] = 'El salario no parece un número válido, no incluya simbolos (como $) ni letras.';

    // ¿Ya existe ese empleado?
    list($texto_busqueda, $numero_resultados) = empleado_buscar(array('estricto' => 1, 'no_resultados_no_error',  'ID_empresa' => usuario_cache('ID_empresa'), 'modo' => 'no_descontar_creditos', 'funcion' => 'empleado_buscar__vista_agregar', 'DUI' => $_POST['DUI'],'NIT' => $_POST['NIT']));
    if ($numero_resultados)
        $comprobacion_fallos[] = 'Se encontró un empleado registrado en su empresa con los mismos datos de DUI y NIT.</p>'.$texto_busqueda.'<p>Recomendamos <a href="'.PROY_URL.'~empleado" alt="Empleados de su empresa">revisar sus registros de empleado</a>';

    // Si no hubo errores agreguemos el empleado
    if (!count($comprobacion_fallos))
    {
        
        // Datos del empleado
        unset($datos);
        $datos['ID_empresa'] = usuario_cache('ID_empresa');
        $datos['ID_usuario'] = usuario_cache('ID_usuario');
        $datos['fecha_ingreso'] = mysql_date();
        $datos = array_merge($datos, array_intersect_key( $_POST, array_flip( array('DUI','NIT','nombres','apellidos','op_fecha_nacimiento', 'op_lugar_nacimiento', 'op_direccion', 'op_correo', 'op_estado_civil', 'op_idioma', 'op_informatica', 'op_interes', 'op_referencias' , 'op_telefono1', 'op_telefono2', 'op_movil1', 'op_movil2') ) ) );
        $ID_empleado = db_agregar_datos(db_prefijo.'empleado',$datos);

        // Su primer cargo laboral - aww :)
        unset($datos);
        $datos['ID_empresa'] = usuario_cache('ID_empresa');
        $datos['ID_usuario'] = usuario_cache('ID_usuario');
        $datos['ID_empleado'] = $ID_empleado;
        $datos['fecha_ingreso'] = mysql_date();
        $datos = array_merge($datos, array_intersect_key( $_POST,array_flip(array('fecha_inicio','cargo','salario')) ) );
        $ID_historial = db_agregar_datos(db_prefijo.'historial',$datos);

        echo '<h1>Resultado de solicitud de ingreso de nuevo registro de empleado</h1>';
        if ($ID_empleado && $ID_historial)
        {
            $mensaje['mensaje'] = 'El usuario <strong>'.usuario_cache('nombre').'</strong>, añadió un nuevo empleado [<strong>'. $_POST['apellidos'] .','. $_POST['nombres'].'</strong>] a su empresa.';
            $mensaje['tipo'] = 'info';
            mensaje(array(usuario_cache('ID_empresa')),array($mensaje));
            empleado_difundir_actualizaciones($_POST['DUI'],$_POST['NIT'],'ha sido registrado en la empresa <strong>'.usuario_cache('razon_social').'</strong>. Registrado con nombre <strong>'. $_POST['apellidos'] .','. $_POST['nombres'].'</strong>.');
            echo '<p>Gracias, su solicitud de ingreso de datos a sido recibida y aceptada. El nuevo registro de empleado ya se encuentra disponible.</p>';
            echo '<p>Si desea añadir mas cargos laborales a su empleado dirijase a <a href="'.PROY_URL.'~empleado?cargo='.$ID_empleado.'">cargos laborales para '.$_POST['apellidos'].', '.$_POST['nombres'].'</a></p>';
            echo '<p>Tambien puede <a href="'.PROY_URL.'~empleado" alt="Empleados de su empresa">revisar sus registros de empleado</a>, <a href="'.PROY_URL.'~empleado?agregar" alt="Agregar empleado a su empresa">agregar otro empleado</a> o <a href="'.PROY_URL.'" title="Pagina de inicio de BCA">regresar a la pagina de inicio de BCA</a></p>';
            return;
        }
        else
        {
            $buffer = '<p class="error">Lo sentimos, sucedio un error desconocido y su solicitud no pudo ser procesada, puede intentarlo nuevamene si lo desea</p>';
        }
    }
}


$arrCSS[] = 'overcast/jquery-ui-1.8rc3.custom';
$arrJS[] = 'jquery-ui-1.8rc3.min';
$arrJS[] = 'jquery.ui.datepicker-es';
$arrHEAD[] = JS_onload('$(".datepicker").datepicker({inline: true, maxDate: "+0", dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});');

?>
<h1>Agregar empleado a <?php echo usuario_cache('razon_social'); ?></h1>
<p>En esta seccion puede agregar nuevos empleados de su empresa al sistema de <?php echo PROY_NOMBRE ?>.</p>
<p>Por favor llene los nombres y apellidos segun aparece en el DUI de su empleado sin utilizar abreviaturas, sobrenombres o diminutivos.</p>
<p class="importante">Ud. no podra editar esta informacion una vez ingresada y aceptada en el sistema, si deseara realizar un cambio a esta informacion en el futuro podrá realizarla sin ningún costo a travez de su ejecutivo de cuenta de <?php echo PROY_NOMBRE; ?>.</p>
<form autocomplete="off" action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">
<h2>Datos personales</h2>
<table class="t100 tfija">
<tr><th><acronym title="Documento Único de Identidad">DUI</acronym></th><td><input id="DUI" name="DUI" type="text" value="<?php echo @$_POST['DUI']; ?>" /></td></tr>
<tr><th><acronym title="Número de Identificación Triburataria">NIT</acronym></th><td><input id="NIT" name="NIT" type="text" value="<?php echo @$_POST['NIT']; ?>" /></td></tr>
<tr><th>Nombres</th><td><input name="nombres" type="text" value="<?php echo @$_POST['nombres']; ?>" /></td></tr>
<tr><th>Apellidos</th><td><input name="apellidos" type="text" value="<?php echo @$_POST['apellidos']; ?>" /></td></tr>
</table>

<h2>Datos del primer cargo laboral que tuvo en su empresa</h2>
<table class="t100 tfija">
<tr><th>Primer cargo laboral</th><td><input name="cargo" type="text" value="<?php echo @$_POST['cargo']; ?>" /></td></tr>
<tr><th>Fecha de inicio de contratación</th><td><input class="datepicker" name="fecha_inicio" type="text" value="<?php echo @$_POST['fecha_inicio']; ?>" /></td></tr>
<tr><th>Salario para este cargo</th><td><input name="salario" type="text" value="<?php echo @$_POST['salario']; ?>" /></td></tr>
</table>

<?php if( usuario_cache('ui_rrhh_extendido') == 'si' ) { ?>
<h2>Datos opcionales para su uso interno</h2>
<table class="t100 tfija">
<tr><th>Fecha de nacimiento</th><td><input name="op_fecha_nacimiento" class="datepicker" type="text" value="<?php echo @$_POST['op_fecha_nacimiento']; ?>" /></td></tr>
<tr><th>Lugar de nacimiento</th><td><input name="op_lugar_nacimiento" type="text" value="<?php echo @$_POST['op_lugar_nacimiento']; ?>" /></td></tr>
<tr><th>Dirección</th><td><input name="op_direccion" type="text" value="<?php echo @$_POST['op_direccion']; ?>" /></td></tr>
<tr><th>Correo electrónico</th><td><input name="op_correo" type="text" value="<?php echo @$_POST['op_correo']; ?>" /></td></tr>
<tr><th>Estado civíl</th><td><?php echo ui_combobox('op_estado_civil', ui_array_a_opciones(array('soltero' => 'Solter@', 'casado' => 'Casad@', 'divorciado' => 'Divorciad@', 'viudo' => 'Viud@', 'concubino' => 'Concubin@')), @$_POST['op_estado_civil']); ?></td></tr>
<tr><th>Idiomas</th><td><input name="op_idioma" type="text" value="<?php echo @$_POST['op_idioma']; ?>" /></td></tr>
<tr><th>Informática</th><td><input name="op_informatica" type="text" value="<?php echo @$_POST['op_informatica']; ?>" /></td></tr>
<tr><th>Otros datos de interés</th><td><input name="op_interes" type="text" value="<?php echo @$_POST['op_interes']; ?>" /></td></tr>
<tr><th>Referencias</th><td><input name="op_referencias" type="text" value="<?php echo @$_POST['op_referencias']; ?>" /></td></tr>
<tr><th>Teléfono 1</th><td><input name="op_idioma" type="text" value="<?php echo @$_POST['op_telefono1']; ?>" /></td></tr>
<tr><th>Teléfono 2</th><td><input name="op_idioma" type="text" value="<?php echo @$_POST['op_telefono2']; ?>" /></td></tr>
<tr><th>Móvil 1</th><td><input name="op_idioma" type="text" value="<?php echo @$_POST['op_movil1']; ?>" /></td></tr>
<tr><th>Móvil 2</th><td><input name="op_idioma" type="text" value="<?php echo @$_POST['op_movil2']; ?>" /></td></tr>
</table>
<?php } ?>;

<center>
    <input type="submit" name="enviar" value="Enviar datos" />
    <input type="submit" name="cancelar" value="Cancelar" />
</center>
</form>

<?php
if (count($comprobacion_fallos))
    echo '<h2>Errores encontrados en la comprobación</h2><p class="error">'.join('</p><p class="error">',$comprobacion_fallos).'</p>';
else
    echo $buffer;
?>

<hr />
<?php echo $enlaces_rapidos ?>
