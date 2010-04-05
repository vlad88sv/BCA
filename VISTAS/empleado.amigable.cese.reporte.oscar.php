<table class="tija reporte-tipo-oscar">
    <tr><td style="font-weight:bolder;">Fecha de finalización de labores:</td><td><?php echo $cese['fecha_cese_formato']; ?></td></tr>
    <tr><td style="font-weight:bolder;">Motivo de finalización de labores:</td><td><?php echo $cese['motivo']; ?></td></tr>
    <tr><td style="font-weight:bolder;">Según el desempeño profesional observado, la empresa :</td><td><?php echo ucfirst($cese['calificacion']); ?></td></tr>
    <tr><td colspan="2"><p>El empleado <strong><?php echo $cese['indemnizado']; ?></strong> fue indemnizado.</p></td></tr>
    <tr><td colspan="2" style="text-align:justify;"><strong>Causa de finalización laboral según causales del Código de Trabajo:</strong>
            <?php if($cese['motivo'] == 'Despido' && !empty($cese['codigo_laboral'])) { ?>
            <?php echo $cese['codigo_laboral']; ?>
            <?php } elseif ( $cese['motivo'] == 'Despido' ){?>
            La empresa optó por no especificar el artículo laboral por el cúal fue realizado el despido.
            <?php } else {?>
            El motivo de cese laboral de este empleado fue <strong><?php echo strtolower($cese['motivo']); ?></strong> y no requerria especificar el articulo del código laboral por el cual finalizó labores.
            <?php }?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align:justify;"><strong>Comentario adicional sobre causa de finalización de labores:</strong>
            <?php if($cese['comentario']) { ?>
            <?php echo $cese['comentario']; ?>
            <?php } else {?>
            No se especificó ningún comentario.
            <?php }?>
    </td>
    <?php if (0 && $cese['ID_empresa'] == usuario_cache('ID_empresa')) { ?>
    <tr>
        <td colspan="2" style="text-align:justify;"><strong>Motivo interno especificado como causa real del cese laboral:</strong>
            <?php if($cese['motivo_interno']) { ?>
            <?php echo $cese['motivo_interno']; ?>
            <?php } else {?>
            No se especificó ningún motivo interno/privado adicional como causa de despido.
            <?php }?>
    </td>
    <?php } ?>
    </tr>
</table>