<?php
protegerme();
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
$opts['tb'] = db_prefijo.'contenido';

// Name of field which is the unique key
$opts['key'] = 'ID_contenido';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'string';

// Sorting field(s)
$opts['sort_field'] = array('ID_contenido');

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
$opts['triggers']['insert']['before']    = 'PHP/-contenido.trigger.inc';
$opts['triggers']['update']['before']    = 'PHP/-contenido.trigger.inc';

$opts['fdd']['ID_contenido'] = array(
  'name'     => 'ID contenido',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 10,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['enlace_seo'] = array(
  'name'     => 'URL',
  'select'   => 'T',
  'maxlen'   => 200,
  'options'  => 'LVPDR', // auto increment
  'sort'     => true
);

$opts['fdd']['enlace_pista'] = array(
  'name'     => 'SEO URL',
  'select'   => 'T',
  'maxlen'   => 200,
  'options'  => 'ACPD', // auto increment
  'sort'     => true
);

$opts['fdd']['comentario'] = array(
  'name'     => 'Comentario',
  'select'   => 'T',
  'maxlen'   => -1,
  'textarea' => array(
    'rows' => 2,
    'cols' => 70),
  'sort'     => true
);
$opts['fdd']['comentario']['trimlen'] = 10;

$opts['fdd']['titulo_contenido'] = array(
  'name'     => 'Titulo contenido',
  'select'   => 'T',
  'maxlen'   => 250,
  'sort'     => true
);

$opts['fdd']['contenido'] = array(
  'name'     => 'Contenido',
  'select'   => 'T',
  'maxlen'   => -1,
  'options'  => 'ACPD',
  'textarea' => array(
    'rows' => 30,
    'cols' => 70),
  'sort'     => true
);
$opts['fdd']['contenido']['css'] = array('postfix' => 'tinymce');
$opts['fdd']['contenido']['trimlen'] = 50;

$opts['fdd']['ID_usuario'] = array(
  'name'     => 'ID usuario',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);

$opts['fdd']['ID_usuario']['values'] = array(usuario_cache('ID_usuario'));
$opts['fdd']['ID_usuario']['options'] = 'LV';

$opts['fdd']['fecha_creacion'] = array(
  'name'     => 'Fecha creacion',
  'select'   => 'T',
  'maxlen'   => 19,
  'sort'     => true
);
$opts['fdd']['fecha_creacion']['sqlw'] = 'IF(fecha_creacion = "0000-00-00 00:00:00", now(), $val_qas)';

$opts['fdd']['fecha_modificacion'] = array(
  'name'     => 'Fecha modificacion',
  'select'   => 'T',
  'maxlen'   => 19,
  'sort'     => true
);
$opts['fdd']['fecha_modificacion']['sqlw'] = 'now()';

$arrJS[] = 'tiny_mce/jquery.tinymce';
$arrHEAD[] = JS_onload('
$().ready(function() {
    $(".pme-input-0-tinymce").tinymce({
            // Location of TinyMCE script
            script_url : "JS/tiny_mce/tiny_mce.js",

            // General options
            theme : "advanced",
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

            // Theme options
            theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true
    });
});
');

// Now important call to phpMyEdit
new phpMyEdit($opts);
?>