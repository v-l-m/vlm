<?php

class kml_SimpleField extends kml_SchemaField {

    protected $tagName = 'SimpleField';


    /* Constructor */
    function kml_SimpleField($name = null, $type = null) {
        parent::kml_SchemaField();
        if ($name !== null) $this->set_name($name);
        if ($type !== null) $this->set_type($type);

    }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);
        return $X;
    }

}


