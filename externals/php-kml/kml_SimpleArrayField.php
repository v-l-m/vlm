<?php

class kml_SimpleArrayField extends kml_SchemaField {

    protected $tagName = 'SimpleArrayField';


    /* Constructor */
    function kml_SimpleArrayField($name = null, $type = null) {
        parent::kml_SchemaField();
        if ($name !== null) $this->name($name);
        if ($type !== null) $this->type($type);

    }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);
        return $X;
    }

}

/**
$a = new kml_SimpleArrayField('iiii');
$a->dump(false);
*/
