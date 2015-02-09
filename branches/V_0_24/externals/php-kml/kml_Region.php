<?php

class kml_Region extends kml_Object {

    protected $tagName = 'Region';

    var $LatLonAltBox;
    var $Lod;


    /* Constructor */
    function kml_Region($LatLonAltBox) {
        parent::kml_Object();
        $this->set_LatLonAltBox($LatLonAltBox);
    }


    /* Assignments */
    function set_LatLonAltBox($LatLonAltBox) { $this->LatLonAltBox = $LatLonAltBox; }
    function set_Lod($Lod) { $this->Lod = $Lod; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        $X->appendChild($this->LatLonAltBox->render($doc));
        if (isset($this->Lod)) $X->appendChild($this->Lod->render($doc));

        return $X;
    }
}



/*
$a = new kml_Region(new kml_LatLonAltBox(2,2,2,2));
$a->dump(false);
*/
