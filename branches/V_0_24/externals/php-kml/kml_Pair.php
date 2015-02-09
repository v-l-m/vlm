<?php

class kml_Pair extends kml_root {

    protected $tagName = 'Pair';

    var $key;
    var $styleUrl;


    /* Constructor */
    function kml_Pair($key = null, $styleUrl = null) {
        parent::kml_root();
		if ($key !== null) $this->set_key($key);
		if ($styleUrl !== null) $this->set_styleUrl($styleUrl);
    }


    /* Assignments */
    function set_key($key) { $this->key = $key; }
    function set_styleUrl($styleUrl) { $this->styleUrl = $styleUrl; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->key)) $X->appendChild(XML_create_text_element($doc, 'key', $this->key));
        if (isset($this->styleUrl)) $X->appendChild(XML_create_text_element($doc, 'styleUrl', $this->styleUrl));
        return $X;
    }

}

/**
$a = new kml_Pair('iiii');
$a->dump(false);
*/
