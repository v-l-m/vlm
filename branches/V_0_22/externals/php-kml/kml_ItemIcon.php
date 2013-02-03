<?php

class kml_ItemIcon extends kml_root {

    protected $tagName = 'ItemIcon';

    var $state;
    var $href;


    /* Constructor */
    function kml_ItemIcon() {
        parent::kml_root();
    }


    /* Assignments */
    function set_state($state) { $this->state = $state; }
    function set_href($href) { $this->href = $href; }


    /* Render */
    function render($doc) {
        $X = parent::render($doc);

        $X->appendChild(XML_create_text_element($doc, 'targetHref', $this->targetHref));

        if (isset($this->state)) $X->appendChild(XML_create_text_element($doc, 'state', $this->state));
        if (isset($this->href)) $X->appendChild(XML_create_text_element($doc, 'href', $this->href));


        return $X;
    }

}

/*
include_once('kml.php');

$a = new kml_ItemIcon('id');
$p = new kml_Placemark('old');
$p->set_targetId('oldPlacemark');
$a->add_ItemIcon(new kml_Change($p));


$a->dump(false);
*/
