<?php

class kml_Folder extends kml_Container {

    protected $tagName = 'Folder';


    /* Constructor */
    function kml_Folder($name = null) {
        parent::kml_Container();
        if ($name !== null) $this->set_name($name);

    }

    /* Render */
    function render($doc) {
        $X = parent::render($doc);
        return $X;
    }
}


/*

$a = new kml_Folder('Addresses Utiles');
$a->set_id('uid000000111');
$p = new kml_Placemark('Sonomusic');
$p->LookAt->set_tilt(30);
$a->add_Feature($p);

$a->dump();


*/
