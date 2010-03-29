<?php
if(isset($_POST['ajax']))
{
    switch($_POST['ajax'])
    {
        case 'anexo_tipo':
            ajax__anexo_tipo();
            break;
    }
}

function ajax__anexo_tipo()
{
    require_once('PHP/__vital.php');
    protegerme(false,array(NIVEL_administrador,NIVEL_empresa));
    require_once('VISTAS/empleado.amigable.anexo.'.$_POST['tipo'].'.php');
}
?>