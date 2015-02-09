<?php

class kml_LatLonAltBox extends kml_root {

    protected $tagName = 'LatLonAltBox';

    var $north;
    var $south;
    var $east;
    var $west;
    var $altitudeMode;
    var $minAltitude;
    var $maxAltitude;

    /* Constructor */
    function kml_LatLonAltBox($north, $south, $east, $west) {
        parent::kml_root();
        $this->set_north($north);
        $this->set_south($south);
        $this->set_east($east);
        $this->set_west($west);
    }

    /* Assignments */
    function set_north($north) { $this->north = $north; }
    function set_south($south) { $this->south = $south; }
    function set_east($east) { $this->east = $east; }
    function set_west($west) { $this->west = $west; }
    function set_altitudeMode($altitudeMode) { $this->altitudeMode = $altitudeMode; }
    function set_minAltitude($minAltitude) { $this->minAltitude = $minAltitude; }
    function set_maxAltitude($maxAltitude) { $this->maxAltitude = $maxAltitude; }

   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        $X->appendChild(XML_create_text_element($doc, 'north', $this->north));
        $X->appendChild(XML_create_text_element($doc, 'south', $this->south));
        $X->appendChild(XML_create_text_element($doc, 'east', $this->east));
        $X->appendChild(XML_create_text_element($doc, 'west', $this->west));
        if (isset($this->altitudeMode)) $X->appendChild(XML_create_text_element($doc, 'altitudeMode', $this->altitudeMode));
        if (isset($this->minAltitude)) $X->appendChild(XML_create_text_element($doc, 'minAltitude', $this->minAltitude));
        if (isset($this->maxAltitude)) $X->appendChild(XML_create_text_element($doc, 'maxAltitude', $this->maxAltitude));

        return $X;
    }
}

/*
$a = new kml_LatLonAltBox(10,20,20,10);
$a->dump(false);
*/
