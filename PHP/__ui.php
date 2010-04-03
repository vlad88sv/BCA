<?php
function ui_destruir_vacios($cadena)
{
return preg_replace("/(\s)?\w+=\"\"/","",$cadena);
}
function ui_img ($id_gui, $src,$alt="[Imagen no puso ser cargada]"){
	return ui_destruir_vacios('<img id="'.$id_gui.'" alt="'.$alt.'" src="'.$src.'" />');
}
function ui_href ($id_gui, $href, $texto, $clase="", $extra=""){
	return ui_destruir_vacios('<a id="'.$id_gui.'" href="'.$href.'" class="' . $clase . '" ' . $extra . '>'.$texto.'</a>');
}
function ui_A ($id_gui, $texto, $clase="", $extra=""){
	return '<a id="'.$id_gui.'" class="' . $clase . '" ' . $extra . '>'.$texto.'</a>';
}
function ui_combobox ($id_gui, $opciones, $selected = "", $clase="", $estilo="") {
	$opciones = str_replace('value="'.$selected.'"', 'selected="selected" value="'.$selected.'"', $opciones);
	return '<select id="' . $id_gui . '" name="' . $id_gui . '" style="' . $estilo . '">'. $opciones . '</select>';
}
function ui_input ($id_gui, $valor="", $tipo="text", $clase="", $estilo="", $extra ="") {
	$tipo = empty($tipo) ? "text" : $tipo;
	return '<input type="'.$tipo.'" id="' . $id_gui . '" name="' . $id_gui . '" class="' . $clase . '" style="' . $estilo . '" value="' . $valor .'" '.$extra.'></input>';
}
function ui_textarea ($id_gui, $valor="", $clase="", $estilo="") {
	return "<textarea id='$id_gui' name='$id_gui' class='$clase' style='$estilo'>$valor</textarea>";
}
function ui_th ($valor, $clase="") {
	return "<th class='$clase'>$valor</td>";
}
function ui_td ($valor, $clase="", $estilo="") {
	return "<td class='$clase' style='$estilo'>$valor</td>";
}
function ui_tr ($valor) {
	return "<tr>$valor</tr>";
}
function ui_optionbox_nosi ($id_gui, $valorNo = 0, $valorSi = 1, $TextoSi = "Si", $TextoNo = "No") {
	return "<input id='$id_gui' name='$id_gui' type='radio' checked='checked' value='$valorNo'>$TextoNo</input>" . '&nbsp;&nbsp;&nbsp;&nbsp;'."<input id='$id_gui' name='$id_gui' type='radio' value='$valorSi'>$TextoSi</input>";
}
function ui_combobox_o_meses (){
	$opciones = '';
	for ($i = 1; $i < 13; $i++) {
		$opciones .= '<option value=$i>'.strftime('%B', mktime (0,0,0,$i,1,2009)).'</option>';
	}
	return $opciones;
}
function ui_combobox_o_anios (){
	$opciones = '';
	for ($i = 0; $i < 13; $i++) {
		$opciones .= '<option value=$i>'.(date('Y') - $i).'</option>';
	}
	return $opciones;
}

function ui_js_ini_datepicker ($inicio = '', $fin = '', $extra = ''){
	if ($inicio) $inicio = ", minDate: '$inicio'";
	if ($fin) $fin = ", maxDate: '$fin'";
	return "$('.date-pick').datepicker({dateFormat: 'dd-mm-yy' $inicio $fin $extra});";
}
function ui_js_ini_slider ($id_gui, $objetivo = '', $value = '0', $inicio = '0', $fin = '100', $paso = '1'){

	return "$('#slider').slider({value:100, min: 0, max: 500, step: 50, slide: function(event, ui) {	$('#amount').val('$' + ui.value); }	});
		$('#amount').val( $('#slider').slider('value'));
		});
		";
}

function ui_array_a_opciones($array)
{
	$buffer = '';
	foreach ($array as $valor => $texto)
	{
		$buffer .= '<option value="'.$valor.'">'.$texto.'</option>'."\n";
	}

	return $buffer;
}

