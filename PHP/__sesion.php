<?php
session_start();

function _F_sesion_cerrar(){
   unset ( $_SESSION );
   session_destroy ();
   header("location: ./");
}

function S_iniciado(){
   return isset($_SESSION['autenticado']);
}

if (!S_iniciado())
   $_SESSION['cache_datos_usuario']['nivel'] = NIVEL_visitante;
?>
