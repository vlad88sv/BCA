<?php
$strJSDatePicker = "$('.calendario').datepicker({inline: true, maxDate: '+0', dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});";
echo JS_onload($strJSDatePicker);
?>
<h1>Anexar registro de falta laboral</h1>
<table>
    <tr><th>Fecha de la falta</th><td><input name="valor_fecha" class="calendario" value="" type="text" style="width:150px"/></td></tr>
    <tr><th>Tipo de falta</th><td><select name="subcategoria" style="width:150px"><option value="leve">Leve</option><option value="moderada">Moderada</option><option value="grave">Grave</option></select></td></tr>
    <tr><th>Causa, notas y comentarios</th><td><textarea name="valor" style="width:150px;height:200px;"></textarea></td></tr>
</table>
<input type="hidden" name="tipo" value="falta" />