<?php
define('SQL_CAMPOS_CESE','cese.`ID_cese`, `ID_cese`, `ID_usuario`, `ID_empresa`, `ID_empleado`, `fecha_ingreso`, `fecha_cese`, DATE_FORMAT(cese.`fecha_cese`,"%e de %M de %Y") AS "fecha_cese_formato", `motivo`, `calificacion`, `codigo_laboral`, `comentario`, `apelacion`, `aprobado`, `indemnizado`');
empleado_obtener_ceses($ID_empleado, $DUI='', $NIT='')
{
    $PARAMS = 'AND empleado.ID_empleado="'.db_codex($ID_empleado).'"';

    if (usuario_cache('nivel') != NIVEL_administrador && !($DUI || $NIT))
        $PARAMS .=' AND empleado.ID_empresa="'.usuario_cache('ID_empresa').'"';

    if ($DUI || $NIT)
        $PARAMS .= ' AND empleado.DUI="'.db_codex($DUI).'" AND empleado.NIT="'.db_codex($NIT).'"';

    $c = 'SELECT %s FROM cese WHERE '
}
?>
