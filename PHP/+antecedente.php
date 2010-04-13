<?php
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
    $cese_reporte =  ob_end_clean();
    
    echo $resultados_busqueda.$cese_reporte;
?>