<?php

class kml_ScreenOverlay extends kml_Overlay {

    protected $tagName = 'ScreenOverlay';

    var $overlayXY;
    var $screenXY;
    var $rotationXY;
    var $size;
    var $rotation;


    /* Constructor */
    function kml_ScreenOverlay() {
        parent::kml_Overlay();
    }


    /* Assignments */
    function set_overlayXY($x = '0.5', $y = '0.5', $xunits = 'fraction', $yunits = 'fraction') {
        $this->overlayXY = array('x' => $x, 'y' => $y, 'xunits' => $xunits, 'yunits' => $yunits);
    }
    function set_screenXY($x = '0.5', $y = '0.5', $xunits = 'fraction', $yunits = 'fraction') {
        $this->screenXY = array('x' => $x, 'y' => $y, 'xunits' => $xunits, 'yunits' => $yunits);
    }
    function set_rotationXY($x = '0.5', $y = '0.5', $xunits = 'fraction', $yunits = 'fraction') {
        $this->rotationXY = array('x' => $x, 'y' => $y, 'xunits' => $xunits, 'yunits' => $yunits);
    }
    function set_size($x = '0.5', $y = '0.5', $xunits = 'fraction', $yunits = 'fraction') {
        $this->size = array('x' => $x, 'y' => $y, 'xunits' => $xunits, 'yunits' => $yunits);
    }
    function set_rotation($rotation) { $this->rotation = $rotation; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->overlayXY)) $X->appendChild(XML_create_text_element($doc, 'overlayXY', null, $this->overlayXY));
        if (isset($this->screenXY)) $X->appendChild(XML_create_text_element($doc, 'screenXY', null, $this->screenXY));
        if (isset($this->rotationXY)) $X->appendChild(XML_create_text_element($doc, 'rotationXY', null, $this->rotationXY));
        if (isset($this->size)) $X->appendChild(XML_create_text_element($doc, 'size', null, $this->size));

        if (isset($this->rotation)) $X->appendChild(XML_create_text_element($doc, 'rotation', $this->rotation));

        return $X;
    }
}

/*
$a = new kml_ScreenOverlay();
$a->set_id('1');
$a->set_overlayXY();
$a->dump(false);
*/
