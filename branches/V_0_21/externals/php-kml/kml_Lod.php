<?php

class kml_Lod extends kml_root {

    protected $tagName = 'Lod';

    var $minLodPixels;
    var $maxLodPixels;
    var $minFadeExtent;
    var $maxFadeExtent;


    /* Constructor */
    function kml_Lod() {
        parent::kml_root();
    }


    /* Assignments */
    function set_minLodPixels($minLodPixels) { $this->minLodPixels = $minLodPixels; }
    function set_maxLodPixels($maxLodPixels) { $this->maxLodPixels = $maxLodPixels; }
    function set_minFadeExtent($minFadeExtent) { $this->minFadeExtent = $minFadeExtent; }
    function set_maxFadeExtent($maxFadeExtent) { $this->maxFadeExtent = $maxFadeExtent; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->minLodPixels)) $X->appendChild(XML_create_text_element($doc, 'minLodPixels', $this->minLodPixels));
        if (isset($this->maxLodPixels)) $X->appendChild(XML_create_text_element($doc, 'maxLodPixels', $this->maxLodPixels));
        if (isset($this->minFadeExtent)) $X->appendChild(XML_create_text_element($doc, 'minFadeExtent', $this->minFadeExtent));
        if (isset($this->maxFadeExtent)) $X->appendChild(XML_create_text_element($doc, 'maxFadeExtent', $this->maxFadeExtent));

        return $X;
    }
}

/*
$a = new kml_Lod();
$a->dump(false);
*/
