<?php

class kml_LatLonBox extends kml_root {

    protected $tagName = 'LatLonBox';

    var $north;
    var $south;
    var $east;
    var $west;
    var $rotation;

    /* Constructor */
    function kml_LatLonBox($north, $south, $east, $west, $rotation = null) {
        parent::kml_root();
        $this->set_north($north);
        $this->set_south($south);
        $this->set_east($east);
        $this->set_west($west);
        if ($rotation !== null) $this->set_rotation($rotation);
    }

    /* Assignments */
    function set_north($north) { $this->north = $north; }
    function set_south($south) { $this->south = $south; }
    function set_east($east) { $this->east = $east; }
    function set_west($west) { $this->west = $west; }
    function set_rotation($rotation) { $this->rotation = $rotation; }

   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        $X->appendChild(XML_create_text_element($doc, 'north', $this->north));
        $X->appendChild(XML_create_text_element($doc, 'south', $this->south));
        $X->appendChild(XML_create_text_element($doc, 'east', $this->east));
        $X->appendChild(XML_create_text_element($doc, 'west', $this->west));
        if (isset($this->rotation)) $X->appendChild(XML_create_text_element($doc, 'rotation', $this->rotation));

        return $X;
    }
}

/*
$a = new kml_LatLonBox(10,20,20,10);
$a->dump(false);
*/
