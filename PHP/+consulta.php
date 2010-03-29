<?php
protegerme(false,array(NIVEL_administrador,NIVEL_empresa));
$arrJS[] = 'jquery.maskedinput-1.2.2.min';
$arrHEAD[] = JS_onload('$("#DUI").mask("99999999-9");$("#NIT").mask("9999-999999-999-9");');
$arrHEAD[] = JS_onload('$("#acepto").click(function () {$("#buscar").attr("disabled",!$("#acepto").is(":checked"));});');

$resultados_busqueda = '';
if (isset($_GET['DUI']) && isset($_GET['NIT']))
{
    $buscar = 'buscar';
    $_POST['DUI'] = $_GET['DUI'];
    $_POST['NIT'] = $_GET['NIT'];
}
if (isset($_POST['buscar']))
{
    $fecha_cese     = '(SELECT `fecha_cese` FROM cese WHERE cese.ID_empleado=empleado.ID_empleado AND cese.fecha_cese > @fecha_inicio_max ORDER BY fecha_cese DESC LIMIT 1)';
    $fecha_inicio_max      = '(SELECT `fecha_inicio` FROM historial WHERE historial.ID_empleado=empleado.ID_empleado ORDER BY fecha_inicio DESC LIMIT 1)';
    $fecha_inicio_min      = '(SELECT `fecha_inicio` FROM historial WHERE historial.ID_empleado=empleado.ID_empleado ORDER BY fecha_inicio ASC LIMIT 1)';
    $op['estricto'] = 1;
    $op['no_resultados_no_error'] = 1;
    $op['modo'] = 'creditos';
    $op['funcion'] = 'empleado_buscar__vista_consulta_global';
    $op['DUI'] = $_POST['DUI'];
    $op['NIT'] = $_POST['NIT'];
    $op['ORDER_BY'] = 'fecha_inicio ASC';
    $op['campos'] = '@fecha_inicio := '.$fecha_inicio_min.' as fecha_inicio, LAST_DAY(@fecha_fin := GREATEST( (COALESCE(@fecha_inicio_max := '.$fecha_inicio_max.', DATE(NOW()))), (COALESCE(@fecha_cese := '.$fecha_cese.', DATE(NOW()))) )) AS fecha_fin, IF(@fecha_cese,1,0) AS flag_cese, DATE_FORMAT(@fecha_inicio,"%e de %M de %Y") AS fecha_inicio_formato, DATE_FORMAT(@fecha_fin,"%e de %M de %Y") AS fecha_fin_formato';
    $resultados_busqueda = empleado_buscar($op);
}


?>
<h1>Consulta global de antecedente laboral</h1>
<p>En este modulo puede realizar consultas del antecedente laboral de cualquier persona natural por su número de DUI <strong>y</strong> NIT.</p>

<p>No es necesario que la persona cuyo antecedente laboral desee Ud. consultar sea un empleado activo de su empresa, por ejemplo puede utilizarlo para conocer el antecedente laboral de un aspirante.</p>

<form action="<?php echo PROY_URL_ACTUAL; ?>" autocomplete="off" method="post">
<table class="t100 tfija va-m">
<tr><th>DUI</th><th>NIT</th><th>Legal</th><th>Accion</th></tr>
<tr><td><input id="DUI" name="DUI" type="text" value="<?php echo @$_POST['DUI']; ?>" /></td><td><input id="NIT" name="NIT" type="text" value="<?php echo @$_POST['NIT']; ?>" /></td><td><input value="acepto" name="acepto" id="acepto" type="checkbox" /> Acepto <a target="_blank" href="<?php echo PROY_URL; ?>bca-terminos-y-condiciones-de-bca.html">Terminos y Condiciones</a> de BCA</td><td><input type="submit" id="buscar" name="buscar" disabled="disabled" value="Búscar globalemente" /></td></tr>
</table>
</form>
<?php echo $resultados_busqueda; ?>
