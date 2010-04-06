<?php
define('SQL_CAMPOS_EMPLEADO','empleado.`ID_empleado`, empleado.`ID_empresa`, COALESCE(`siglas`,`razon_social`) as razon_social, empleado.`ID_usuario`, `usuario`, empleado.`fecha_ingreso`, `DUI`, `NIT`, `nombres`, `apellidos`,`op_fecha_nacimiento`, `op_lugar_nacimiento`, `op_direccion`, `op_correo`, `op_estado_civil`, `op_idioma`, `op_informatica`, `op_interes`, `op_referencias`, `op_telefono1`, `op_telefono2`, `op_movil1`, `op_movil2`');

function empleado_obtener_datos($ID_empleado,$ID_empresa=NULL,$DUI=NULL,$NIT=NULL)
{
    $ID_empresa = $ID_empresa ? $ID_empresa : usuario_cache('ID_empresa');

    $PARAMS = 'AND empleado.ID_empresa="'.$ID_empresa.'"';

    if ($DUI && $NIT && $ID_empresa)
        $PARAMS = sprintf(' AND empleado.`DUI` = "%s" AND empleado.`NIT` = "%s" AND empleado.`ID_empresa` = %s', $DUI, $NIT, $ID_empresa);
    else
        $PARAMS .= ' AND empleado.ID_empleado="'.db_codex($ID_empleado).'"';

    $c = sprintf('SELECT %s FROM `empleado` LEFT JOIN `empresa` USING(`ID_empresa`) LEFT JOIN `usuario` USING(`ID_usuario`) LEFT JOIN `cese` USING(ID_empleado) WHERE 1 %s LIMIT 1',SQL_CAMPOS_EMPLEADO, $PARAMS);
    $r = db_consultar($c);

    if (!mysql_num_rows($r))
        return false;
    else
        return mysql_fetch_assoc($r);
}
/*
 * Estados:
 * 1. "jamas cesado" -> con 1 o mas cargos y ningun cese
 * 2. "recontratable" -> con al menos 1 cargo mas antiguo que todas las fechas de cese
 * 3. "recontratado" -> con al menos 1 cargo mas nuevo que todas las fechas de cese
 *
 * $arrRango = array('fecha_inicio' => date('Y-m-d'), 'fecha_final' => date('Y-m-d'))
*/
function empleado_estado($ID_empleado, $arrRango = NULL)
{
    $rango2 = $rango = '';
    if(is_array($arrRango) && isset($arrRango['fecha_inicio']) && isset($arrRango['fecha_final']))
    {
        $rango =  sprintf('AND cese.fecha_cese BETWEEN "%s" AND "%s"', date('Y-m-d', $arrRango['fecha_inicio']), date('Y-m-d', $arrRango['fecha_final']));
        $rango2 =  sprintf('AND historial.fecha_inicio BETWEEN "%s" AND "%s"', date('Y-m-d', $arrRango['fecha_inicio']), date('Y-m-d', $arrRango['fecha_final']));
    }
    $ID_empleado = db_codex($ID_empleado);
    $jamas_cesado = sprintf('(SELECT COUNT(*) FROM cese WHERE ID_empleado=%s %s)',$ID_empleado,$rango);
    $recontratable = sprintf('(SELECT COUNT(*) FROM cese WHERE ID_empleado=%s %s AND cese.fecha_cese > ALL(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=%s %s))',$ID_empleado,$rango,$ID_empleado,$rango2);
    $recontratado = sprintf('(SELECT COUNT(*) FROM cese WHERE ID_empleado=%s %s AND cese.fecha_cese < ANY(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=%s %s))',$ID_empleado,$rango,$ID_empleado,$rango2);

    $c = sprintf('SELECT COALESCE(if(%s > 0, NULL, "jamas cesado"), if(%s > 0, "recontratable", NULL), if(%s > 0, "recontratado", NULL), "Desconocido") AS estado',$jamas_cesado,$recontratable,$recontratado);
    $r = db_consultar($c);
    //echo '<pre>'.$c.'</pre>';

    if (!mysql_num_rows($r))
        return false;

    $f = mysql_fetch_assoc($r);
    return $f['estado'];
}

