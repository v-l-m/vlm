<?php

class kml_Scale extends kml_root {

    protected $tagName = 'Scale';

    var $x;
    var $y;
    var $z;


    /* Constructor */
    function kml_Scale($x = null, $y = null, $z = null) {
        parent::kml_root();
        if ($x !== null) $this->set_x($x);
        if ($y !== null) $this->set_y($y);
        if ($z !== null) $this->set_z($z);
    }


    /* Assignments */
    function set_x($x) { $this->x = $x; }
    function set_y($y) { $this->y = $y; }
    function set_z($z) { $this->z = $z; }


   /* Render */
    function render($doc) {
        $X = $doc->createElement($this->tagName);

        if (isset($this->x)) $X->appendChild(XML_create_text_element($doc, 'x', $this->x));
        if (isset($this->y)) $X->appendChild(XML_create_text_element($doc, 'y', $this->y));
        if (isset($this->z)) $X->appendChild(XML_create_text_element($doc, 'z', $this->z));

        return $X;
    }
}


/*
$a = new kml_Scale(array(array(3, 4), array(3, 5)));
$a->dump(true);
*/

