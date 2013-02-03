<?php

class kml_ObjArrayField extends kml_SchemaField {

    protected $tagName = 'ObjArrayField';


    /* Constructor */
    function kml_ObjArrayField($name = null, $type = null) {
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
$a = new kml_ObjArrayField('iiii');
$a->dump(false);
*/
