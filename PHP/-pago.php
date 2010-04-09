<?php
protegerme();
$buffer = '';
if(isset($_POST['saldar']) && isset($_POST['ID_pago']) && is_numeric($_POST['ID_pago']))
{
    $datos['pendiente'] = 0;
    $datos['fecha_saldado'] = mysql_datetime();
    db_actualizar_datos(db_prefijo.'empresa_pago',$datos,'ID_pago='.$_POST['ID_pago']);
    $buffer .= '<h2>Resultado</h2><p>El pago pendiente fue saldado.</p>';
}
if(isset($_POST['grabar']) && is_array($_POST['ID_empresa']))
{
    foreach($_POST['ID_empresa'] as $ID_empresa)
    {
        unset($datos);
        $datos['ID_empresa'] = $ID_empresa;
        $datos['ID_usuario'] = usuario_cache('ID_usuario');
        $datos['fecha_registro'] = mysql_datetime();
        $_POST['pendiente'] = (int)@$_POST['pendiente'];
        if ($_POST['pendiente'] == 0)
            $datos['fecha_saldado'] = mysql_datetime();
        
        if(empty($_POST['cantidad_dias']) && empty($_POST['fecha_fin']))
            $errores[] = 'No especifico duracion en dias ni fecha limite';
        
        if(!empty($_POST['cantidad_dias']) && is_numeric($_POST['cantidad_dias']))
            $_POST['fecha_fin'] = mysql_date($_POST['fecha_inicio'] . ' +'. $_POST['cantidad_dias'].' day');
        
        if(!preg_match('/\d{4}-\d{2}-\d{2}/',$_POST['fecha_inicio']))
            $errores[] = 'La fecha de inicio no es válida';
            
        if(!preg_match('/\d{4}-\d{2}-\d{2}/',$_POST['fecha_fin']))
            $errores[] = 'La fecha de fin no es válida';

        $datos = array_merge($datos, array_intersect_key( $_POST,array_flip(array('fecha_inicio','fecha_fin','pago','pendiente')) ) );
        
        if (isset($errores))
        {
            $buffer .= '<h2>Errores encontrados en la comprobación</h2><p class="error">'.join('</p><p class="error">',$errores).'</p><hr />';
            break;
        }
        else
        {
            db_agregar_datos('empresa_pago',$datos);
            $buffer .= '<h2>Resultado</h2><p>Los datos de pago fueron correctamente ingresados.</p>';
        }    
    }
}

$c = 'SELECT ID_empresa, COALESCE(`siglas`,`razon_social`) as razon_social FROM empresa ORDER BY COALESCE(`siglas`,`razon_social`) ASC';
$r = db_consultar($c);

$ui_lista_empresas = '';
while ($f = mysql_fetch_assoc($r))
    $ui_lista_empresas .= sprintf('<option value="%s">%s</option>',$f['ID_empresa'],$f['razon_social']);


/**** Pagos pendientes ****/
$c = 'SELECT COALESCE(`siglas`,`razon_social`) as "Razón social", CONCAT("$",FORMAT(pago,2)) as "Pago", DATE_FORMAT(fecha_inicio,"%e de %M de %Y") AS "Fecha de inicio", DATE_FORMAT(fecha_fin,"%e de %M de %Y") AS "Fecha de final", CONCAT("<form method=\"post\" action=\"'.PROY_URL_ACTUAL.'\"><input name=\"ID_pago\" type=\"hidden\" value=\"",`ID_pago`,"\" /><input type=\"submit\" name=\"saldar\" value=\"Saldar\" /></form>") AS "Acción" FROM empresa_pago LEFT JOIN empresa USING(ID_empresa) WHERE pendiente=1';
$r = db_consultar($c);
$ui_tabla_pagos_pendientes = db_ui_tabla($r,'class="t100"');

$arrCSS[] = 'overcast/jquery-ui-1.8rc3.custom';
$arrJS[] = 'jquery-ui-1.8rc3.min';
$arrJS[] = 'jquery.ui.datepicker-es';
$arrHEAD[] = JS_onload('$(".datepicker").datepicker({inline: true, dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});');
?>
<h1>Módulo de pago</h1>
<?php echo $buffer; ?>
<form action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">
<table class="t100 vtop wauto">
<tr>
<td>
<p>Seleccione las empresas a las que aplicará el pago</p>
<select name="ID_empresa[]" size="20" multiple="multiple">
<?php echo $ui_lista_empresas; ?>
</select>
</td>
<td>
<p>Monto del pago en dólares ($USA) <input name="pago" type="text" value=""></p>
<p>Fecha de inicio de la válidez del pago <input name="fecha_inicio" class="datepicker" type="text" value="<?php echo mysql_date(); ?>"></p>
<p>Cantidad de días para el cúal el pago será válido (contando desde la fecha de inicio)<br />
<input name="cantidad_dias" type="text" value="0" /> o <input name="fecha_fin" class="datepicker" type="text" value="" />
</p>
<p><input name="pendiente" type="checkbox" checked="checked" value="1" /> Pago pendiente de cobro</p>
</td>
</tr>
</table>
<center><input name="grabar" value="Grabar pagos" type="submit" /></center>
</form>

<hr />
<h1>Pagos pendientes</h1>
<?php echo $ui_tabla_pagos_pendientes; ?>