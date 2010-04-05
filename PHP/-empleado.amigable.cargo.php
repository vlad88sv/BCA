<?php
$empleado = empleado_obtener_datos($_GET['cargo']);

if(!$empleado)
{
    echo '<p class="error">Lo sentimos, no se le pueden mostrar los datos de este empleado</p>';
    echo $enlaces_rapidos;
    return;
}

if(isset($_GET['agregar']))
{
    require_once('-empleado.amigable.cargo.agregar.php');
    return;
}

$cargos = cargo_obtener_para(usuario_cache('ID_empresa'),$_GET['cargo'],'','','cargo_obtener_para__vista_cargo_amigable','ASC');


/*************** Gráfico de faltas ******************/
$faltas = '';
if( usuario_cache('ui_rrhh_extendido') == 'si' ) {
    $cfaltas = 'SELECT `ID_empleado_anexo`, `ID_empleado`, `categoria`, `detalle1`, `detalle2`, `valor`, CONCAT("<table class=\'t100 tfija\'><tr><th>Fecha</th><th>Hora</th><th>Intensidad</th><th>Justificación</th></tr>",GROUP_CONCAT("<tr>", "<td>", DATE(`valor_fecha`), "</td><td>", TIME(`valor_fecha`), "</td><td>", `detalle1`, "</td><td>", `detalle2`, "</td>", "</tr>" SEPARATOR ""),"</table>") AS titulo, CONCAT(SUBSTR(`valor_fecha`,1,8),"01") AS fecha_inicio, LAST_DAY(`valor_fecha`) AS fecha_fin, `fecha_registro`, COUNT(*) AS cuenta FROM `empleado_anexo` LEFT JOIN `empleado` USING(ID_empleado) WHERE `empleado`.`ID_empresa` = '.usuario_cache('ID_empresa') .' AND grupo="accion_de_personal" AND `empleado`.`ID_empleado` = "'.$_GET['cargo'].'" GROUP BY categoria,CONCAT(detalle1,detalle2),CONCAT(YEAR(valor_fecha),".",MONTH(valor_fecha))';
    $rfaltas = db_consultar($cfaltas);
    
    if (mysql_num_rows($rfaltas))
    {
        $faltas .= '<h2> Gráfico de faltas </h2>';
        while ( $f = mysql_fetch_assoc($rfaltas) )
            $arrFaltas[] = array('grupo_mayor' => $f['categoria'], 'leyenda' => '<span title="Intensidad">'.$f['detalle1'].'</span> | <span title="Justificación">'.$f['detalle2'].'</span>', 'titulo' => $f['titulo'], 'contenido' => $f['cuenta'], 'fecha_inicio' => $f['fecha_inicio'], 'fecha_fin' => $f['fecha_fin'], 'fecha_inicio_formato' => $f['fecha_inicio'], 'fecha_fin_formato' => $f['fecha_fin']);
            
        $faltas .= ui_timeline($arrFaltas, array('grupo_mayor' => true, 'contenido_en_barra' => true));
    }
    else
    {
        $faltas .= '<h2>Faltas laborales</h2>';
        $faltas .= '<p>El empleado no tiene faltas laborales registradas en esta empresa</p>';
    }
    
    $faltas .= '<hr />';
}
/*************** 8< ******************/

$c = 'SELECT ID_categoria, titulo_categoria, GROUP_CONCAT(CONCAT(ID_cargo,"|",titulo_cargo) SEPARATOR "||") AS cargos FROM categoria LEFT JOIN cargo USING(ID_categoria) GROUP BY ID_categoria';
$r = db_consultar($c);
$opciones_categorias_cargos = '';
while ( $f = mysql_fetch_assoc($r) )
    $opciones_categorias_cargos .= '<optgroup label="'.$f['titulo_categoria'].'">'.preg_replace('/(.*?)\|(.*?)\|\|/','<option value="$1">$2</option>',$f['cargos']).'</optgroup>';
