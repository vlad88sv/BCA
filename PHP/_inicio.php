<?php
if (isset($_POST['iniciar_proceder']))
{
    ob_start();
    $ret = _F_usuario_acceder($_POST['iniciar_campo_correo'],$_POST['iniciar_campo_clave']);
    $buffer = ob_get_clean();
    if ($ret != 1)
    {
        echo '<p>Datos de acceso erroneos, por favor intente de nuevo</p>';
        echo '<p>'.$buffer.'</p>';
    }
}

if (S_iniciado())
{
    if (!empty($_POST['iniciar_retornar']))
    {
        header("location: ".$_POST['iniciar_retornar']);
    }
    else
    {
        header("location: ./");
    }
    return;
}

$HEAD_titulo = PROY_NOMBRE . ' - Iniciar sesion';

if (isset($_GET['ref']))
    $_POST['iniciar_retornar'] = $_GET['ref'];

$retorno = empty($_POST['iniciar_retornar']) ? PROY_URL : $_POST['iniciar_retornar'];
echo '<div style="text-align:center;vertical-align:middle;">';
echo '<h1>Inicio de sesión en sistema BCA</h1>';
echo '<form style="margin:auto;width:250px" autocomplete="off" action="'.PROY_URL.'inicio" method="POST">';
echo ui_input("iniciar_retornar", $retorno, "hidden");
echo "<table>";
echo ui_tr(ui_td("Usuario",'a-der')     . ui_td(ui_input("iniciar_campo_correo")));
echo ui_tr(ui_td("Constraseña",'a-der') . ui_td(ui_input("iniciar_campo_clave","","password")));
echo "</table>";
echo ui_input("iniciar_proceder", "Iniciar sesión", "submit")."<br />";
echo "</form>";
echo '</div>';
?>
