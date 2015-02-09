<?php

class kml_Update extends kml_root {

    protected $tagName = 'Update';

    var $targetHref;
    var $updates;


    /* Constructor */
    function kml_Update($targetHref) {
        parent::kml_root();
        $this->set_targetHref($targetHref);
    }


    /* Assignments */
    function set_targetHref($targetHref) { $this->targetHref = $targetHref; }
    function add_update($update) { $this->updates[] = $update; }


    /* Render */
    function render($doc) {
        $X = $doc->createElement($this->tagName);

        $X->appendChild(XML_create_text_element($doc, 'targetHref', $this->targetHref));

        if (isset($this->updates))
            foreach($this->updates as $u)
                $X->appendChild($u->render($doc));

        return $X;
    }

}

/*
include_once('kml.php');

$a = new kml_Update('id');
$p = new kml_Placemark('old');
$p->set_targetId('oldPlacemark');
$a->add_update(new kml_Change($p));


$a->dump(false);

*/
