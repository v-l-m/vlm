<?php

class kml_Document extends kml_Container {

    protected $tagName = 'Document';

    var $Schemas;


    /* Constructor */
    function kml_Document($name = null) {
        parent::kml_Container();
        if ($name !== null) $this->set_name($name);
    }


    /* Assignments */
    function add_Schema($Schema) { $this->Schemas[] = $Schema; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        if (isset($this->Schemas))
            foreach($this->Schemas as $S)
                $X->appendChild($S->render($doc));

        return $X;
    }
}


/*

$a = new kml_Document();
$a->set_id('uid000000111');
include_once('kml_Placemark.php');
$p = new kml_Placemark('Sonomusic');
$a->add_Feature($p);

$a->dump(false);

*/
