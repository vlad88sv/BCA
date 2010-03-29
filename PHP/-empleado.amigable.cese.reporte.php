<?php
// Esta bien el Nº de DUI?
if (isset($_POST['DUI']) || isset($_POST['NIT']) || isset($_POST['ID_empresa']))
{
    if (preg_match('/\d{8}-\d/',$_POST['DUI']) && preg_match('/\d{4}-\d{6}-\d{3}-\d/',$_POST['NIT']))
    {
        $empleado = empleado_obtener_datos(0,$_POST['ID_empresa'],$_POST['DUI'],$_POST['NIT']);
    }
    else
    {
        echo '#ERROR#';
        return;
    }
}
if (isset($empleado['ID_empleado']) && is_numeric($empleado['ID_empleado']))
    $PARAMS = sprintf('`ID_empleado` = %s', $empleado['ID_empleado']);
else
{
    echo '#ERROR.2#';
    return;
}

$rango = '';
if(isset($arrRango) && is_array($arrRango) && isset($arrRango['fecha_inicio']) && isset($arrRango['fecha_final']))
{
    $PARAMS .=  sprintf(' AND cese.`fecha_cese` BETWEEN "%s" AND "%s"', date('Y-m-d', $arrRango['fecha_inicio']), date('Y-m-d', $arrRango['fecha_final']));
}
?>
<h1>Reporte de cese laboral para  <?php echo $empleado['apellidos'] . ', ' . $empleado['nombres'] . ' @ ' . $empleado['razon_social']; ?></h1>
<p><strong>DUI:</strong> <?php echo $empleado['DUI']; ?> / <strong>NIT:</strong> <?php echo $empleado['NIT']; ?></p>

<?php
$c = sprintf('SELECT `ID_cese`, `ID_usuario`, `ID_empresa`, `ID_empleado`, `fecha_ingreso`, `fecha_cese`, DATE_FORMAT(`fecha_cese`,"%%e de %%M de %%Y") AS "fecha_cese_formato", `motivo`, `motivo_interno`, `calificacion`, `codigo_laboral`, `comentario`, `apelacion`, `aprobado`, `indemnizado` FROM `cese` WHERE %s', $PARAMS);
$rceses = db_consultar($c);

if (!mysql_num_rows($rceses))
{
    echo '<p class="Destacado">Este empleado no tiene ningun cese laboral registrado por '.$empleado['razon_social'].' en este periodo laboral</p>';
}
else
{
    $cese = mysql_fetch_assoc($rceses);
    require_once('VISTAS/empleado.amigable.cese.reporte.oscar.php');
}

$c = 'SELECT `ID_empresa`, `razon_social`, `siglas`, `fecha_ingreso`, `clasificacion`, `giro`, `actividad_principal`, `registro_fiscal`, `contacto_rrhh`, `telefono_rrhh` FROM `empresa` WHERE ID_empresa='.$empleado['ID_empresa'];
$r = db_consultar($c);
$empresa = mysql_fetch_assoc($r);

?>
<hr />

<h1>Datos publicos de la empresa <?php echo $empresa['razon_social']; ?></h1>
<p>Contacto para referencia directa: <?php echo $empresa['contacto_rrhh']; ?>, teléfono: <?php echo $empresa['telefono_rrhh']; ?></p>

<hr />
<?php echo $enlaces_rapidos ?>