/*
 *$requeridos=[D=DUI|N=NIT|M=Mote|A=Apellidos]
 *Retorna:array('r','arrErrores', 'arrAdvertencias')
*/
function empleado_buscar($op)
{
    $arrErrores = array();
    $arrAdvertencias = array();
    // Revisamos las opciones

    $funcion = isset($op['funcion']) ? $op['funcion'] : 'empleado_buscar__vista_estandar';
    $arrPARAMS = (array_intersect_key($op,array('ID_empresa' => '','DUI' => '', 'NIT' => '', 'nombres' => '', 'apellidos' => '')));

    $WHERE = $CAMPOS = $PARAMS = $ORDER_BY = $GROUP_BY = '';

    if (count($arrPARAMS))
    {
        foreach($arrPARAMS as $campo => $valor)
        {
            if ( $valor )
            {
                $valor   = db_codex($valor);
                $PARAMS .= " AND empleado.$campo=\"$valor\"";
            }
        }
    }

    if (!empty($op['nombre_completo']))
    {
        $op['nombre_completo'] = db_codex($op['nombre_completo']);
        $PARAMS .= ' AND MATCH (nombres,apellidos) AGAINST("'.$op['nombre_completo'].'" IN BOOLEAN MODE)';

        if (isset($op['limite']) && $op['limite'] == 'solo_activos_si_nombre')
        {
            $op['WHERE'] = 'AND (SELECT COUNT(*) FROM cese WHERE cese.ID_empleado=empleado.ID_empleado AND cese.fecha_cese > ALL(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=empleado.ID_empleado)) = 0';
        }
    }
    
    if (isset($op['limite']) && $op['limite'] == 'solo_activos')
    {
        $op['WHERE'] = 'AND (SELECT COUNT(*) FROM cese WHERE cese.ID_empleado=empleado.ID_empleado AND cese.fecha_cese > ALL(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=empleado.ID_empleado)) = 0';
    }
    
    if (isset($op['limite']) && $op['limite'] == 'solo_inactivos')
    {
        $op['WHERE'] = 'AND (SELECT COUNT(*) FROM cese WHERE cese.ID_empleado=empleado.ID_empleado AND cese.fecha_cese > ALL(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=empleado.ID_empleado)) > 0';
    }

    if (isset($op['estricto']))
    {
        if (empty($arrPARAMS['DUI']) || empty($arrPARAMS['NIT']) || !preg_match('/\d{8}-\d/',$arrPARAMS['DUI']) || !preg_match('/\d{4}-\d{6}-\d{3}-\d/',$arrPARAMS['NIT']))
        {
            $arrErrores[] = 'DUI o NIT faltantes o inválidos';
            $PARAMS = '= 0';
        }
    }

    if (!empty($op['campos']))
    {
        $CAMPOS = ','.$op['campos'];
    }

    if (!empty($op['WHERE']))
    {
        $WHERE = ' '.$op['WHERE'];
    }

    if (!empty($op['ORDER_BY']))
    {
        $ORDER_BY = ' ORDER BY '.$op['ORDER_BY'];
    }
    
    if (!empty($op['GROUP_BY']))
    {
        $GROUP_BY = ' GROUP BY '.$op['GROUP_BY'];
    }

    $c = sprintf('SELECT %s %s FROM `empleado` LEFT JOIN `empresa` USING(ID_empresa) LEFT JOIN `usuario` USING(`ID_usuario`) LEFT JOIN `cese` USING(ID_empleado) WHERE 1 %s %s %s %s',SQL_CAMPOS_EMPLEADO,$CAMPOS,$WHERE,$PARAMS,$ORDER_BY,$GROUP_BY);
    $r = db_consultar($c);

    //echo $c;
    if (!isset($op['no_resultados_no_error']) && !mysql_num_rows($r))
        $arrErrores[] = 'No se encontraron registros de empleados <strong>activos</strong> que coincidan con su criterio de búsqueda';

    return call_user_func($funcion,$r, $arrErrores, $arrAdvertencias, $op);
}