?>
<h1>Trayectoria laboral en <?php echo $empleado['razon_social']; ?></h1>
<h2>Cargos laborales para <?php echo $empleado['apellidos'] . ', ' . $empleado['nombres'] . ' @ ' . $empleado['razon_social']; ?></h2>

<p><strong>DUI:</strong> <?php echo $empleado['DUI']; ?> / <strong>NIT:</strong> <?php echo $empleado['NIT']; ?></p>

<?php echo $cargos; ?>

<hr />

<?php echo $faltas; ?>

<?php if( usuario_cache('ui_rrhh_extendido') == 'si' ) { ?>
<h2>Datos adicionales que Ud. ingresó para este empleado</h2>
<table class="t100 tfija">
<tr><th>Fecha de nacimiento</th><td><input name="op_fecha_nacimiento" readonly="readonly" type="text" value="<?php echo $empleado['op_fecha_nacimiento']; ?>" /></td></tr>
<tr><th>Lugar de nacimiento</th><td><input name="op_lugar_nacimiento" readonly="readonly" type="text" value="<?php echo $empleado['op_lugar_nacimiento']; ?>" /></td></tr>
<tr><th>Dirección</th><td><input name="op_direccion" readonly="readonly" type="text" value="<?php echo $empleado['op_direccion']; ?>" /></td></tr>
<tr><th>Correo electrónico</th><td><input name="op_correo" readonly="readonly" type="text" value="<?php echo $empleado['op_correo']; ?>" /></td></tr>
<tr><th>Estado civíl</th><td><input name="op_estado_civil" readonly="readonly" type="text" value="<?php echo $empleado['op_estado_civil']; ?>" /></td></tr>
<tr><th>Idiomas</th><td><input name="op_idioma" readonly="readonly" type="text" value="<?php echo $empleado['op_idioma']; ?>" /></td></tr>
<tr><th>Informática</th><td><input name="op_informatica" readonly="readonly" type="text" value="<?php echo $empleado['op_informatica']; ?>" /></td></tr>
<tr><th>Otros datos de interés</th><td><input name="op_interes" readonly="readonly" type="text" value="<?php echo $empleado['op_interes']; ?>" /></td></tr>
<tr><th>Referencias</th><td><input name="op_referencias" readonly="readonly" type="text" value="<?php echo $empleado['op_referencias']; ?>" /></td></tr>
<tr><th>Teléfono 1</th><td><input name="op_idioma" readonly="readonly" type="text" value="<?php echo @$empleado['op_telefono1']; ?>" /></td></tr>
<tr><th>Teléfono 2</th><td><input name="op_idioma" readonly="readonly" type="text" value="<?php echo @$empleado['op_telefono2']; ?>" /></td></tr>
<tr><th>Móvil 1</th><td><input name="op_idioma" readonly="readonly" type="text" value="<?php echo @$empleado['op_movil1']; ?>" /></td></tr>
<tr><th>Móvil 2</th><td><input name="op_idioma" readonly="readonly" type="text" value="<?php echo @$empleado['op_movil2']; ?>" /></td></tr>
</table>
<hr />
<?php } ?>

Opciones para este empleado: 
<a href="<?php echo PROY_URL; ?>~empleado?cargo=<?php echo $empleado['ID_empleado']; ?>&agregar" alt="Agregar nuevo cargo laboral para este empleado">agregar cargo laboral</a> /
<a href="<?php echo PROY_URL; ?>~empleado?cese=<?php echo $empleado['ID_empleado']; ?>" alt="Agregar cese laboral a este empleado">crear cese laboral</a> / 
<a href="<?php echo PROY_URL; ?>~consulta?DUI='<?php echo $empleado['DUI']; ?>&NIT=<?php echo $empleado['NIT']; ?>" title="Ver antecedente laboral del empleado">ver antecedente laboral global</a>


<?php echo $enlaces_rapidos ?>