function ui_simile_timeline(&$arrBuffer,$fecha_min,$fecha_max)
{
	global $arrHEAD;
	$arrHEAD[] = '<script src="https://simile.mit.edu/timeline/api/timeline-api.js" type="text/javascript"></script>';
	$buffer = '<div id="my-timeline" style="height: 100px; border: 1px solid #aaa"></div>';


 $timeline_eventos = '
  var tl;
  var eventSource = new Timeline.DefaultEventSource();
  var dateEvent = new Date();
';

foreach($arrBuffer as $empresa => $datos)
{
	foreach($datos AS $clave => $valor)
	{
	      $timeline_eventos .= '
	      eventSource.add(
	      new Timeline.DefaultEventSource.Event(
		 new Date('.strtotime($valor['fecha_inicio']).'*1000), //start
		 new Date('.strtotime($valor['fecha_fin']).'*1000), //end
		 undefined, //latestStart
		 undefined, //earliestEnd
		 false, //instant
		 "'.$empresa.' :: '.$valor['titulo'].'", //text
		 "" //description
	      )
	      );
	      ';
	      //echo $empresa.' :: '.$valor['titulo'].'<br>';
	}
}

$timeline_constructor = '
  var bandInfos = [
    Timeline.createBandInfo({
        showEventText:  true,
	date:           new Date('.strtotime($fecha_min).'*1000),
        //trackHeight:    0.5,
        //trackGap:       0.2,
        width:          "100%",
        intervalUnit:   Timeline.DateTime.MONTH,
        intervalPixels: 150,
        eventSource: eventSource
    })
  ];
  bandInfos[1].syncWith = 0;
  bandInfos[1].highlight = true;
  bandInfos[1].eventPainter.setLayout(bandInfos[0].eventPainter.getLayout());
  tl = Timeline.create(document.getElementById("my-timeline"), bandInfos);

  ';

  return $buffer."\n".JS_onload($timeline_eventos."\n".$timeline_constructor);

}


