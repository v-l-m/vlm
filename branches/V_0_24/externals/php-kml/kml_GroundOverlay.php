<?php

class kml_GroundOverlay extends kml_Overlay {

    protected $tagName = 'GroundOverlay';

    var $altitude;
    var $altitudeMode;
    var $LatLonBox;


    /* Constructor */
    function kml_GroundOverlay($LatLonBox) {
        parent::kml_Overlay();
        $this->set_LatLonBox = $LatLonBox;
    }


    /* Assignments */
    function set_altitude($altitude) { $this->altitude = $altitude; }
    function set_altitudeMode($altitudeMode) { $this->altitudeMode = $altitudeMode; }
    function set_LatLonBox($LatLonBox) { $this->LatLonBox = $LatLonBox; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->altitude)) $X->appendChild(XML_create_text_element($doc, 'altitude', $this->altitude));
        if (isset($this->altitudeMode)) $X->appendChild(XML_create_text_element($doc, 'altitudeMode', $this->altitudeMode));
        $X->appendChild($this->LatLonBox->render($doc));

        return $X;
    }
}

/*
$a = new kml_GroundOverlay();
$a->set_id('1');
$a->set_overlayXY();
$a->dump(false);
*/
