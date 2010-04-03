<?php
$empleado = empleado_obtener_datos($_GET['cese']);
$editable = true;
if(!$empleado)
{
    echo '<p class="error">Lo sentimos, no se le pueden mostrar los datos de este empleado</p>';
    echo $enlaces_rapidos;
    return;
}

if( empleado_estado($empleado['ID_empleado']) == 'recontratable' )
    $editable = false;

// Si aun no tiene hoja de cese entonces el ingreso el DUI y NIT correcto
// pero cambio el numero de empleado para crear una hoja de cese que no le corresponde
// FIXME: permitir si tiene 1 o mas ceses ingresados
if($editable && $empleado['ID_empresa'] != usuario_cache('ID_empresa'))
{
    echo '<p class="error">Intento de violación al sistema, no se le pueden mostrar los datos de este empleado</p>';
    echo $enlaces_rapidos;
    return;
}

if ( isset($_POST['cancelar']) )  {
    header('Location: '.PROY_URL.'~empleado?cargo='.$empleado['ID_empleado']);
    exit;
}

if(isset($_POST['enviar']) && $editable)
{
    //Validemos
    $valido = true;

    if(!isset($_POST['paso1']))
    {
        echo '<p class="error">Error: debe establecer el motivo del cese laboral del empleado.</p>';
        $valido = false;
    }
    elseif(!in_array($_POST['paso1'],array('Renuncia','Despido','Recorte de Personal')))
    {
        echo '<p class="error">Error: intento de violacion al sistema #1</p>';
        $valido = false;
    }
    elseif (($_POST['paso1'] == 'Despido') && empty($_POST['paso4']))
    {
        $_POST['paso4'] = 'No especificado.';
    }
    elseif ($_POST['paso1'] == 'Despido')
    {
        // Si era un numero, veamos que exista.
        $articulo_codigo_laboral = db_obtener('codigo_laboral_despido','contenido','ID_articulo='.db_codex($_POST['paso4']));
        if (!$articulo_codigo_laboral)
        {
            echo '<p class="error">Error: intento de violacion al sistema #4.2</p>';
            $valido = false;
        }
    }

    if(!isset($_POST['paso2']))
    {
        echo '<p class="error">Error: debe establecer su valoración del empleado.</p>';
        $valido = false;
    }
    elseif(!in_array($_POST['paso2'],array('volveria a contratarlo','no volveria a contratarlo')))
    {
        echo '<p class="error">Error: intento de violacion al sistema #2</p>';
        $valido = false;
    }

    if(!isset($_POST['paso3']))
    {
        echo '<p class="error">Error: debe establecer la fecha del cese laboral del empleado.</p>';
        $valido = false;
    }
    elseif(!preg_match('/\d{4}-\d{2}-\d{2}/',$_POST['paso3']))
    {
        echo '<p class="error">Error: intento de violacion al sistema #3</p>';
        $valido = false;
    }
    elseif (!empleado_validar__fecha_es_mayor_a_ultima_fecha($empleado['ID_empleado'],$_POST['paso3']))
    {
        echo '<p class="error">Ud. esta intentando agregar un cese con fecha anterior al ultimo cargo o cese laboral registrado para este empleado.</p>';
        $valido = false;
    }
    elseif (strtotime($_POST['paso3']) > time())
    {
        echo '<p class="error">Ud. esta intentando agregar un cese con fecha posterior a la actual ['.date('d/m/Y').'].</p>';
        $valido = false;
    }
    
    if(!isset($_POST['paso6']))
    {
        echo '<p class="error">Error: debe establecer si hubo indemnización del empleado.</p>';
        $valido = false;
    }
    elseif(!in_array($_POST['paso6'],array('si','no')))
    {
        echo '<p class="error">Error: intento de violacion al sistema #6</p>';
        $valido = false;
    }

    if ($valido)
    {
        $mensaje[] = array('tipo' => 'info', 'mensaje' => 'El usuario <strong>'.usuario_cache('nombre').'</strong>, añadió un cese laboral para el empleado <strong>'. $empleado['apellidos'] . ', ' . $empleado['nombres'].'</strong>.');
        $mensaje[] = array('tipo' => 'info', 'mensaje' => '[<strong>SISTEMA-RRHH</strong>] El empleado <strong>'. $empleado['apellidos'] . ', ' . $empleado['nombres'].'</strong> ya no es un empleado activo de su empresa.');
        mensaje(array(usuario_cache('ID_empresa')),$mensaje);

        echo '<h1>Registro de cese laboral para  '. $empleado['apellidos'] . ', ' . $empleado['nombres'] . ' @ ' . $empleado['razon_social'].'; creado</h1>';
        echo '<p>El registro del cese laboral ha sido ingresado. Para editar este cese laboral deberá contactar con su ejecutivo de cuenta en ' . PROY_NOMBRE . '.</p>';
        $datos['ID_usuario'] = usuario_cache('ID_usuario');
        $datos['ID_empresa'] = usuario_cache('ID_empresa');
        $datos['ID_empleado'] = $empleado['ID_empleado'];
        $datos['fecha_ingreso'] = mysql_date();
        $datos['motivo'] = $_POST['paso1'];
        $datos['calificacion'] = $_POST['paso2'];
        $datos['fecha_cese'] = $_POST['paso3'];
        $datos['codigo_laboral'] = (($_POST['paso1'] == 'Despido') ? $articulo_codigo_laboral : '');
        $datos['comentario'] = $_POST['paso5'];
        $datos['indemnizado'] = $_POST['paso6'];
        $datos['motivo_interno'] = $_POST['motivo_interno'];
        db_agregar_datos('cese',$datos);
        $editable = false;
    }
}

