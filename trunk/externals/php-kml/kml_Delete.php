<?php

class kml_Delete extends kml_root {

    protected $tagName = 'Delete';

    var $Object;


    /* Constructor */
    function kml_Delete($Object) {
        parent::kml_root();
        $this->set_Object($Object);
    }


    /* Assignments */
    function set_Object($Object) { $this->Object = $Object; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        $X->appendChild($this->Object->render($doc));

        return $X;
    }

}

/**
$a = new kml_Delete('iiii');
$a->dump(false);
*/
