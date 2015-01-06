<?php

class kml_Polygon extends kml_Geometry {

    protected $tagName = 'Polygon';

    var $extrude;
    var $tesselate;
    var $altitudeMode;

    var $outerBoundaryIs;
    var $innerBoundaryIs = array();


    /* Constructor */
    function kml_Polygon($outerBoundaryIs) {
        parent::kml_Geometry();
        $this->set_outerBoundaryIs($outerBoundaryIs);
    }


    /* Assignments */
    function set_extrude($extrude) { $this->extrude = (int)$extrude; }
    function set_tesselate($tesselate) { $this->tesselate = (int)$tesselate; }
    function set_altitudeMode($altitudeMode) { $this->altitudeMode = $altitudeMode; }

    function set_outerBoundaryIs($outerBoundaryIs) { $this->outerBoundaryIs = $outerBoundaryIs; }
    function add_innerBoundaryIs($innerBoundaryIs) { $this->innerBoundaryIs[] = $innerBoundaryIs; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->extrude)) $X->appendChild(XML_create_text_element($doc, 'extrude', $this->extrude));
        if (isset($this->tesselate)) $X->appendChild(XML_create_text_element($doc, 'tesselate', $this->tesselate));
        if (isset($this->altitudeMode)) $X->appendChild(XML_create_text_element($doc, 'altitudeMode', $this->altitudeMode));

        $b = $X->appendChild($doc->createElement('outerBoundaryIs'));
        $b->appendChild($this->outerBoundaryIs->render($doc));

        if ( $this->innerBoundaryIs )
        {
            $b = $X->appendChild($doc->createElement('innerBoundaryIs'));
            foreach($this->innerBoundaryIs as $innerBoundaryIs)
                $b->appendChild($innerBoundaryIs->render($doc));
        }

        return $X;
    }
}



