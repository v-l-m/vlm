<?php

class kml_MultiGeometry extends kml_Geometry {

    protected $tagName = 'MultiGeometry';

    var $Geometries = array();

    /* Constructor */
    function kml_MultiGeometry($Geometries = null) {
        parent::kml_Geometry();
        if ($Geometries !== null) $this->Geometries = $Geometries;
    }


    /* Assignments */
    function add_Geometry($Geometry) { $this->Geometries[] = $Geometry; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        foreach($this->Geometries as $Geometry)
            $X->appendChild($Geometry->render($doc));

        return $X;
    }
}


/*
$a = new kml_MultiGeometry(array(array(3, 4), array(3, 5)));
$a->dump(true);
*/

