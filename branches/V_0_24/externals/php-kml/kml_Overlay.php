<?php

class kml_Overlay extends kml_Feature {

    protected $tagName = 'Overlay';

    var $color;
    var $drawOrder;
    var $Icon;


    /* Constructor */
    function kml_Overlay() {
        parent::kml_Feature();
    }


    /* Assignments */
    function set_color($color) { $this->color = $color; }
    function set_drawOrder($drawOrder) { $this->drawOrder = $drawOrder; }
    function set_Icon($Icon) { $this->Icon = $Icon; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->color)) $X->appendChild(XML_create_text_element($doc, 'color', $this->color));
        if (isset($this->drawOrder)) $X->appendChild(XML_create_text_element($doc, 'drawOrder', $this->drawOrder));
        if (isset($this->Icon)) $X->appendChild($this->Icon->render($doc));

        return $X;
    }
}

/*
$a = new kml_Overlay();
$a->set_id('1');
$a->dump(false);
*/
