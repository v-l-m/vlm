<?php

class kml_ColorStyle extends kml_Object {

    protected $tagName = 'ColorStyle';

    var $color;
    var $colorMode;


    /* Constructor */
    function kml_ColorStyle($color = null, $colorMode = null) {
        parent::kml_Object();
        if ($color !== null) $this->set_color($color);
        if ($colorMode !== null) $this->set_colorMode($colorMode);
    }


    /* Assignments */
    function set_color($color) { $this->color = $color; }
    function set_colorMode($colorMode) { $this->colorMode = $colorMode; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->color)) $X->appendChild(XML_create_text_element($doc, 'color', $this->color));
        if (isset($this->colorMode)) $X->appendChild(XML_create_text_element($doc, 'colorMode', $this->colorMode));

        return $X;
    }
}

/*
$a = new kml_ColorStyle();
$a->dump(false);
*/
