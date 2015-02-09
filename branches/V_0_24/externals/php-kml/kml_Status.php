<?php

class kml_Status extends kml_root {

    protected $tagName = 'Status';

    var $code;
    var $request = 'geocode';


    /* Constructor */
    function kml_Status($code = null, $request = null) {
        parent::kml_root();
        if ($code !== null) $this->set_x($code);
        if ($request !== null) $this->set_y($request);
    }


    /* Assignments */
    function set_code($code) { $this->code = $code; }
    function set_request($request) { $this->request = $request; }


   /* Render */
    function render($doc) {
        $X = $doc->createElement($this->tagName);

        if (isset($this->code)) $X->appendChild(XML_create_text_element($doc, 'code', $this->code));
        if (isset($this->request)) $X->appendChild(XML_create_text_element($doc, 'request', $this->request));

        return $X;
    }
}


/*
$a = new kml_Status(array(array(3, 4), array(3, 5)));
$a->dump(true);
*/