if (!$editable)
{
    require_once("-empleado.amigable.cese.reporte.php");
    return;
}

$arrCSS[] = 'overcast/jquery-ui-1.8rc3.custom';
$arrJS[] = 'jquery-ui-1.8rc3.min';
$arrJS[] = 'jquery.ui.datepicker-es';
$arrHEAD[] = JS_onload('$(".paso1").click(function(){$("input[name=paso4]").attr("checked",false);$("#codigo-laboral").toggle($(".paso1:checked").val() == "Despido");$("#codigo-laboral-no").toggle($(".paso1:checked").val() != "Despido");});
                       $("#codigo-laboral").accordion({ event: "mouseover", collapsible: true });
                       $("#codigo-laboral-no").show();
                       $("#codigo-laboral").hide();
                       $("#paso3").datepicker({inline: true, maxDate: "+0", dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});
                       $(function(){var d=$("input[type=radio]");var b;var c=function(g){var f=g.target;b=$(f).attr("checked")};var a=function(g){if(g.type=="keypress"&&g.charCode!=32){return false}var f=g.target;if(b){$(f).attr("checked",false)}else{$(f).attr("checked",true)}};$.each(d,function(f,g){var e=$("label[for="+$(this).attr("id")+"]");$(this).bind("mousedown keydown",function(h){c(h)});e.bind("mousedown keydown",function(h){h.target=$("#"+$(this).attr("for"));c(h)});$(this).bind("click",function(h){a(h)})})});
                       ');
?>

<h1>Crear registro de cese laboral para  <?php echo $empleado['apellidos'] . ', ' . $empleado['nombres'] . ' @ ' . $empleado['razon_social']; ?></h1>
<p><strong>DUI:</strong> <?php echo $empleado['DUI']; ?> / <strong>NIT:</strong> <?php echo $empleado['NIT']; ?></p>

<p>En esta seccion puede ingresar los datos del cese de laborales de este empleado para su empresa.</p>
<p class="destacado">Por favor lea cuidadosamente cada uno de los pasos, y no envie el formulario hasta verificar exhaustivamente la informacion ingresada.</p>
<p>No dude en contactar a su ejecutivo de cuenta en <?php echo PROY_NOMBRE; ?> si no comprende como completar este formulario o si tiene dudas al respecto.</p>

<form id="form-cese" autocomplete="off" action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">

<div style="background-color:#EEE">
<h2>Paso 1. Establecer el motivo del cese laboral de empleado</h2>
<div class="opciones">
<input name="paso1" class="paso1" type="radio" value="Renuncia" /> Renuncia
<input name="paso1" class="paso1" type="radio" value="Despido" /> Despido
<input name="paso1" class="paso1" type="radio" value="Recorte de personal" /> Recorte de Personal
</div>
</div>

<hr />

<div style="background-color:#FFF">
<h2>Paso 2. Valoracion del empleado</h2>
<p>Según su observación sobre el desempeño profesional del empleado, Ud.:</p>
<div class="opciones">
<input name="paso2" type="radio" value="volveria a contratarlo" /> Volveria a contratarlo
<input name="paso2" type="radio" value="no volveria a contratarlo" /> No volveria a contratarlo
</div>
</div>

<hr />

<div style="background-color:#EEE">
<h2>Paso 3. Fecha del cese laboral</h2>
<div class="opciones">
<input id="paso3" name="paso3" type="text" value="" />
</div>
</div>

<hr />

<div style="background-color:#FFF">
<h2>Paso 4. Especificar la causa de la finalización de los servicios</h2>
<p><strong>Sugerencia:</strong> no ingrese ninguna de las causas siguientes al menos que Ud. pueda comprobarlo legalmente</p>
<div class="opciones">
<div id="codigo-laboral-no" style="display:none"><p>Este paso no esta disponible para el motivo de cese laboral escogido.</p></div>
<div id="codigo-laboral"><?php echo db_ui_option('paso4','codigo_laboral_despido','resumen','ID_articulo','contenido'); ?></div>
</div>
</div>

<hr />

<div style="background-color:#EEE">
<h2>Paso 5. Comentario adicional sobre la causa que provocó el cese de laborales</h2>
<p><strong>Opcionalmente</strong> ingrese un comentario breve y especifico sobre la causa que provocó el cese de labores. No incluya acusaciones que no sean comprobables legalmente o acompañelas de las palabras "<i>se presume que ...</i>" para evitarle problemas legales en el futuro.</p>
<div class="opciones">
<textarea name="paso5" cols="100" rows="2"></textarea>
</div>
</div>

<hr />

<div style="background-color:#FFF">
<h2>Paso 6. Indemnización del empleado</h2>
<div class="opciones">
<input name="paso6" type="radio" value="no" /> No indemnizó
<input name="paso6" type="radio" value="si" /> Si indemnizó
</div>
</div>

<hr />

<h2>Informacion privada adicional <span style="color:#F00;font-weight:bolder;">[opcional]</span></h2>
<p>Opcionalmente especifique la causa real por la cual Ud. esta despidiendo a esta persona. <span style="color:#F00;">Esta informacion solo podra ser vista por su empresa y se almacena para los propositos que su empresa crea convenientes</span>.</p>
<div class="opciones">
<textarea name="motivo_interno" cols="100" rows="2"></textarea>
</div>

<hr />
<input name="enviar" type="submit" value="Enviar"/> <input name="cancelar" type="submit" value="Cancelar"/>

</form>

<hr />
<?php echo $enlaces_rapidos ?>
