<?php

class kml_Style extends kml_StyleSelector {

    protected $tagName = 'Style';

    var $IconStyle;
    var $LabelStyle;
    var $LineStyle;
    var $PolyStyle;
    var $BalloonStyle;
    var $ListStyle;


    /* Constructor */
    function kml_Style($id = null) {
        parent::kml_StyleSelector();
		if ($id !== null) $this->set_id($id);
    }


    /* Assignments */
    function set_IconStyle($IconStyle) { $this->IconStyle = $IconStyle; }
    function set_LabelStyle($LabelStyle) { $this->LabelStyle = $LabelStyle; }
    function set_LineStyle($LineStyle) { $this->LineStyle = $LineStyle; }
    function set_PolyStyle($PolyStyle) { $this->PolyStyle = $PolyStyle; }
    function set_BalloonStyle($BalloonStyle) { $this->BalloonStyle = $BalloonStyle; }
    function set_ListStyle($ListStyle) { $this->ListStyle = $ListStyle; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->IconStyle))  $X->appendChild($this->IconStyle->render($doc));
        if (isset($this->LabelStyle))  $X->appendChild($this->LabelStyle->render($doc));
        if (isset($this->LineStyle))  $X->appendChild($this->LineStyle->render($doc));
        if (isset($this->PolyStyle))  $X->appendChild($this->PolyStyle->render($doc));
        if (isset($this->BalloonStyle))  $X->appendChild($this->BalloonStyle->render($doc));
        if (isset($this->ListStyle))  $X->appendChild($this->ListStyle->render($doc));

        return $X;
    }
}

/*
$a = new kml_Style();
$a->set_LineStyle_State('normal');
$a->dump(false);
*/
