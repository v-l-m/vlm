<?php

class kml_IconStyle extends kml_ColorStyle {

    protected $tagName = 'IconStyle';

    var $scale;
    var $heading;
    var $Icon;
    var $hotSpot;


    /* Constructor */
    function kml_IconStyle() {
        parent::kml_ColorStyle();
    }


    /* Assignments */
    function set_scale($scale) { $this->scale = $scale; }
    function set_heading($heading) { $this->heading = $heading; }
    function set_Icon($Icon) { $this->Icon = $Icon; }
    function set_hotSpot($x = '0.5', $y = '0.5', $xunits = 'fraction', $yunits = 'fraction') {
        $this->hotSpot = array('x' => $x, 'y' => $y, 'xunits' => $xunits, 'yunits' => $yunits);
    }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->scale)) $X->appendChild(XML_create_text_element($doc, 'scale', $this->scale));
        if (isset($this->heading)) $X->appendChild(XML_create_text_element($doc, 'heading', $this->heading));
        if (isset($this->Icon)) $X->appendChild($this->Icon->render($doc));
        if (isset($this->hotSpot)) $X->appendChild(XML_create_text_element($doc, 'hotSpot', null, $this->hotSpot));

        return $X;
    }
}

/*
$a = new kml_IconStyle();
$a->set_hotSpot();
$a->dump(false);
*/
