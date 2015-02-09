<?php
/**
 * This is where the rendering and dumping happens
 */

abstract class kml_root {


    function kml_root() {}

    /* Render */
    function render($doc) {
        return $doc->createElement($this->tagName);
    }


    /* The Only Dump Function */
    function dump( $header = true, $filename = false, $format = true, $return = false, $kmz = false) {

	// create kml
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = $format;
        $root = $doc->appendChild($doc->createElement('kml'));
        $root->setAttribute('xmlns', 'http://earth.google.com/kml/2.1');
        $root->appendChild($this->render($doc));

	// save kml        
        $output = $doc->saveXml();
        
	// zipped kmz file?
	if ($kmz)
	{
		$zip = new ZipArchive();
		$zipfilename = '/tmp/data' . microtime(true) . '.kmz';

		if ( $zip->open($zipfilename, ZIPARCHIVE::CREATE) !== true ) {
			$kmz =  false;
		} else {
			$zip->addFromString('doc.kml', $output);
			$zip->close();
			$output = file_get_contents($zipfilename);
			unlink($zipfilename);
		}
	}
	
	// http headers
        if ($header) 
	{
		if ($kmz) header('Content-type: application/vnd.google-earth.kmz');
		else      header('Content-type: application/vnd.google-earth.kml+xml; charset=UTF-8');

        	if ($filename) header('Content-Disposition: attachment; filename="'. $filename.'.'.($kmz ? 'kmz' : 'kml').'"');
        } 
	
	// return content instead of dumping 
	if ($return)
        {	
		return $output;
	}
	
	// dump result
        echo $output;
    }
}


