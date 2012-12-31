<?php

class kml_TimeSpan extends kml_TimePrimitive {

    protected $tagName = 'TimeSpan';

    var $begin;
    var $end;


    /* Constructor */
    function kml_TimeSpan($begin = null, $end = null) {
        parent::kml_TimePrimitive();
        if ($begin !== null) $this->set_begin($begin);
        if ($end !== null) $this->set_end($end);
    }


    /* Assignments */
    function set_begin($begin) { $this->begin = $begin; }
    function set_end($end) { $this->end = $end; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->begin)) $X->appendChild(XML_create_text_element($doc, 'begin', $this->begin));
        if (isset($this->end)) $X->appendChild(XML_create_text_element($doc, 'end', $this->end));

        return $X;
    }
}


/*
$a = new kml_TimeSpan();
$a->dump(false);
*/
