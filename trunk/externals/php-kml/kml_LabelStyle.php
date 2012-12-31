<?php

class kml_LabelStyle extends kml_ColorStyle {

    protected $tagName = 'LabelStyle';

    var $scale;


    /* Constructor */
    function kml_LabelStyle($color = null, $scale = null) {
        parent::kml_ColorStyle();
		if ($color !== null) $this->set_color($color);
		if ($scale !== null) $this->set_scale($scale);
    }


    /* Assignments */
    function set_scale($scale) { $this->scale = $scale; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->scale)) $X->appendChild(XML_create_text_element($doc, 'scale', $this->scale));

        return $X;
    }
}

/*
$a = new kml_LabelStyle();
$a->set_scale(4);
$a->dump(false);
*/
