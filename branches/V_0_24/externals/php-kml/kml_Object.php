<?php

class kml_Object extends kml_root {

    protected $tagName = 'Object';

    var $id;
    var $targetId;

    /* Constructor */
    function kml_Object($id = null, $targetId = null) {
        if ($id !== null) $this->set_id($id);
        if ($targetId !== null) $this->set_targetId($targetId);
    }

    /* Assignments */
    function set_id($id) { $this->id = $id; }
    function set_targetId($targetId) { $this->targetId = $targetId; }

    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->id)) $X->setAttribute('id', $this->id);
        if (isset($this->targetId)) $X->setAttribute('targetId', $this->targetId);

        return $X;
    }

}

/**
$a = new kml_Object('iiii');
$a->dump(false);
*/
