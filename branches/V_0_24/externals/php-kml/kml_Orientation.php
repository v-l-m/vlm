<?php

class kml_Orientation extends kml_root {

    protected $tagName = 'Orientation';

    var $heading;
    var $tilt;
    var $roll;


    /* Constructor */
    function kml_Orientation($heading = null, $tilt = null, $roll = null) {
        parent::kml_Geometry();
        if ($heading !== null) $this->set_heading($heading);
        if ($tilt !== null) $this->set_tilt($tilt);
        if ($roll !== null) $this->set_roll($roll);
    }


    /* Assignments */
    function set_heading($heading) { $this->heading = $heading; }
    function set_tilt($tilt) { $this->tilt = $tilt; }
    function set_roll($roll) { $this->roll = $roll; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->heading)) $X->appendChild(XML_create_text_element($doc, 'heading', $this->heading));
        if (isset($this->tilt)) $X->appendChild(XML_create_text_element($doc, 'tilt', $this->tilt));
        if (isset($this->roll)) $X->appendChild(XML_create_text_element($doc, 'roll', $this->roll));

        return $X;
    }
}


/*
$a = new kml_Orientation(array(array(3, 4), array(3, 5)));
$a->dump(true);
*/

