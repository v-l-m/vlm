<?php

class kml_TimePrimitive extends kml_Object {

    protected $tagName = 'TimePrimitive';


    /* Constructor */
    function kml_TimePrimitive() {
        parent::kml_Object();
    }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);
        return $X;
    }
}


