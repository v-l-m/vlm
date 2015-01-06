<?php

class kml_BalloonStyle extends kml_Object {

    protected $tagName = 'BalloonStyle';

    var $bgColor;
    var $textColor;
    var $text;


    /* Constructor */
    function kml_BalloonStyle($bgColor = null, $textColor = null) {
        parent::kml_Object();
        if ($bgColor !== null) $this->set_bgColor($bgColor);
        if ($textColor !== null) $this->set_textColor($textColor);
    }


    /* Assignments */
    function set_bgColor($bgColor) { $this->bgColor = $bgColor; }
    function set_textColor($textColor) { $this->textColor = $textColor; }
    function set_text($text) { $this->text = $text; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->bgColor)) $X->appendChild(XML_create_text_element($doc, 'bgColor', $this->bgColor));
        if (isset($this->textColor)) $X->appendChild(XML_create_text_element($doc, 'textColor', $this->textColor));
        if (isset($this->text)) $X->appendChild(XML_create_text_element($doc, 'text', $this->text));

        return $X;
    }
}

/*
$a = new kml_BalloonStyle();
$a->dump(false);
*/
