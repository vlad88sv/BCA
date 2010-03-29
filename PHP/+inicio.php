<?php
protegerme(false,array(NIVEL_administrador,NIVEL_empresa));
$c        = sprintf('SELECT `ID_mensaje`, `ID_empresa`, `tipo`, `mensaje`, `leido` FROM `mensaje` WHERE ID_empresa=%s',usuario_cache('ID_empresa'));
$r        = db_consultar($c);
$mensajes = '';
if (mysql_num_rows($r))
{
  while ($f = mysql_fetch_assoc($r))
  {
    $mensajes .= sprintf('<p class="mensaje_%s">%s</p>',$f['tipo'],$f['mensaje']);
  }
}
else
{
  $mensaje = '<p class="destacado">Su empresa no tiene nuevos mensajes.</p>';
}
?>
<h1>Centro de mensajes y estadísticas | <?php echo usuario_cache('usuario'); ?> | <?php echo usuario_cache('razon_social'); ?></h1><h1>
<h2>Mensajes</h2>
<?php echo $mensajes; ?>
