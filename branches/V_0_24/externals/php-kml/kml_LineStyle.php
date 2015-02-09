<?php

class kml_LineStyle extends kml_ColorStyle {

    protected $tagName = 'LineStyle';

    var $width;


    /* Constructor */
    function kml_LineStyle($color = null, $width = null) {
        parent::kml_ColorStyle();
        if ($color !== null) $this->set_color($color);
        if ($width !== null) $this->set_width($width);
    }


    /* Assignments */
    function set_width($width) { $this->width = $width; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->width)) $X->appendChild(XML_create_text_element($doc, 'width', $this->width));

        return $X;
    }
}

/*
$a = new kml_LineStyle();
$a->set_width(4);
$a->dump(false);
*/
