<?php
function cargo_obtener_para($ID_empresa, $ID_empleado, $DUI='', $NIT='', $funcion='cargo_obtener_para__vista_estandar', $ORDEN = 'ASC', $arrRango = NULL)
{
    $rango = '';
    if(is_array($arrRango) && isset($arrRango['fecha_inicio']) && isset($arrRango['fecha_final']))
    {
        $rango =  sprintf('AND h1.fecha_inicio BETWEEN "%s" AND "%s"', date('Y-m-d', $arrRango['fecha_inicio']), date('Y-m-d', $arrRango['fecha_final']));
    }
    
    $fecha_cese     = '(SELECT fecha_cese FROM cese WHERE cese.ID_empleado=h1.ID_empleado AND fecha_cese > h1.fecha_inicio)';
    $fecha_fin      = '(SELECT DATE_SUB(h2.`fecha_inicio`, INTERVAL 1 DAY) FROM historial AS h2 WHERE h2.ID_empleado=h1.ID_empleado AND h2.`fecha_inicio` > h1.`fecha_inicio`  ORDER BY h2.`fecha_inicio` LIMIT 1)';
    $PARAMS         = '';

    //$ID_empresa = $ID_empresa ? $ID_empresa : usuario_cache('ID_empresa');

    //$PARAMS = 'AND empleado.ID_empresa="'.$ID_empresa.'"';

    if ($DUI && $NIT)
        $PARAMS = sprintf(' AND h1.ID_empleado IN (SELECT empleado.ID_empleado FROM empleado WHERE empleado.`DUI` = "%s" AND empleado.`NIT` = "%s")', $DUI, $NIT);
    else
        $PARAMS = ' AND h1.ID_empleado="'.db_codex($ID_empleado).'"';

    $c = 'SELECT COALESCE(`siglas`,`razon_social`) AS razon_social, `ID_historial`, h1.`ID_empresa`, `ID_empleado`, h1.`fecha_ingreso`, `fecha_inicio`, DATE_FORMAT(`fecha_inicio`,"%e de %M de %Y") AS fecha_inicio_formato, @fecha_fin := LEAST( COALESCE('.$fecha_fin.', DATE(NOW())),COALESCE(@cese := '.$fecha_cese.', DATE(NOW())) ) AS fecha_fin, DATE_FORMAT(@fecha_fin,"%e de %M de %Y") AS "fecha_fin_formato", IF(@cese,1,0) AS "flag_cese", (TO_DAYS(@fecha_fin) - TO_DAYS(fecha_inicio)) AS "dias_laborados", IF(`ID_cargo`, CONCAT(`titulo_categoria`, "::" , `titulo_cargo`), `cargo`) AS cargo, empleado.`nombres`, empleado.`apellidos`, empleado.`DUI`, empleado.`NIT`, empresa.`telefono_rrhh`, empresa.`contacto_rrhh` FROM `historial` AS h1 LEFT JOIN `empresa` USING(ID_empresa) LEFT JOIN `cargo` USING(`ID_cargo`) LEFT JOIN `categoria` USING(`ID_categoria`) LEFT JOIN `empleado` USING(ID_empleado) WHERE 1 '.$PARAMS.' '. $rango .' ORDER BY h1.`fecha_inicio` '.$ORDEN;
    $r = db_consultar($c);
    //echo $c.'<br><br>';
    return call_user_func($funcion,$r);
}

function cargo_obtener_para__vista_estandar(&$r)
{
    if (!mysql_num_rows($r))
        return false;

    $fecha_min = '99999999';
    $fecha_max = '00000000';
    $arrBuffer = array();
    $buffer = '<table class="tfija t100">';
    $buffer .= '<tr><th>Cargo</th><th>Fecha de inicio</th><th>Fecha Fin</th></tr>';
    while ($f = mysql_fetch_assoc($r))
    {
        $arrBuffer[] = array_merge($f,array('titulo' => $f['cargo'],'razon_social' => $f['cargo']));
        
        if (!$f['flag_cese'] && (date('Ymd',strtotime($f['fecha_fin']))  == date('Ymd')))
            $f['fecha_fin_formato'] = 'a la fecha';
        
        $buffer .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',$f['cargo'],$f['fecha_inicio_formato'],$f['fecha_fin_formato']);
    }
    $buffer .= '</table>';
    mysql_data_seek($r,0);
    $f = mysql_fetch_assoc($r);
    $tabla = '<h2>Gráfico de cargos laborales en <strong>'.$f['razon_social'].'</strong></h2>';

    $tabla .= ui_timeline($arrBuffer);
    return $buffer.$tabla;
}

function cargo_obtener_para__vista_cargo_amigable(&$r)
{
    if (!mysql_num_rows($r))
        return false;

    $fecha_min = '99999999';
    $fecha_max = '00000000';
    $arrBuffer = array();

    $buffer = '<table class="tfija t100">';
    $buffer .= '<tr><th>Cargo</th><th>Fecha de inicio</th><th>Fecha Fin</th></tr>';
    while ($f = mysql_fetch_assoc($r))
    {        
        $arrBuffer[] = array('razon_social' => $f['cargo'],'fecha_inicio' => $f['fecha_inicio'] , 'fecha_fin' => $f['fecha_fin'], 'fecha_inicio_formato' => $f['fecha_inicio_formato'] , 'fecha_fin_formato' => $f['fecha_fin_formato'], 'flag_cese' => $f['flag_cese'], 'titulo' => $f['cargo']);

        $fecha_minima = date( 'Ym01', strtotime($f['fecha_inicio']) );
        $fecha_maxima = date( 'Ymd',  strtotime($f['fecha_fin']) );
        $fecha_min = min($fecha_min, $fecha_minima);
        $fecha_max = max($fecha_max, $fecha_maxima);
        
        if (!$f['flag_cese'] && (date('Ymd',strtotime($f['fecha_fin']))  == date('Ymd')))
            $f['fecha_fin_formato'] = 'a la fecha';
        
        $buffer .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',$f['cargo'],$f['fecha_inicio_formato'],$f['fecha_fin_formato']);
    }
    $buffer .= '</table>';

    mysql_data_seek($r,0);

    $f = mysql_fetch_assoc($r);
    $tabla = '<h2>Gráfico de antecedente de cargos laborales en '.$f['razon_social'].'</h2>';
    mysql_data_seek($r,0);

    $tabla .= ui_timeline($arrBuffer);

    return $buffer.$tabla;
}

function cargo_obtener_para__vista_lista(&$r)
{

    if (!mysql_num_rows($r))
        return false;

    $rangos = array();

    while ($f = mysql_fetch_assoc($r))
    {
        if (!$f['flag_cese'] && (date('Ymd',strtotime($f['fecha_fin']))  == date('Ymd')))
            $f['fecha_fin_formato'] = 'a la fecha';
        $rangos[] = array_merge($f,array('titulo' => $f['cargo']));
        
    }

    return $rangos;
}

function cargo_obtener_para__vista_lista2(&$r)
{

    if (!mysql_num_rows($r))
        return false;

    $rangos = array();

    while ($f = mysql_fetch_assoc($r))
    {
        if (!$f['flag_cese'] && (date('Ymd',strtotime($f['fecha_fin']))  == date('Ymd')))
            $f['fecha_fin_formato'] = 'a la fecha';
        $rangos[] = array_merge($f,array('titulo' => $f['razon_social'],'razon_social' => $f['cargo'],'grupo_mayor' => $f['razon_social']));
    }

    return $rangos;
}
?>
