<?php
if (isset($_GET['tema']))
{
    $c = sprintf('SELECT ID_contenido, titulo_contenido, contenido, meta_descripcion FROM %s WHERE enlace_seo="%s"',db_prefijo.'contenido',db_codex($_GET['tema']));
    $r = db_consultar($c);

    if (!mysql_num_rows($r))
    {
        echo sprintf('<h1>Error</h1><p> el tema requerido no existe. %s</p>',$_GET['tema']);
    }

    $f = mysql_fetch_assoc($r);
    echo '<h1>'.$f['titulo_contenido'].'</h1>';
    eval('?>'.$f["contenido"].'<?');
}
?>
