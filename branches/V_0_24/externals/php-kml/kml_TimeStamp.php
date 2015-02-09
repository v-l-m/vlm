<?php

class kml_TimeSptamp extends kml_TimePrimitive {

    protected $tagName = 'TimeSptamp';

    var $when;


    /* Constructor */
    function kml_TimeSptamp($when = null) {
        parent::kml_TimePrimitive();
        if ($when !== null) $this->set_when($when);
    }


    /* Assignments */
    function set_when($when) { $this->when = $when; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->when)) $X->appendChild(XML_create_text_element($doc, 'when', $this->when));

        return $X;
    }
}

/*
$a = new kml_TimeSptamp();
$a->dump(false);
*/
