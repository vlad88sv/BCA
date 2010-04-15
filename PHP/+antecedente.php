<?php
    protegerme(false,array(NIVEL_administrador,NIVEL_empresa));
    // Esta bien el Nº de DUI?
    if (!preg_match('/\d{8}-\d/',$_POST['DUI']))
        $comprobacion_fallos[] = 'Numero de DUI invalido, asegurese de ingresar los nueve digitos, incluyendo el guion. Ej. <strong>12345678-9</strong>';

    // Esta bien el Nº de NIT?
    if (!preg_match('/\d{4}-\d{6}-\d{3}-\d/',$_POST['NIT']))
        $comprobacion_fallos[] = 'Numero de NIT invalido, asegurese de ingresar los catorce digitos, incluyendo guiones. Ej. <strong>1234-567890-1234-4</strong>';

    $op['estricto'] = 1;
    $op['modo'] = 'no_descontar_creditos';
    $op['funcion'] = 'empleado_buscar__vista_antecedente';
    $op['DUI'] = $_POST['DUI'];
    $op['NIT'] = $_POST['NIT'];
    $op['ID_empresa'] = $_POST['ID_empresa'];
    $op['arrRango'] = array('fecha_inicio' => $_POST['fi'], 'fecha_final' => $_POST['ff']);

    $resultados_busqueda = empleado_buscar($op);

    $enlaces_rapidos = '<p>Enlaces rápidos: <a href="'.PROY_URL.'~empleado" alt="Empleados de su empresa">búsqueda de mis empleados</a>, <a href="'.PROY_URL.'~empleado?agregar" alt="Agregar empleado a su empresa">agregar nuevo empleado</a> o <a href="'.PROY_URL.'" title="Pagina de inicio de BCA">pagina de inicio de BCA</a></p>';
    
    $arrRango = array('fecha_inicio' => $_POST['fi'], 'fecha_final' => $_POST['ff']);

    ob_start();
    require_once("-empleado.amigable.cese.reporte.php");
    $cese_reporte =  ob_get_clean();
    $html = $resultados_busqueda.$cese_reporte;

    $titulo = 'Antecedente laboral [periodo del '. strftime('%e de %B de %G',$_POST['fi']) . ' al ' .  strftime('%e de %B de %G',$_POST['ff']).'] - DUI ['.$_POST['DUI'].'] / NIT ['.$_POST['NIT'].']';
    $razon_social = db_obtener(db_prefijo.'empresa','razon_social','ID_empresa = '.$_POST['ID_empresa']);

    $HEAD_titulo = "BCA - $titulo";
    echo $html;
    
    if (!isset($_POST['impresion']))
        echo '<form autocomplete="off" action ="'.PROY_URL_ACTUAL_DINAMICA.'" method="post"><input type="submit" name="impresion" value="Vista para impresión" />'.ui_form_old_post_to_hidden().'</form>';
    else
        $GLOBAL_solo_body = true;
?>