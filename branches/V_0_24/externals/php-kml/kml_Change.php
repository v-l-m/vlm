<?php

class kml_Change extends kml_root {

    protected $tagName = 'Change';

    var $Object;


    /* Constructor */
    function kml_Change($Object) {
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
$a = new kml_Change('iiii');
$a->dump(false);
*/
