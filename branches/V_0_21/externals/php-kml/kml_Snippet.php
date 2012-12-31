<?php

class kml_Snippet extends kml_root {

    protected $tagName = 'Snippet';

    var $maxLines;
    var $Snippet;


    /* Constructor */
    function kml_Snippet($Snippet = null, $maxLines = null) {
        parent::kml_root();
        if ($Snippet !== null) $this->set_Snippet($Snippet);
        if ($maxLines !== null) $this->set_maxLines($maxLines);
    }


    /* Assignments */
    function set_Snippet($Snippet) { $this->Snippet = $Snippet; }
    function set_maxLines($maxLines = 2) { $this->maxLines = $maxLines; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->Snippet)) $X->appendChild($doc->create_text_node($this->Snippet));
        if (isset($this->maxLines)) $X->setAttribute('maxLines', $this->maxLines);

        return $X;
    }

}

/**
$a = new kml_Snippet('iiii');
$a->dump(false);
*/
