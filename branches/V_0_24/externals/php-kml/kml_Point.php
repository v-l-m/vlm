<?php

class kml_Point extends kml_Geometry {

    protected $tagName = 'Point';

    var $extrude;
    var $tesselate;
    var $altitudeMode;
    var $coordinates;


    /* Constructor */
    function kml_Point($lon, $lat, $alt = null) {
        parent::kml_Geometry();
        $this->coordinates = array($lon, $lat);
        if ($alt !== null) $this->coordinates[] = $alt;
    }


    /* Assignments */
    function set_extrude($extrude) { $this->extrude = (int)$extrude; }
    function set_tesselate($tesselate) { $this->tesselate = (int)$tesselate; }
    function set_altitudeMode($altitudeMode) { $this->altitudeMode = $altitudeMode; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->extrude)) $X->appendChild(XML_create_text_element($doc, 'extrude', $this->extrude));
        if (isset($this->tesselate)) $X->appendChild(XML_create_text_element($doc, 'tesselate', $this->tesselate));
        if (isset($this->altitudeMode)) $X->appendChild(XML_create_text_element($doc, 'altitudeMode', $this->altitudeMode));

        $X->appendChild(XML_create_text_element($doc, 'coordinates', join(',', $this->coordinates)));

        return $X;
    }
}




//$a = new kml_Point();
//$a->dump();


