<?php

class kml_PolyStyle extends kml_ColorStyle {

    protected $tagName = 'PolyStyle';

    var $fill;
    var $outline;

    /* Constructor */
    function kml_PolyStyle($color = null) {
        if ($color !== null) $this->set_color($color);
        parent::kml_ColorStyle();
    }

    /* Assignments */
    function set_fill($fill) { $this->fill = $fill; }
    function set_outline($outline) { $this->outline = $outline; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->fill)) $X->appendChild(XML_create_text_element($doc, 'fill', $this->fill));
        if (isset($this->outline)) $X->appendChild(XML_create_text_element($doc, 'outline', $this->outline));

        return $X;
    }
}

/*
$a = new kml_PolyStyle();
$a->set_fill(4);
$a->dump(false);
*/
