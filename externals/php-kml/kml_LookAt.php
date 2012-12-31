<?php

class kml_LookAt extends kml_Object {

    protected $tagName = 'LookAt';

    var $longitude;
    var $latitude;
    var $altitude;
    var $range;
    var $tilt;
    var $heading;
    var $altitudeMode;


    /* Constructor */
    function kml_LookAt($longitude, $latitude, $range) {
        parent::kml_Object();
        $this->set_longitude($longitude);
        $this->set_latitude($latitude);
        $this->set_range($range);
    }


    /* Assignments */
    function set_longitude($longitude) { $this->longitude = $longitude; }
    function set_latitude($latitude) { $this->latitude = $latitude; }
    function set_altitude($altitude) { $this->altitude = $altitude; }
    function set_range($range) { $this->range = $range; }
    function set_tilt($tilt) { $this->tilt = $tilt; }
    function set_heading($heading) { $this->heading = $heading; }
    function set_altitudeMode($altitudeMode) { $this->altitudeMode = $altitudeMode; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        $X->appendChild(XML_create_text_element($doc, 'longitude', $this->longitude));
        $X->appendChild(XML_create_text_element($doc, 'latitude',  $this->latitude));
        if (isset($this->altitude)) $X->appendChild(XML_create_text_element($doc, 'altitude',  $this->altitude));
        $X->appendChild(XML_create_text_element($doc, 'range',     $this->range));
        if (isset($this->tilt)) $X->appendChild(XML_create_text_element($doc, 'tilt',         $this->tilt));
        if (isset($this->heading)) $X->appendChild(XML_create_text_element($doc, 'heading',      $this->heading));
        if (isset($this->altitudeMode)) $X->appendChild(XML_create_text_element($doc, 'altitudeMode', $this->altitudeMode));

        return $X;
    }
}


