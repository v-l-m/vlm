<?php
/** \mainpage kml-php
 *
 * \section intro_sec Introduction
 *
 * php-kml is a library for creating <a href="http://en.wikipedia.org/wiki/Keyhole_Markup_Language">kml</a>
 * files. If you're not familiar with this format, I strongly recommend taking a 
 * quick look at the <a href="http://code.google.com/apis/kml/documentation/kmlreference.html">complete reference</a>
 * first.
 *
 * The actual version is bases on the 2.1 version of kml.
 *
 * \section usage_sec Usage
 *
 * All <b>main</b> kml <Element>s have a correspondig kml_Element class (eg. kml_Placemark,
 * kml_Document, etc.)
 *
 * Theses classes are composed of set_xx methods and add_xx methods depending
 * on wehter you can have multiple <xx> elements or not. The set_xx methods are
 * also used to set attributes (eg. set_id('foo'))
 *
 * Here's the code for generating a Placemark named "My House" with lon=10 and lat=32
 * \code
 * include_once('lib/kml.php');
 *
 * $p = new kml_Placemark();
 * $p->set_name('My House');
 * $p->set_Geometry( new kml_Point(10, 32) );
 * \endcode
 *
 * Most of the classes' constructors, take optional arguments. For instance, the 
 * previous code could be shortened to
 * \code
 * include_once('lib/kml.php');
 *
 * $p = new kml_Placemark('My House', new kml_Point(10, 32));
 * \endcode
 *
 * \section render_sec Rendering the kml file
 *
 * Once you have finsished creating your kml object, you can call it's dump() method
 * to render the <xml>
 *
 * \code
 * $p->dump();
 * \endcode
 * which will output
 * \code
 * <?xml version="1.0" encoding="UTF-8"?>
 * <kml xmlns="http://earth.google.com/kml/2.1">
 * <Placemark>
 *   <name>My place</name>
 *   <Point>
 *     <coordinates>10, 32, 0</coordinates>
 *   </Point>
 * </Placemark>
 * </kml>
 * \endcode
 *
 *
 * \section more_sec What else ?
 *
 * On top of all the kml_xx classes representing the standard <elements> as defined by
 * the kml schema, you'll find some skml_xx classes. (skml standing for specialKml,
 * or perhaps did I think SelimKml?)
 *
 * These skml_xx are shortcuts for creating complex objects.
 *
 * For example if you 
 * to draw a circle, instead of using <LinearRing> and then computing all the
 * edges (yes! circles have edges), you just use the skml_Circle class.
 * \code
 * $c = new skml_Circle(10, 20, 3);
 * $c->dump();
 * \endcode
 * which will generate a <LinearRing> containing a circle centered on lon=10 and lat=20
 * with a radius of 3 (we're talking 3 degrees : visible from the moon)
 * \code
 * <?xml version="1.0" encoding="utf-8"?>
 * <kml xmlns="http://earth.google.com/kml/2.1">
 *   <LinearRing>
 *     <coordinates><![CDATA[13,20,0, 12.9631023843,20.4690674368,0 12.8533171598,20.9265965603, ......, 13,20,0]]></coordinates>
 *   </LinearRing>
 * </kml>
 * \endcode
 *
 * Keep in mind that because skml_Circle extends kml_LinearRing wich extend kml_Geomerty,
 * skml_Circle inherits all the methods of it's parents.
 *
 */

error_reporting(E_ALL);

function __autoload($class_name) {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . $class_name . '.php';
}

function XML_create_text_element($doc, $tagName, $content = null, $attributes = array())
{
    $e = $doc->createElement($tagName);
    if ($content !== null) $e->appendChild($doc->createCDataSection($content));
    foreach($attributes as $k => $v) $e->setAttribute($k, $v);
    return $e;
}

function color2kml($color, $alpha = 'FF') {
    return sprintf("$alpha%s%s%s", 
		substr($color,4,2), 
		substr($color,2,2), 
		substr($color,0,2));
}

/**** definitions ****/

// altitudeModeEnum <LookAt> & <Region>
define('KML_clampToGround',     'clampToGround');
define('KML_relativeToGround', 'relativeToGround');
define('KML_absolute',          'absolute ');

// colorModeEnum <ColorStyle>
define('KML_normal', 'normal');
define('KML_random', 'random');

// refreshModeEnum <Link>
define('KML_onChange',   'onChange');
define('KML_onInterval', 'onInterval');
define('KML_onExpire',   'onExpire');

// viewRefreshEnum <Link>
define('KML_never',     'never');
define('KML_onStop',    'onStop');
define('KML_onRequest', 'onRequest');
define('KML_onRegion',  'onRegion');

// listItemTypeEnum <ListStyle>
define('KML_check',             'check');
define('KML_radioFolder',       'radioFolder');
define('KML_checkOffOnly',      'checkOffOnly');
define('KML_checkHideChildren', 'checkHideChildren');

// styleStateEnum <StyleMap>
//define('KML_normal',    'normal');
define('KML_highlight', 'highlight');

// unitsEnum See <hotSpot> in <IconStyle>, <ScreenOverlay>
define('KML_fraction',    'fraction');
define('KML_pixels',      'pixels');
define('KML_insetPixels', 'insetPixels');


// itemIconModeEnum
define('KML_open',      'open');
define('KML_closed',    'closed');
define('KML_error',     'error');
define('KML_fetching0', 'fetching0');
define('KML_fetching1', 'fetching1');
define('KML_fetching2', 'fetching2');

