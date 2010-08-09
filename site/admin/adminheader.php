<?php

include('htmlstart.php');

//start configuring phpMyEdit

$opts = Array();

// MySQL host name, user name, password, database, and table
$opts['hn'] = DBMASTERSERVER;
$opts['un'] = DBMASTERUSER;
$opts['pw'] = DBMASTERPASSWORD;
$opts['db'] = DBNAME;

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 50;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACPVDF';

// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
$opts['navigation'] = 'UGDB';

// Display special page elements
$opts['display'] = array(
	'form'  => true,
	'query' => true,
	'sort'  => true,
	'time'  => true,
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
$opts['language'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) . '-UTF8';

/* Table-level filter capability. If set, it is included in the WHERE clause
   of any generated SELECT statement in SQL query. This gives you ability to
   work only with subset of data from table.

$opts['filters'] = "column1 like '%11%' AND column2<17";
$opts['filters'] = "section_id = 9";
$opts['filters'] = "PMEtable0.sessions_count > 200";
*/

$opts['url'] = array('images' => '/images/pme/');
$opts['logtable'] = 'admin_changelog';

?>
