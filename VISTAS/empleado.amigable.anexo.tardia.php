<?php
$strJSDatePicker = "$('.calendario').datepicker({inline: true, maxDate: '+0', dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});";
echo JS_onload($strJSDatePicker);
echo JS_onload('$("#tardia_Tiempo").mask("99:99");');
?>
<h1>Anexar registro de llegada tardía</h1>
<table>
    <tr><th>Fecha de la llegada tarde</th><td><input name="valor_fecha" class="calendario" value="" type="text" style="width:150px"/></td></tr>
    <tr><th>Hora de llegada tarde</th><td><input name="tardia_Tiempo" id="tardia_Tiempo" value="" type="text" style="width:70px"/><select name="tardia_TiempoExtra" style="width:80px"><option value="a.m.">a.m.</option><option value="p.m.">p.m.</option></select></td></tr>
</table>
<input type="hidden" name="tipo" value="tardia" />