<?php
$tablausuarios = db_prefijo.'usuario';
define ('SQL_CAMPOS_USUARIO', '`ID_usuario`, `usuario`, `correo`, `nombre`, `ID_empresa`, COALESCE(`siglas`,`razon_social`) as razon_social, `clave`, `mac`, `nivel`, `fecha_acceso`, `ui_rrhh`, `ui_rrhh_extendido`');

function _F_usuario_existe($correo,$campo="correo"){
    global $tablausuarios;
    $nombre_completo = db_codex($correo);
    $resultado = db_consultar ("SELECT correo FROM $tablausuarios where $campo='$correo'");
    if ($resultado) {
        if ( mysql_num_rows($resultado) == 1 )
        {
            return true;
        }
    }
    return false;
}

function _F_usuario_datos($valor,$campo="correo"){
    global $tablausuarios;
    $c = "SELECT ".SQL_CAMPOS_USUARIO." FROM $tablausuarios LEFT JOIN empresa USING (ID_empresa) WHERE $campo='$valor'";
    $resultado = db_consultar ($c);
    return (mysql_num_rows($resultado) > 0) ? db_fila_a_array($resultado) : false;
}

function _F_usuario_agregar($datos){
    global $tablausuarios;
    if ( !_F_usuario_existe($datos['nombre_completo']) ){
        return db_agregar_datos ($tablausuarios, $datos);
    } else {
        return false;
    }
}

function _F_usuario_acceder($usuario, $clave,$enlazar=true){
    global $tablausuarios;
    $usuario = db_codex (trim($usuario));
    $clave =db_codex (trim($clave));

    $c = "SELECT ".SQL_CAMPOS_USUARIO." FROM $tablausuarios LEFT JOIN empresa USING (ID_empresa) WHERE LOWER(usuario)=LOWER('$usuario') AND clave=SHA1('$clave')";
    DEPURAR($c,0);
    $resultado = db_consultar ($c);
    if ($resultado) {
    $n_filas = mysql_num_rows ($resultado);
    if ( $n_filas == 1 ) {
        $_SESSION['autenticado'] = true;
        $_SESSION['cache_datos_usuario'] = db_fila_a_array($resultado);
        $c = "UPDATE $tablausuarios SET fecha_acceso=NOW() WHERE ID_usuario=".usuario_cache('ID_usuario');
        $resultado = db_consultar ($c);
        db_agregar_datos(db_prefijo.'acceso',array('ID_empresa' => usuario_cache('ID_empresa'),'ID_usuario' => usuario_cache('ID_usuario'),'tiempo' => mysql_datetime()));
        return 1;
    }
    }else
    {
        unset ($_SESSION['autenticado']);
        unset ($_SESSION['cache_datos_usuario']);
        echo "Error general al autenticar!"."<br />";
        return 0;
    }

}

function usuario_cache($campo){
    if ( isset($_SESSION) && array_key_exists('cache_datos_usuario', $_SESSION) ) {
        if ( array_key_exists ($campo, $_SESSION['cache_datos_usuario']) ) {
            return $_SESSION['cache_datos_usuario'][$campo];
        }else{
            return NULL;
        }
    }else{
        return NULL;
    }
}
?>
