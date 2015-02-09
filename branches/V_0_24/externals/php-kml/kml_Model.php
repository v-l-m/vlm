<?php

class kml_Model extends kml_Geometry {

    protected $tagName = 'Model';

    var $Location;
    var $Orientation;
    var $Scale;


    /* Constructor */
    function kml_Model() {
        parent::kml_Geometry();
    }


    /* Assignments */
    function set_Location($Location) { $this->Location = $Location; }
    function set_Orientation($Orientation) { $this->Orientation = $Orientation; }
    function set_Scale($Scale) { $this->Scale = $Scale; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->Location)) $X->appendChild($this->Location->render($doc));
        if (isset($this->Orientation)) $X->appendChild($this->Orientation->render($doc));
        if (isset($this->Scale)) $X->appendChild($this->Scale->render($doc));


        return $X;
    }
}


/*
$a = new kml_Model(array(array(3, 4), array(3, 5)));
$a->dump(true);
*/

