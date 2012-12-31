<?php

class kml_Container extends kml_Feature {

    protected $tagName = 'Conainter';

    var $Features;


    /* Constructor */
    function kml_Container($name = null) {
        parent::kml_Feature($name);
        if ($name !== null) $this->set_name($name);
    }


    /* Assignments */
    function add_Feature($Feature) { $this->Features[] = $Feature; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->Features))
            foreach($this->Features as $F)
                $X->appendChild($F->render($doc));

        return $X;
    }
}

