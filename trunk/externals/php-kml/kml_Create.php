<?php

class kml_Create extends kml_root {

    protected $tagName = 'Create';

    var $Object;


    /* Constructor */
    function kml_Create($Object) {
        parent::kml_root();
        $this->set_Object($Object);
    }


    /* Assignments */
    function set_Object($Object) { $this->Object = $Object; }


    /* Render */
    function render($doc) {
        $X = $doc->createElement($this->tagName);

        $X->appendChild($this->Object->render($doc));

        return $X;
    }

}

/**
$a = new kml_Create('iiii');
$a->dump(false);
*/
