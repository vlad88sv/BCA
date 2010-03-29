<?php
if (!isset($_SESSION['cache_datos_usuario']['su']))
    protegerme();

if(isset($_POST['convertir']))
{
    if (usuario_cache('nivel') == NIVEL_administrador)
        $ID_empresa  = $_SESSION['cache_datos_usuario']['ID_empresa'];

    if ( isset($_SESSION['cache_datos_usuario']['su']) )
        $ID_empresa = $_SESSION['cache_datos_usuario']['su'];

    $ID_usuario = db_obtener('usuario','ID_usuario','ID_empresa='.$_POST['ID_empresa'],'ORDER BY RAND()');
    $_SESSION['cache_datos_usuario'] = _F_usuario_datos($ID_usuario,'ID_usuario');
    $_SESSION['cache_datos_usuario']['nivel'] = NIVEL_empresa; // Por si cae en un no nivel empresa

    if (isset($ID_empresa))
        $_SESSION['cache_datos_usuario']['su'] = $ID_empresa;

    header("Location: ".PROY_URL);
    exit;
}

if(isset($_POST['administrar']))
{
    $_SESSION['cache_datos_usuario']['ID_empresa'] = $_SESSION['cache_datos_usuario']['su'];
    $_SESSION['cache_datos_usuario']['nivel'] = NIVEL_administrador;
    unset($_SESSION['cache_datos_usuario']['su']);
    header("Location: ".PROY_URL);
}
?>
<h1>Ver sistema como empresa</h1>
Esta utilidad le permite cambiar temporalmente su nivel a empresa y su ID_empresa a la que Ud. desee.
<form autocomplete="off" action ="<?php echo PROY_URL_ACTUAL_DINAMICA; ?>" method="post">

Convertirme temportalmente en usuario de la siguiente empresa:
<select name="ID_empresa"><?php echo db_ui_opciones('ID_empresa','razon_social','empresa'); ?></select>
<input name="convertir" type="submit" value="Convertir" />
<br /><input name="administrar" type="submit" value="Volver como administrador" />


</form>
