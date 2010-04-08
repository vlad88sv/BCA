<?php
protegerme(false,array(NIVEL_administrador));
$mensajes = '';

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de empleados" FROM empleado LEFT JOIN empresa USING(ID_empresa) GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_empleado = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de empleados activo" FROM empleado LEFT JOIN empresa USING(ID_empresa) WHERE (SELECT COUNT(*) FROM cese WHERE cese.ID_empleado=empleado.ID_empleado AND cese.fecha_cese > ALL(SELECT fecha_inicio FROM historial WHERE historial.ID_empleado=empleado.ID_empleado)) = 0 GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_empleado_activo = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de usuarios" FROM usuario LEFT JOIN empresa USING(ID_empresa) GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_usuario = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de consultas" FROM consulta LEFT JOIN empresa USING(ID_empresa) GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_consulta = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de ceses laborales" FROM cese LEFT JOIN empresa USING(ID_empresa) GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_cese = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de accesos" FROM acceso LEFT JOIN empresa USING(ID_empresa) GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_acceso = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT COALESCE(`siglas`,`razon_social`) AS "Razón social", COUNT(*) AS "Número de accesos" FROM acceso LEFT JOIN empresa USING(ID_empresa) WHERE CONCAT(YEAR(tiempo),MONTH(tiempo))=CONCAT(YEAR(NOW()),MONTH(NOW())) GROUP BY ID_empresa ORDER BY COUNT(*) DESC,COALESCE(`siglas`,`razon_social`)';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_acceso_mensual = db_ui_tabla($restadisticas,'class="t100"');

$cestadisticas = 'SELECT IF(giro="","[Desconocido]",giro) AS "Giro", COUNT(*) AS "Número de empresas" FROM empresa GROUP BY giro ORDER BY COUNT(*) DESC,`Giro`';
$restadisticas = db_consultar($cestadisticas);
$estadisticas_giro = db_ui_tabla($restadisticas,'class="t100"');
?>
<h1>Centro de mensajes y estadísticas para Administradores</h1>
<h2>Mensajes</h2>
<?php echo $mensajes; ?>
<h2>Estadísticas</h2>
<table class="t100 tfija vtop">
<tr>
<td>
<h3>Estadísticas de empleado</h3>
<?php echo $estadisticas_empleado; ?>
</td>
<td>
<h3>Estadísticas de consulta</h3>
<?php echo $estadisticas_consulta; ?>
</td>
</tr>
<tr>
<td>
<h3>Estadísticas de empleado activo</h3>
<?php echo $estadisticas_empleado_activo; ?>   
</td>
<td>
<h3>Estadísticas de usuario</h3>
<?php echo $estadisticas_usuario; ?>
</td>
</tr>
<tr>
<td>
<h3>Estadísticas de cese</h3>
<?php echo $estadisticas_cese; ?>
</td>
<td>
<h3>Estadísticas de giro</h3>
<?php echo $estadisticas_giro; ?>   
</td>
</tr>
<tr>
<td>
<h3>Estadísticas de acceso mensual actual</h3>
<?php echo $estadisticas_acceso_mensual; ?>
</td>
<td>
<h3>Estadísticas de acceso</h3>
<?php echo $estadisticas_acceso; ?>   
</td>
</tr>
</table>