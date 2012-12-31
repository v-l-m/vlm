<?php
/** Renders a circle using <LineaRing>
 *
 * @class skml_Circle
 * 
 * A shortcut for drawing circles or even triangles ($steps = 3), squares
 * ($steps = 4) ....
 *
 * \code
 * // Example
 * include_once('lib/kml.php');
 *
 * $discs = new kml_MultiGeometry();
 * for ($i = -180; $i < 180; $i++) {
 *    $disc = new kml_Polygon(new skml_Circle($i, 0, 0.5, 0, 3));
 *    $disc->add_innerBoundaryIs(new skml_Circle($i, 0, 0.1));
 *    $discs->add_Geometry($disc);
 * }
 * $placemark = new kml_Placemark('Lots of punched triangles ', $discs);
 *
 * $placemark->dump();
 * \endcode
 */


class skml_Circle extends kml_LinearRing {


    /** Constructor.
     *
     * @param $lon longitude of the circle's center
     * @param $lat loatitude of the circle's center
     * @param $radius radius of the circle
     * @param $alt altitude of the circle (default = 0)
     * @param $steps number of lines used to draw the circle (default = 40). 
     * @param $offset starting angle (default = 0)
     *
     */
    function skml_Circle($lon, $lat, $radius, $alt = 0, $steps = 40, $offset = 0) {
        
        // we don't allow any mistake!
        $steps = (int)$steps;
        if ( $steps < 1 ) $steps = 1;

        $angle = $offset;
        $coordinates = array();
        
        for ($i = 0; $i < $steps; $i++)
        {
            $coordinates[] = array( 
                $lon + $radius * cos($angle),
                $lat + $radius * sin($angle),
                $alt
            );
            $angle += 6.28 / $steps;
        }
        // we need to close the circle
        $coordinates[] = $coordinates[0];
        
        parent::kml_LinearRing($coordinates);
    }
}

