<div style="background-color:#EEE">
<h2>1. Motivo del cese laboral de empleado</h2>
<p>El motivo de cese laboral para este empelado fue: <strong><?php echo strtolower($cese['motivo']); ?></strong></p>
</div>

<hr />

<div style="background-color:#FFF">
<h2>2. Valoracion del empleado</h2>
<p>La valoracion de este empelado fue: <strong><?php echo $cese['calificacion']; ?></p></strong>
</div>

<hr />

<div style="background-color:#EEE">
<h2>3. Fecha del cese laboral</h2>
<p>Este empleado dejó de laboral en la fecha: <strong><?php echo $cese['fecha_cese_formato']; ?></p></strong>
</div>

<hr />

<div style="background-color:#FFF">
<h2>4. Causa de la finalización de los servicios</h2>
<p>
<?php if($cese['motivo'] == 'Despido' && !empty($cese['codigo_laboral'])) { ?>
El empleado fue despedido bajo el amparo del siguiente articulo del código laboral:<br /><strong><?php echo $cese['codigo_laboral']; ?></strong>
<?php } elseif ( $cese['motivo'] == 'Despido' ){?>
La empresa optó por no especificar el artículo laboral por el cúal fue realizado el despido.
<?php } else {?>
El motivo de cese laboral de este empleado fue <strong><?php echo strtolower($cese['motivo']); ?></strong> y no requerria especificar el articulo del código laboral por el cual finalizó laborales.
<?php }?>
</p>
</div>

<hr />


<div style="background-color:#EEE">
<h2>5. Comentario adicional sobre la causa que provocó el cese de laborales</h2>
<p>
<?php if($cese['comentario']) { ?>
<?php echo $cese['comentario']; ?></p>
<?php } else {?>
No se especificó ningún comentario.
<?php }?>
</p>
</div>

<hr />

<div style="background-color:#FFF">
<h2>6. Indemnización del empleado</h2>
<p>El empleado <strong><?php echo $cese['indemnizado']; ?></strong> fue indemnizado.</p>
</div>