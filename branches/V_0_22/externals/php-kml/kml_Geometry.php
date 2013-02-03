<?php

class kml_Geometry extends kml_Object {

    protected $tagName = 'Geometry';

    /* Constructor */
    function kml_Geometry() {
        parent::kml_Object();
    }

   /* Render */
    function render($doc) {
        $X = parent::render($doc);
        return $X;
    }
}


/*
$a = new kml_Geometry(false);
$a->dump(false);
*/

