<?php

class kml_ListStyle extends kml_Object {

    protected $tagName = 'ListStyle';

    var $bgColor;
    var $listItemType;
    var $ItemIcon;


    /* Constructor */
    function kml_ListStyle() {
        parent::kml_Object();
    }


    /* Assignments */
    function set_bgColor($bgColor) { $this->bgColor = $bgColor; }
    function set_listItemType($listItemType) { $this->listItemType = $listItemType; }
    function set_ItemIcon($ItemIcon) { $this->ItemIcon = $ItemIcon; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->bgColor)) $X->appendChild(XML_create_text_element($doc, 'bgColor', $this->bgColor));
        if (isset($this->listItemType)) $X->appendChild(XML_create_text_element($doc, 'listItemType', $this->listItemType));
        if (isset($this->ItemIcon)) { $X->appendChild($this->ItemIcon->render($doc)); }

        return $X;
    }
}

/*
$a = new kml_ListStyle();
$a->set_ItemIcon_State('normal');
$a->dump(false);
*/
