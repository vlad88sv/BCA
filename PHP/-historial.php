<?php
protegerme(false,array('empresa','administrador'));
/*
 * IMPORTANT NOTE: This generated file contains only a subset of huge amount
 * of options that can be used with phpMyEdit. To get information about all
 * features offered by phpMyEdit, check official documentation. It is available
 * online and also for download on phpMyEdit project management page:
 *
 * http://platon.sk/projects/main_page.php?project_id=5
 *
 * This file was generated by:
 *
 *                    phpMyEdit version: unknown
 *       phpMyEdit.class.php core class: unknown
 *            phpMyEditSetup.php script: unknown
 *              generating setup script: 1.50
 */

// MySQL host name, user name, password, database, and table
$opts['dbh'] = $db_link;
$opts['page_name'] = PROY_URL_ACTUAL;
$opts['tb'] = db_prefijo.'historial';

// Name of field which is the unique key
$opts['key'] = 'ID_historial';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('ID_historial');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACPVDF';

// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
$opts['navigation'] = 'DB';

// Display special page elements
$opts['display'] = array(
	'form'  => true,
	'query' => true,
	'sort'  => true,
	'time'  => false,
	'tabs'  => true
);

// Set default prefixes for variables
$opts['js']['prefix']               = 'PME_js_';
$opts['dhtml']['prefix']            = 'PME_dhtml_';
$opts['cgi']['prefix']['operation'] = 'PME_op_';
$opts['cgi']['prefix']['sys']       = 'PME_sys_';
$opts['cgi']['prefix']['data']      = 'PME_data_';

/* Get the user's default language and use it if possible or you can
   specify particular one you want to use. Refer to official documentation
   for list of available languages. */
$opts['language'] = 'ES-UTF8';

/* Table-level filter capability. If set, it is included in the WHERE clause
   of any generated SELECT statement in SQL query. This gives you ability to
   work only with subset of data from table.

$opts['filters'] = "column1 like '%11%' AND column2<17";
$opts['filters'] = "section_id = 9";
$opts['filters'] = "PMEtable0.sessions_count > 200";
*/

/* Field definitions

Fields will be displayed left to right on the screen in the order in which they
appear in generated list. Here are some most used field options documented.

['name'] is the title used for column headings, etc.;
['maxlen'] maximum length to display add/edit/search input boxes
['trimlen'] maximum length of string content to display in row listing
['width'] is an optional display width specification for the column
          e.g.  ['width'] = '100px';
['mask'] a string that is used by sprintf() to format field output
['sort'] true or false; means the users may sort the display on this column
['strip_tags'] true or false; whether to strip tags from content
['nowrap'] true or false; whether this field should get a NOWRAP
['select'] T - text, N - numeric, D - drop-down, M - multiple selection
['options'] optional parameter to control whether a field is displayed
  L - list, F - filter, A - add, C - change, P - copy, D - delete, V - view
            Another flags are:
            R - indicates that a field is read only
            W - indicates that a field is a password field
            H - indicates that a field is to be hidden and marked as hidden
['URL'] is used to make a field 'clickable' in the display
        e.g.: 'mailto:$value', 'http://$value' or '$page?stuff';
['URLtarget']  HTML target link specification (for example: _blank)
['textarea']['rows'] and/or ['textarea']['cols']
  specifies a textarea is to be used to give multi-line input
  e.g. ['textarea']['rows'] = 5; ['textarea']['cols'] = 10
['values'] restricts user input to the specified constants,
           e.g. ['values'] = array('A','B','C') or ['values'] = range(1,99)
['values']['table'] and ['values']['column'] restricts user input
  to the values found in the specified column of another table
['values']['description'] = 'desc_column'
  The optional ['values']['description'] field allows the value(s) displayed
  to the user to be different to those in the ['values']['column'] field.
  This is useful for giving more meaning to column values. Multiple
  descriptions fields are also possible. Check documentation for this.
*/

$opts['fdd']['ID_historial'] = array(
  'name'     => 'ID historial',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['ID_empresa'] = array(
  'name'     => 'ID empresa',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['ID_empresa']['values']['table'] = db_prefijo.'empresa';
$opts['fdd']['ID_empresa']['values']['column'] = 'ID_empresa';
$opts['fdd']['ID_empresa']['values']['description'] = 'razon_social';

$opts['fdd']['ID_usuario'] = array(
  'name'     => 'ID usuario',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['ID_usuario']['values']['table'] = db_prefijo.'usuario';
$opts['fdd']['ID_usuario']['values']['column'] = 'ID_usuario';
$opts['fdd']['ID_usuario']['values']['description'] = 'usuario';

$opts['fdd']['ID_empleado'] = array(
  'name'     => 'ID empleado',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['ID_empleado']['values']['table'] = db_prefijo.'empleado';
$opts['fdd']['ID_empleado']['values']['column'] = 'ID_empleado';
$opts['fdd']['ID_empleado']['values']['description']['columns'][0] = 'apellidos';
$opts['fdd']['ID_empleado']['values']['description']['divs'][0] = ', ';
$opts['fdd']['ID_empleado']['values']['description']['columns'][1] = 'nombres';

$opts['fdd']['fecha_ingreso'] = array(
  'name'     => 'Fecha ingreso',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['fecha_inicio'] = array(
  'name'     => 'Fecha inicio',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['ID_cargo'] = array(
  'name'     => 'ID cargo',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['cargo'] = array(
  'name'     => 'Cargo',
  'select'   => 'T',
  'maxlen'   => 250,
  'sort'     => true
);

// Now important call to phpMyEdit
new phpMyEdit($opts);

?>
