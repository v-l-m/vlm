<?php

class kml_StyleMap extends kml_StyleSelector {

    protected $tagName = 'StyleMap';

    var $Pairs = array();


    /* Constructor */
    function kml_StyleMap($id) {
        parent::kml_StyleSelector();
		if ($id !== null) $this->set_id($id);
	}


    /* Assignments */
    function add_Pair($Pair) { $this->Pairs[] = $Pair; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

		foreach($this->Pairs as $Pair) $X->appendChild($Pair->render($doc));

        return $X;
    }
}

/*
$a = new kml_StyleMap();
$a->set_LineStyleMap_State('normal');
$a->dump(false);
*/
