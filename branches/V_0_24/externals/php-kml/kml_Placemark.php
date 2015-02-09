<?php
/** <Placemark> A visible object.
 *
 * This class represents a <Placemark>
 *
 */

class kml_Placemark extends kml_Feature {

    protected $tagName = 'Placemark';

    var $Geometry;

    /* Constructor */
    function kml_Placemark($name = null, $Geometry = null) {
        parent::kml_Feature($name);
        if ($Geometry !== null) $this->set_Geometry($Geometry);
    }

    /* Assignments */
    function set_Geometry($Geometry) { $this->Geometry = $Geometry; }

    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->Geometry))
            $X->appendChild($this->Geometry->render($doc));

        return $X;
    }
}

