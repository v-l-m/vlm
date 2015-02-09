<?php

/*******************************/

class kml_Location extends kml_root {

    protected $tagName = 'Location';

    var $longitude;
    var $latitude;
    var $altitude;


    /* Constructor */
    function kml_Location($longitude = null, $latitude = null, $altitude = null) {
        parent::kml_Geometry();
        if ($longitude !== null) $this->set_longitude($longitude);
        if ($latitude !== null) $this->set_latitude($latitude);
        if ($altitude !== null) $this->set_altitude($altitude);
    }


    /* Assignments */
    function set_longitude($longitude) { $this->longitude = $longitude; }
    function set_latitude($latitude) { $this->latitude = $latitude; }
    function set_altitude($altitude) { $this->altitude = $altitude; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->longitude)) $X->appendChild(XML_create_text_element($doc, 'longitude', $this->longitude));
        if (isset($this->latitude)) $X->appendChild(XML_create_text_element($doc, 'latitude', $this->latitude));
        if (isset($this->altitude)) $X->appendChild(XML_create_text_element($doc, 'altitude', $this->altitude));

        return $X;
    }
}


/*
$a = new kml_Location(array(array(3, 4), array(3, 5)));
$a->dump(true);
*/

