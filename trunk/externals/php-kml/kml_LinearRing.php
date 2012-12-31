<?php
/** <LinearRing>, a closed line string.
 *
 * Defines a closed line string, typically the outer boundary of a Polygon. 
 * Optionally, a LinearRing can also be used as the inner boundary of a Polygon
 * to create holes in the Polygon. A Polygon can contain multiple <LinearRing> 
 * elements used as inner boundaries.
 *
 * Ex:.
 * $path = new kml_LinearRing(array(array(3, 4), array(3, 5)));.
 * $path->dump();.
 *
 * @see http://code.google.com/apis/kml/documentation/kmlreference.html#linearring
 */


/*******************************/

class kml_LinearRing extends kml_Geometry {

    protected $tagName = 'LinearRing';

    /** Boolean.
     * Boolean value. Specifies whether to connect the LinearRing to the ground.
     * To extrude this geometry, the <altitudeMode> must be either 
     * relativeToGround or absolute, and the altitude component within the
     *  <coordinates> element must be greater than 0 (that is, in the air). 
     * Only the vertices of the LinearRing are extruded, not the center of the 
     * geometry. The vertices are extruded toward the center of the Earth's 
     * sphere.
     */
    var $extrude;
    
    /** Boolean value.
     * Boolean value. Specifies whether to allow the LinearRing to follow the 
     * terrain. To enable tessellation, the value for <altitudeMode> must be 
     * clampToGround. Very large LinearRings should enable tessellation so that
     *  they follow the curvature of the earth (otherwise, they may go 
     * underground and be hidden).
     */
    var $tesselate;
    var $altitudeMode;
    var $coordinates; // list of tuples


    /* Constructor */
    function kml_LinearRing($coordinates) {
        parent::kml_Geometry();
        $this->set_coordinates($coordinates);
    }


    /* Assignments */
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