function empleado_buscar__vista_estandar(&$r, &$arrErrores, &$arrAdvertencias, &$op)
{
    if (!count($arrErrores))
    {
        echo '<p class="error">'.join('<p><p class="error">',$arrErrores).'</p>';
        return;
    }
    echo db_ui_tabla($r,'tfija t100');
}
function empleado_buscar__vista_amigable(&$r, &$arrErrores, &$arrAdvertencias, &$op)
{
    $tabla = '';
    $nResultadosOmitidos = 0;
    $buffer = '<h3>Resultados de búsqueda interna de empleados</h3>';
    if (count($arrErrores))
        $buffer .='<p class="error">'.join('<p><p class="error">',$arrErrores).'</p>';
    else
    {
        while ($f = mysql_fetch_assoc($r))
        {
            $tabla .= '<table class="t100">';

            $tabla .= '<tr><th>DUI</th><th>NIT</th><th>Nombre completo</th></tr>';

            $estado = empleado_estado($f['ID_empleado']);
            switch($estado)
            {
                // Activo
                case 'jamas cesado':
                case 'recontratado':
                    $acciones =
                    '<a href="'.PROY_URL.'~empleado?cargo='.$f['ID_empleado'].'" title="Ver cargos laborales del empleado">trayectoria laboral en '.$f['razon_social'].'</a> | '.
                    '<a href="'.PROY_URL.'~empleado?cargo='.$f['ID_empleado'].'&agregar" title="Agregar cargos laborales al empleado">agregar cargo laboral</a> | '.
                    '<a href="'.PROY_URL.'~empleado?cese='.$f['ID_empleado'].'" title="Ver o agregar cese laboral al empleado">crear cese laboral</a> | '.
                    '<a href="'.PROY_URL.'~consulta?DUI='.$f['DUI'].'&NIT='.$f['NIT'].'" title="Ver antecedente laboral del empleado">consulta global de antecedente laboral</a>';
                    if( usuario_cache('ui_rrhh_extendido') == 'si' )
                        $acciones .= ' | <a target="_blank" href="'.PROY_URL.'~empleado?anexo='.$f['ID_empleado'].'" title="Agregar acción de personal">agregar acción de personal</a>';
                    break;

                // No activo
                case 'recontratable':
                    $acciones =
                    '<a href="'.PROY_URL.'~empleado?cargo='.$f['ID_empleado'].'" title="Ver cargos laborales del empleado">trayectoria laboral en '.$f['razon_social'].'</a> | '.
                    '<a href="'.PROY_URL.'~empleado?cargo='.$f['ID_empleado'].'&agregar" title="Reactivar este empleado agregando un nuevo cargo">reactivar empleado</a>';
                    break;
            }
            

            $tabla .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr><tr><tr><th colspan="3">Acciones para este empleado</th></tr><tr><td colspan="3">%s</td></tr>',$f['DUI'],$f['NIT'],$f['apellidos'].', '.$f['nombres'], $acciones);

            $tabla .= '</table>';

/*
            $tabla .= '<br />';

            $tabla .= '<table class="t100 a-izq">';
            $tabla .= '<tr><th colspan="2">Trayectoria laboral</th></tr>';
            $tabla .= '</table>';

            $tabla .=  cargo_obtener_para($f['ID_empresa'],$f['ID_empleado'],'','','cargo_obtener_para__vista_estandar', 'ASC');
*/
            $tabla .= '<hr class="hr-consulta" />';
        }

    }

    return $buffer.$tabla;
}

function empleado_buscar__vista_antecedente(&$r, &$arrErrores, &$arrAdvertencias, &$op)
{
    $buffer = $tabla = '';

    $buffer .= '<h1>Antecedente laboral [periodo del '. strftime('%e de %B de %G',$_POST['fi']) . ' al ' .  strftime('%e de %B de %G',$_POST['ff']).']</h1>';
    
    if (count($arrErrores))
        $buffer .='<p class="error">'.join('<p><p class="error">',$arrErrores).'</p>';
    else
    {
        $f = mysql_fetch_assoc($r);

        $buffer .= '<h2>Resultados de búsqueda de antecende para '.$f['apellidos'].', '.$f['nombres'].' @ '.$f['razon_social'].'</h2>';
        $buffer .= '<p><strong>DUI:</strong> '.$f['DUI'] .' / <strong>NIT:</strong> '. $f['NIT'] .'</p>';

        $tabla .=  cargo_obtener_para($f['ID_empresa'],$f['ID_empleado'],'','','cargo_obtener_para__vista_estandar', 'ASC', $op['arrRango']);

        $tabla .= '<hr class="hr-consulta" />';
    }
    return $buffer.$tabla;
}

