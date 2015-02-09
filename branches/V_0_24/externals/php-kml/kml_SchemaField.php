<?php

class kml_SchemaField extends kml_root {

    protected $tagName = 'SchemaField';

    var $name;
    var $type;


    /* Constructor */
    function kml_SchemaField($name = null, $type = null) {
        parent::kml_root();
        if ($name !== null) $this->set_name($name);
        if ($type !== null) $this->set_type($type);

    }


    /* Assignments */
    function set_name($name) { $this->name = $name; }
    function set_type($type) { $this->type = $type; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->name)) $X->setAttribute('name', $this->name);
        if (isset($this->type)) $X->setAttribute('type', $this->type);

        return $X;
    }

}
