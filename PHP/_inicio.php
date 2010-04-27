<?php
require_once('PHP/terceros/recaptcha/recaptchalib.php');
// Get a key from http://recaptcha.net/api/getkey
$publickey = "6LcygwwAAAAAAAmEXNjPykDoKqAonTbUo5DIBE__";
$privatekey = "6LcygwwAAAAAAHR1dAB98fgX5CqNSftZM_z84t1F";
# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

echo '<div style="text-align:center;vertical-align:middle;">';
echo '<center><strong>Inicio de sesión en sistema BCA</strong></center>';

if (isset($_POST['iniciar_proceder']))
{

    # was there a reCAPTCHA response?
    $recaptcha = false;
    if (isset($_POST["recaptcha_response_field"])) {
            $resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
                $recaptcha = $resp->is_valid;
                $error = $resp->error;
    }

    if(RECAPTCHA && !$recaptcha)
    {
        echo '<p class="error">Ud. no ingresó correctamente las palabras en la imagen.</p>';
    }
    else
    {
        ob_start();
        $ret = _F_usuario_acceder($_POST['iniciar_campo_correo'],$_POST['iniciar_campo_clave']);
        $buffer = ob_get_clean();
        echo '<p class="error">Datos de acceso erroneos, por favor intente de nuevo.</p>';
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
echo '<form style="margin:auto;width:509px" autocomplete="off" action="'.PROY_URL.'inicio" method="POST">';
echo ui_input("iniciar_retornar", $retorno, "hidden");
echo '<table class="t100 vtop">';
echo ui_tr(ui_td("Usuario",'a-der')     . ui_td(ui_input("iniciar_campo_correo")));
echo ui_tr(ui_td("Constraseña",'a-der') . ui_td(ui_input("iniciar_campo_clave","","password")));
if(RECAPTCHA) echo ui_tr(ui_td("Verificación",'a-der'). ui_td(recaptcha_get_html($publickey, $error)));
echo ui_tr('<td colspan="2">'.ui_input("iniciar_proceder", "Iniciar sesión", "submit").'</td>');
echo "</table>";
echo "</form>";
echo '</div>';
?>
