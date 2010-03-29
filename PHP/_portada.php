<?php
$c = sprintf('SELECT tipo, include, redireccion, ID_contenido, titulo_contenido, contenido, meta_descripcion FROM %s LEFT JOIN %s USING (ID_contenido) WHERE nivel="%s"',db_prefijo.'inicio',db_prefijo.'contenido',usuario_cache('nivel'));
$r = db_consultar($c);

if (!mysql_num_rows($r))
{
    echo sprintf('<h1>Error</h1><p>No ha definido pagina de inicio para el grupo de nivel %s</p>',usuario_cache('nivel'));
}

$f = mysql_fetch_assoc($r);

switch($f['tipo'])
{
    case 'contenido':
        echo '<h1>'.$f['titulo_contenido'].'</h1>';
        eval('?>'.$f["contenido"].'<?');
        break;
    case 'include':
        include_once($f['include']);
        break;
    case 'redireccion':
        header('Location: '.$f['redireccion']);
        break;
}
?>
