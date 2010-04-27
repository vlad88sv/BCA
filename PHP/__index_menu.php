<?php
/* Construir menues*/
$c = sprintf('SELECT titulo_menu, descripcion_menu, enlace_menu, orden, GROUP_CONCAT(titulo_submenu ORDER BY orden_submenu ASC SEPARATOR "||") AS titulos_submenu, GROUP_CONCAT(enlace_submenu ORDER BY orden_submenu ASC SEPARATOR "||") AS enlaces_submenu FROM %s LEFT JOIN %s USING (ID_menu) WHERE grupo="%s" GROUP BY ID_menu ORDER BY orden ASC ',db_prefijo.'menu',db_prefijo.'submenu',usuario_cache('nivel'));
$r = db_consultar($c);
$arrconst = array('/{PROY_URL}/' => PROY_URL, '/{EMPRESA}/' => usuario_cache('razon_social'));
$primero = ' primero';
if (!mysql_num_rows($r))
{
?>
<div id="wrapper-menu">
<div id="menu_superior">
<center style="font-size:1.2em;color:#FFF;"><?php echo PROY_NOMBRE; ?></center>
</div>
</div> <!-- wrapper menu !-->
<?php
return;
}
while ($f = mysql_fetch_assoc($r))
{
    $arr_submenu = array_combine(explode('||',$f['titulos_submenu']),explode('||',$f['enlaces_submenu']));
    $submenu = '';
    if (strlen($f['titulos_submenu']) > 0)
    {
        $subprimero = 'class="primero"';
        foreach($arr_submenu as $titulo => $enlace)
        {
            $submenu .= '<li '.$subprimero.'><a href="'.$enlace.'">'.$titulo.'</a></li>';
            $subprimero = '';
        }

        $submenu = '<ul>'.$submenu.'</ul>';

    }
    $noactivo = $f['enlace_menu'] ? '' : ' noactivo';
    $menu[] = '<li class="dir'.$primero.$noactivo.'">'.
    '<a href="'.$f['enlace_menu'].'" title="'.$f['descripcion_menu'].'">'.$f['titulo_menu'].'</a>'.
    $submenu.
    '</li>';
    $primero = '';
}
if (isset($_SESSION['cache_datos_usuario']['su']))
    $menu[] = '<li class="dir"><a href="'.PROY_URL.'~su">[SU]</a></li>'
?>
<div id="wrapper-menu">
<div id="menu_superior">
<ul id="nav" class="dropdown dropdown-horizontal">
<?php echo preg_replace(array_keys($arrconst),array_values($arrconst),join('',$menu)); ?>
</ul>
</div>
</div> <!-- wrapper menu !-->