function ui_timeline(&$arrBuffer,$op = NULL)
{

	$fecha_min = '99999999';
	$fecha_max = '00000000';
	// En +consulta.php se agrega fecha_inicio y fecha_fin (absolutos) al registro de empleado
	foreach($arrBuffer as $clave => $dato)
	{
	
	    $fecha_minima = date( 'Ym01', strtotime($dato['fecha_inicio']) );
	    $fecha_maxima = date( 'Ymd', strtotime($dato['fecha_fin']) );
	
	    $fecha_min = min($fecha_min, $fecha_minima);
	    $fecha_max = max($fecha_max, $fecha_maxima);
	}

        $divisor = 60 * 60 * 24;
        $pixeles_dia = 1;
	$width = 0;
	$antigua_leyenda = $buffer_fecha = $buffer_anios = '';
	$buffer_datos = $arrAnios = array();

	$fecha = strtotime($fecha_min);
	$fecha_max_time = strtotime($fecha_max);
	$i = 0;
	do 
        {
            $fecha = strtotime($fecha_min  .  ' +'. $i.' month');
            $ancho = (cal_days_in_month(CAL_GREGORIAN, DATE('m',$fecha), DATE('Y',$fecha)) * $pixeles_dia);
            $fechaf = strftime("%b",$fecha);
	    $arrAnios[date('Y',$fecha)][] = $ancho;
	    if (date('m',$fecha) == 12 || date('Ym',$fecha) == date('Ym',$fecha_max_time))
		$propiedades = 'width:'.($ancho-2).'px;border-right:2px solid #000';
	    else
		$propiedades = 'width:'.($ancho-1).'px;border-right:1px solid #DDD';
            $buffer_fecha .= '<div style="text-align:center;background-color:#EEE;float:left;white-space:nowrap;'.$propiedades.'">'.$fechaf.'</div>';
	    $width += $ancho;
	    $i++;
        } while(date('Ym',$fecha) < date('Ym',$fecha_max_time));

	foreach($arrAnios as $anio => $anchos)
	{
		$buffer_anios .= '<div style="text-align:center;background-color:#EEE;float:left;white-space:nowrap;width:'.(array_sum($anchos)-2).'px;border-right:2px solid #000;">'.$anio.'</div>';
	}

	/**********************************************************************/
	$tabla = $td_grupo_mayor = $grupo_mayor = $grupo_mayor_siguiente = $buffer_leyenda = $buffer_contenedor = $buffer_grupo_mayor = '';
	$alto_grupo_mayor = 15;
	reset($arrBuffer);
	
	$tabla .= '<div style="clear:both;overflow-x:scroll;">';
	$titulo = "&nbsp;";
        while ($dato = each($arrBuffer))
        {
		$dato = $dato[1];
		$dato_siguiente = current($arrBuffer);

		$inicio = strtotime($dato['fecha_inicio']) - strtotime($fecha_min);
		$inicio = bcdiv($inicio, bcdiv($divisor, $pixeles_dia));//+(1*$pixeles_dia);
		$cantidad = strtotime($dato['fecha_fin'].'+1 day') - strtotime($dato['fecha_inicio']);
		$cantidad = bcdiv($cantidad, bcdiv($divisor, $pixeles_dia));

		// Div que describe periodo laborado
		if (isset($op['titulo_en_barra']))
			 $titulo =  $dato['titulo'];
		$buffer_datos[] = sprintf('<div style="text-align:center;color:#FFF;font-weight:bolder;line-height:15px;position:absolute;left:%spx;width:%spx;border-right:1px solid #F00;height:15px;background-color:#1A2485;" title="'.$dato['titulo'].'. Del '.$dato['fecha_inicio_formato'].' al '.$dato['fecha_fin_formato'].'.">%s</div>',$inicio,$cantidad-1,$titulo);

		if(isset($op['grupo_mayor']))
		{
			$grupo_mayor = $dato['grupo_mayor'];
			$grupo_mayor_siguiente = $dato_siguiente['grupo_mayor'];
		}

		if($grupo_mayor.$dato['leyenda'] != $grupo_mayor_siguiente.$dato_siguiente['leyenda'] || !$dato_siguiente)
		{
			
			if (!$dato['leyenda'])
				$dato['leyenda'] = '&nbsp;';
			
			$buffer_contenedor = sprintf('<div style="position:relative;border-top:1px solid #DDD;width:%spx;height:15px;color:#000">',$width);
			$buffer_leyenda .= '<div style="line-height:15px;border-top:1px solid #DDD;">'.$dato['leyenda'].'</div>';
			$buffer_contenedor .= implode("\n",$buffer_datos);
			$buffer_contenedor .= '</div>';
			$tabla .= $buffer_contenedor;
			
			if(isset($op['grupo_mayor']) && $grupo_mayor != $grupo_mayor_siguiente)
			{
				$buffer_grupo_mayor .= sprintf('<div style="line-height:%spx;border-top:1px solid #DDD;">'.$dato['grupo_mayor'].'</div>',$alto_grupo_mayor);
				$alto_grupo_mayor = 15;
			}
			else
			{
				$alto_grupo_mayor += 16;
			}
			
			unset($buffer_datos,$buffer_contenedor);

		}
        }
	
	$tabla .= sprintf('<div style="clear:both;float:left;border-top:1px solid #DDD;width:%spx;">%s</div>',$width,$buffer_fecha);
	$tabla .= sprintf('<div style="clear:both;float:left;width:%spx;font-weight:bolder;font-size:1.1em;">%s</div>',$width,$buffer_anios);
	$tabla .= '</div>';

	if(isset($op['grupo_mayor']))
	{
	$td_grupo_mayor =
	'<td style="margin:0;border:none;padding:0;white-space:nowrap;width:200px;vertical-align:top;border-right:1px solid #DDD;">'.
	'<div style="clear:both;overflow-x:scroll;">'.
	$buffer_grupo_mayor.
	'<div style="border-top:1px solid #DDD;background-color:#EEE;line-height:24px;">&nbsp;</div>
	</div>
	</td>';
	}

	return '
	<table style="table-layout:fixed;width:100%;border-collapse:collapse;">
	<tr>
	'.$td_grupo_mayor.'
	<td style="margin:0;border:none;padding:0;white-space:nowrap;width:200px;vertical-align:top;border-right:1px solid #DDD;">'.
	'<div style="clear:both;overflow-x:scroll;">'.
	$buffer_leyenda.
	'<div style="border-top:1px solid #DDD;background-color:#EEE;line-height:24px;">&nbsp;</div>
	</div>
	</td>
	<td style="margin:0;border:none;padding:0;vertical-align:top;">'.
	$tabla.
	'</td>
	</tr>
	</table>';
}
?>
