<?php

// headers
$PAGETITLE = "Admin of RACES table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'races';

// Name of field which is the unique key
$opts['key'] = 'idraces';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-deptime');

/* Fields def. helpers */

//suboptimal, should not be done for each load of the page but only when allowing to change something
$dir = "../".DIRECTORY_POLARS ; 
$dh  = opendir($dir);
$select_list="";
while (false !== ($filename = readdir($dh))) {
    if ( !is_dir("$dir/$filename") and ($filename != ".") and ($filename != "..")) {
        //Taking only files
        $keypolar =  substr($filename, 0, -4);
        $list_polars[$keypolar] = substr($keypolar, 5); 
//        print $filename;
    }
}
asort($list_polars);
closedir($dh);

//suboptimal, should not be done for each load of the page but only when allowing to change something

$dir = "../".DIRECTORY_THEMES;
$dh  = opendir($dir);
$select_list="";
while (false !== ($filename = readdir($dh))) {
    if ( is_dir("$dir/$filename") and (substr($filename, 0, 1) != ".") and ($filename != "..")) {
        //Taking only directories
        $list_themes[$filename] = $filename;
    }
}
$list_themes[''] = $filename;
asort($list_themes);
closedir($dh);

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

$opts['fdd']['idraces'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the race',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['raceminimap'] = array(
  'name'     => 'Minimap',
  'options'  => 'LDVF',
  //'select'   => 'T',
  'escape'   => 0,
  'sql'      => 'idraces', 
  'mask'     => "<img style=\"height:40px; \" src=\"/cache/minimaps/%s.png\" />",
  'URL'      => '/admin/uploadracemap.php?idnewrace=$key',
  'help'     => 'Click the minimapto upload the racemap',
  'input'    => 'R',
);

$opts['fdd']['racename'] = array(
  'name'     => 'Name of the race',
  'select'   => 'T',
  'escape'   => false,
  'help'     => 'Basic HTML is for now allowed but discouraged', 
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['started'] = array(
  'name'     => 'Status',
  'help'     => "Status of the race",
  'select'   => 'D',
  'values2'  => Array("-1" => "Finished", "0"=>"Not started", "1" => "Yes"), 
  'maxlen'   => 11,
  'default'  => '0',
  'help'     => 'Status of the race',
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
$opts['fdd']['startlat'] = array(
  'name'     => 'Start lat.',
  'select'   => 'T',
  'sql|LFVD' => 'startlat/1000',
  'maxlen'   => 11,
  'default'  => '0',
  'help'     => 'Please input in Milli deg',
  'sort'     => true
);
$opts['fdd']['startlong'] = array(
  'name'     => 'Start long.',
  'select'   => 'T',
  'maxlen'   => 11,
  'sql|LFVD' => 'startlong/1000',
  'default'  => '0',
  'help'     => 'Please input in Milli deg',
  'sort'     => true
);
$opts['fdd']['boattype'] = array(
  'name'     => 'Boat type',
  'select'   => 'D',
  'values2'  => $list_polars,
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['closetime'] = array(
  'name'     => 'Close time',
  'select'   => 'T',
  'sql|LFVD' => 'FROM_UNIXTIME(closetime)',
  'maxlen'   => 20,
  'calendar' => $calendar_specifications,
  'help|LVFD'=> 'Close time',
  //FIXME: on peut mieux faire / on peut factoriser
  'help|PCA' => '<span id="PME_dhtml_fld_closetime_help" onClick="computeFromEpoc(\'PME_dhtml_fld_closetime\');">Click to check the EPOC value</span>',
  'js'       => Array('required' => true, 'regexp' => '/^[0-9]+$/'),
  'sort'     => true
);
$opts['fdd']['racetype'] = array(
  'name'     => 'Race type',
  'help'     => nl2br("1 => Permanent/record race\n2 => OMORMB"),
  'select'   => 'T',
  'maxlen'   => 11,
//  'select|FLDV'   => 'M',
//  'select|ACP'   => 'C',
//  'values2' => Array(
//      RACE_TYPE_RECORD => "RACE_TYPE_RECORD",
//      RACE_TYPE_OMORMB => "RACE_TYPE_OMORMB",
//      ),
//  'sql' => 'MAKE_SET(`racetype`, 1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 4096)',
    'sort'     => true
);
$opts['fdd']['firstpcttime'] = array(
  'name'     => 'Firstpct time',
  'select'   => 'T',
  'mask'     => '%3.0f %%',
  'maxlen'   => 20,
  'help'     => "Input in %",
  'sort'     => true
);
$opts['fdd']['depend_on'] = array(
  'name'     => 'Depend on',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['qualifying_races'] = array(
  'name'     => 'Qualifying races',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);

/* FIXME: what's that ?
$opts['fdd']['idchallenge'] = array(

  'name'     => 'Id challenge',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
*/
$opts['fdd']['coastpenalty'] = array(
  'name'     => 'Coast penalty',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['bobegin'] = array(
  'name'     => 'Bo Begin',
  'select'   => 'T',
  'options'  => 'ACDPVF',
  'maxlen'   => 20,
  'sql|LFVD' => 'IF (bobegin=0,\'\',FROM_UNIXTIME(bobegin))',
  'default'  => '0',
  'calendar' => $calendar_specifications,
  'help|LVFD'=> 'Start of the blackout',
  //FIXME: on peut mieux faire / on peut factoriser
  'help|PCA' => '<span id="PME_dhtml_fld_bobegin_help" onClick="computeFromEpoc(\'PME_dhtml_fld_bobegin\');">Click to check the EPOC value</span>',
  'sort'     => true
);
$opts['fdd']['boend'] = array(
  'name'     => 'Bo End',
  'select'   => 'T',
  'options'  => 'ACDPVF',
  'maxlen'   => 20,
  'sql|LFVD' => 'IF (boend=0,\'\',FROM_UNIXTIME(boend))',
  'default'  => '0',
  'calendar' => $calendar_specifications,
  'help|LVFD'=> 'End of the blackout',
  //FIXME: on peut mieux faire / on peut factoriser
  'help|PCA' => '<span id="PME_dhtml_fld_boend_help" onClick="computeFromEpoc(\'PME_dhtml_fld_boend\');">Click to check the EPOC value</span>',
  'sort'     => true
);
$opts['fdd']['maxboats'] = array(
  'name'     => 'Maxboats',
  'select'   => 'T',
  'options'  => 'ACDPVF',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['theme'] = array(
  'name'     => 'Theme',
  'select'   => 'D',
  'options'  => 'ACDPVF',
  'maxlen'   => 30,
  'values'   => $list_themes,
  'sort'     => true
);
$opts['fdd']['vacfreq'] = array(
  'name'     => 'Crank. Freq.',
  'select'   => 'T',
  'options'  => 'ACDPVF',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['triggers']['delete']['before'][0] = 'races.TBD.trigger.php';
$opts['triggers']['delete']['after'][0] = 'races.TAD.trigger.php';
$opts['triggers']['insert']['pre'][0] = 'races.TPI.trigger.php';
$opts['triggers']['insert']['after'][0] = 'races.TAI.trigger.php';
//$opts['triggers']['select']['pre'][0] = 'races.TPS.trigger.php';
//$opts['triggers']['update']['before'][0] = 'races.TBU.trigger.php';

//force basic pme class.
//require_once('../externals/phpMyEdit/phpMyEdit.class.php');
//$pmeinstance = new phpMyEdit($opts);

include('adminfooter.php');

?>