function empleado_buscar__vista_agregar(&$r, &$arrErrores, &$arrAdvertencias, &$op)
{
    // Flag 'no_resultados_no_error' en uso, no se toma la falta de resultados como error.
    $tabla = '';
    $buffer = '<p>Resultados de comprobación de búsqueda interna de empleados: ';
    if (mysql_numrows($r))
    {
        $buffer .= 'se encontro un empleado previamente registrado. Fallido.';
        $tabla .= '<table class="t100 tfija">';
        $tabla .= '<tr><th>DUI</th><th>NIT</th><th>Nombre completo</th></tr>';
        while ($f = mysql_fetch_assoc($r))
            $tabla .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',$f['DUI'],$f['NIT'],$f['apellidos'].', '.$f['nombres']);
        $tabla .= '</table>';
    }
    else
    {
        $buffer .= 'no se encontro ningun empleado previamente registrado. Aceptado.';
    }
    $buffer .= '</p>'.$tabla;

    return array($buffer,mysql_numrows($r));
}

function empleado_buscar__vista_consulta_global(&$r, &$arrErrores, &$arrAdvertencias, &$op)
{
    global $arrHEAD;
    
    // Flag 'no_resultados_no_error' en uso, no se toma la falta de resultados como error.
    $tabla = '';
    $buffer = '<h1>Resultado de consulta global de empleado</h1>';

    if (count($arrErrores))
    {
        return '<p class="error">'.join('<p><p class="error">',$arrErrores).'</p>';
    }

    if (!mysql_numrows($r))
    {
        return 'No se encontro ningún empleado registrado con ese DUI y NIT.';
    }

    $arrHEAD[] = JS_onload('
    $("#mostrar-graficos-cargos-laborales-empresa").click(function() {$("#graficos-cargos-laborales-empresa").toggle();});
    $("#mostrar-faltas-laborales").click(function() {$("#graficos-faltas-laborales").toggle();});
    ');

    /*****************************************/
    $tabla .= '<h2>Gráfico de antecedente laboral</h2>';
    $arrBuffer = cargo_obtener_para(0,0,$op['DUI'],$op['NIT'],'cargo_obtener_para__vista_lista','ASC');
    $tabla .= ui_timeline($arrBuffer);

    $tabla .= '<h2><input type="button" class="fs6" id="mostrar-graficos-cargos-laborales-empresa" value="Mostrar/Ocultar" /> gráfico de cargos laborales por empresa</h2>';
    $tabla .= '<div style="display:none" id="graficos-cargos-laborales-empresa">';
    
    $arrBuffer2 = cargo_obtener_para(0,0,$op['DUI'],$op['NIT'],'cargo_obtener_para__vista_lista2','ASC');
    $tabla .= ui_timeline($arrBuffer2,array('grupo_mayor' => true));
    $tabla .= '</div>';

    /*****************************************/

    reset($arrBuffer);
    $fecha_min = time();
    $fecha_max = 0;
    
    while ($dato = each($arrBuffer))
    {

        
        $dato = $dato[1];
        $dato_siguiente = current($arrBuffer);

        $fecha_minima = strtotime($dato['fecha_inicio']);
        $fecha_maxima = strtotime($dato['fecha_fin']);
        $fecha_min = min($fecha_min, $fecha_minima);
        $fecha_max = max($fecha_max, $fecha_maxima);
        
        if($dato['razon_social'] != $dato_siguiente['razon_social'] || !$dato_siguiente)
        {
            
            if (!$dato['flag_cese'] && (date('Ymd',$fecha_max)  == date('Ymd')))
            {
                $fecha_fin = 'a la fecha';
            }
            else
            {
                $fecha_fin = strftime('%e de %B de %G',$fecha_max);
            }
            
            $antecedente = '<form target="_blank" action="' . PROY_URL .'~antecedente" method="post">'.
            ui_input('ID_empresa',$dato['ID_empresa'],'hidden').
            ui_input('DUI',$dato['DUI'],'hidden').
            ui_input('NIT',$dato['NIT'],'hidden').
            ui_input('fi',$fecha_min,'hidden').
            ui_input('ff',strtotime(date('Y-m-d',$fecha_max).'+1 day'),'hidden').
            ui_input('','ver antecedente laboral','submit').
            '</form>';
            
            $tabla .= '<hr class="hr-consulta" />';
            $tabla .= '<table class="t100 tfija">';
            $tabla .= '<tr><th>Empresa</th><th>Nombre registrado</th><th>Periodo laborado</th><th>Acciones</th></tr>';
            $tabla .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', '<acronym title="Contacto para referencia: '.$dato['contacto_rrhh']. ', tel.: ' .$dato['telefono_rrhh'].'">'.$dato['razon_social'].'</acronym>', $dato['apellidos'].', '. $dato['nombres'], strftime('%e de %B de %G',$fecha_min) .' - '.  $fecha_fin, $antecedente);
            $tabla .= '</table>';
            
            $estado = empleado_estado($dato['ID_empleado'],array('fecha_inicio' => strtotime($dato['fecha_inicio'].'+1 day'), 'fecha_final' => strtotime($dato['fecha_fin'].'+1 day') ));

            if ( $estado == 'recontratable' )
            {
                $estado  = 'El empleado no labora en esta empresa. ';
                $estado .= 'Segun el desempeño laboral observado, la empresa <strong>'.$dato['razon_social'].'</strong> '.db_obtener(db_prefijo.'cese','calificacion','ID_empleado='.$dato['ID_empleado'], 'ORDER BY fecha_cese DESC').'.';
            }
            else
            {
                $estado = 'El empleado se encuentra laborando en esta empresa';
            }

            $tabla .= '<table class="t100 a-izq">';
            $tabla .= '<tr><td>Estado laboral actual: ' . $estado . '</td></tr>';
            //if (!empty($f['calificacion'])) $tabla .= '<tr><td>Calificación laboral: '.$f['calificacion'].'</td></tr>';
            //if (!empty($f['comentario_cese'])) $tabla .= '<tr><td>Comentario sobre el cese laboral: '.$f['cese_comentario'].'</td></tr>';
            $tabla .= '</table>';
            
            $fecha_min = time();
            $fecha_max = 0;
        }
    }
    $buffer .= $tabla;

    return $buffer;
}

//true = es mayor, aceptar
function empleado_validar__fecha_dentro_de_periodo_laboral_activo($ID_empleado, $fecha)
{
    $fecha_cese = '(SELECT fecha_cese FROM cese WHERE cese.ID_empleado='.$ID_empleado.' ORDER BY cese.`fecha_cese` DESC LIMIT 1)';
    $fecha_fin = '(SELECT h2.`fecha_inicio` FROM historial AS h2 WHERE h2.ID_empleado='.$ID_empleado.' ORDER BY h2.`fecha_inicio` DESC LIMIT 1)';
    $c = 'SELECT GREATEST( COALESCE('.$fecha_fin.',"1970-01-01"), COALESCE('.$fecha_cese.',"1970-01-01") ) AS fecha_prueba';
    $r = db_consultar($c);
    $f = mysql_fetch_assoc($r);
    
    return ( strtotime($fecha) >= strtotime($f['fecha_prueba']) );
}

function empleado_difundir_actualizaciones($DUI, $NIT, $mensaje)
{
    $c = "SELECT `ID_empresa`, `nombres`, `apellidos` FROM `empleado` WHERE ID_empresa <> ".usuario_cache('ID_empresa')." AND `DUI` = '$DUI' AND `NIT` = '$NIT' AND (SELECT COUNT(*) FROM cese WHERE cese.ID_empleado=empleado.ID_empleado AND cese.fecha_cese > ALL(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=empleado.ID_empleado)) = 0";
    $r = db_consultar($c);
    
    if (!mysql_num_rows($r))
        return false;
    
    while ($f = mysql_fetch_assoc($r))
    {
        $arrID_empresa[] = $f['ID_empresa'];
        $arrMensaje[] = array('tipo' => 'info', 'mensaje' => 'Se le informa que su empleado <strong>'.$f['apellidos'].', '.$f['nombres'].'</strong> '.$mensaje);
        mensaje($arrID_empresa, $arrMensaje);
        unset($arrID_empresa, $arrMensaje);
    }   
    
    

}
?>
