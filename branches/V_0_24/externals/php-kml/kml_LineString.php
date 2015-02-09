<?php
/** <LineString> class.
 *
 * This class renders <LineString> elements.
 *
 * Ex:
 * $path = new kml_LineString(array(array(3, 4), array(3, 5)));
 * $path->dump();
 */



class kml_LineString extends kml_Geometry {

    protected $tagName = 'LineString';

    var $extrude;
    var $tesselate;
    var $altitudeMode;
    var $coordinates; // list of tuples


    /** Constructor.
     *
     * @param $coordinates an array of tuples
     */
    function kml_LineString($coordinates) {
        parent::kml_Geometry();
        $this->set_coordinates($coordinates);
    }


    /** Assignments */
    function set_extrude($extrude) { $this->extrude = (int)$extrude; }
    function set_tesselate($tesselate) { $this->tesselate = (int)$tesselate; }
    function set_altitudeMode($altitudeMode) { $this->altitudeMode = $altitudeMode; }
    function set_coordinates($coordinates) { $this->coordinates = $coordinates; }


   /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->extrude)) $X->appendChild(XML_create_text_element($doc, 'extrude', $this->extrude));
        if (isset($this->tesselate)) $X->appendChild(XML_create_text_element($doc, 'tesselate', $this->tesselate));
        if (isset($this->altitudeMode)) $X->appendChild(XML_create_text_element($doc, 'altitudeMode', $this->altitudeMode));

        $tuples = array();
        foreach($this->coordinates as $c) $tuples[] = join(',', $c);
        $X->appendChild(XML_create_text_element($doc, 'coordinates', join(' ', $tuples)));

        return $X;
    }
}



