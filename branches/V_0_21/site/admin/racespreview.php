<?php

// headers
$PAGETITLE = "Admin of RACESPREVIEW table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'racespreview';

// Name of field which is the unique key
$opts['key'] = 'idracespreview';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('deptime');

/* Fields def. helpers */

$calendar_specifications = array(
     'ifFormat'    => '%s', // defaults to the ['strftimemask']
     'firstDay'    => 1,          // 0 = Sunday, 1 = Monday
     'singleClick' => true,       // single or double click to close
     'weekNumbers' => true,       // Show week numbers
     'showsTime'   => true,      // Show time as well as date
     'timeFormat'  => '24',       // 12 or 24 hour clock
     'button'      => true,       // Display button (rather then clickable area)
     'label'       => '...',      // button label (used by phpMyEdit)
     );

//Javascripts...

?>
<script type="text/javascript">
function computeFromEpoc(id) {
    input = document.getElementById(id);
    output = document.getElementById(id+'_help');
    date = new Date(input.value*1000);
    output.innerHTML = 'Date : '+date.toGMTString();
}
</script>

<?php
//Fields definitions

$opts['fdd']['idracespreview'] = array(
  'help'     => 'Unique id of the preview',
  'select'   => 'T',
  'input'  => 'R',
  'options' => 'H',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['idraces'] = array(
  'name'     => '#IdRace',
  'help'     => 'Unique id of the future race',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['racename'] = array(
  'name'     => 'Name of the race',
  'select'   => 'T',
  'escape'   => false,
  'help'     => 'Basic HTML is for now allowed but discouraged', 
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['deptime'] = array(
  'name'     => 'Start',
  'select'   => 'T',
  'sql|LFVD' => 'FROM_UNIXTIME(deptime)',
  'maxlen'   => 20,
  'sort'     => true,
  'js'       => Array('required' => true, 'regexp' => '/^[0-9]+$/'),
  'help|LVFD'=> 'Start time',
  //FIXME: on peut mieux faire / on peut factoriser
  'help|PCA' => '<span id="PME_dhtml_fld_deptime_help" onClick="computeFromEpoc(\'PME_dhtml_fld_deptime\');">Click to check the EPOC value</span>',
  'calendar' => $calendar_specifications,
);
$opts['fdd']['racetype'] = array(
  'name'     => 'Race type',
  'help'     => nl2br("1 => Permanent/record race\n2 => OMORMB"),
  'select'   => 'T',
  'maxlen'   => 11,
  'select|FLDV'   => 'M',
//  'select|ACP'   => 'C',
  'values2|FLDV' => Array(
      RACE_TYPE_RECORD => "RACE_TYPE_RECORD",
      RACE_TYPE_OMORMB => "RACE_TYPE_OMORMB",
      ),
  'sql|FLDV' => 'MAKE_SET(`racetype`, 1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 4096)',
    'sort'     => true
);
$opts['fdd']['comments'] = array(
  'name'     => 'Description',
  'help'     => 'Public description',
  'options'  => 'ACPDV',
  'select'   => 'T',
  'maxlen'   => 250,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['admincomments'] = array(
  'name'     => 'Admin Comments',
  'help'     => 'Admin comments',
  'options'  => 'ACPDV',
  'select'   => 'T',
  'maxlen'   => 250,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);


$opts['fdd']['updated'] = array(
  'name'     => 'Updated',
  'input'    => 'R',
//  'sql|LFVD' => 'FROM_UNIXTIME(updated)',
  'maxlen'   => 20,
  'sort'     => true,
  'help'=> 'Last change',
);

include('adminfooter.php');

?>
