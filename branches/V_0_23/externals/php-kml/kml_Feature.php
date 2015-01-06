<?php
/**<Feature> rendering class.
 * @class kml_Feature
 * .... 
 * 
 * $Rev$
 */


abstract class kml_Feature extends kml_Object {

    protected $tagName = 'Feature';

    var $name;
    var $visibility;
    var $open;
    var $address;
    var $addressDetails;
    var $phoneNumber;
    var $Snippet;
    var $description;
    var $LookAt;
    var $TimePrimitive;
    var $styleUrl;
    var $StyleSelectors;
    var $Region;


    /* Constructor */
    function kml_Feature($name = null) {
        parent::kml_Object();
        if ($name !== null) $this->set_name($name);
    }


    /* Assignments */
    function set_name($name) { $this->name = $name; }
    function set_visibility($visibility) { $this->visibility = (int)$visibility; }
    function set_open($open) { $this->open = (int)$open; }

    function set_address($address) { $this->address = $address; }
    function set_addressDetails($addressDetails) { $this->addressDetails = $addressDetails; }

    function set_phoneNumber($phoneNumber) { $this->phoneNumber = $phoneNumber; }

    function set_Snippet($Snippet) { $this->Snippet = $Snippet; }

    function set_description($description)  { $this->description = $description; }
    function set_LookAt($LookAt) { $this->LookAt = $LookAt; }

    function set_TimePrimitive($TimePrimitive) {  $this->TimePrimitive = $TimePrimitive; }
    function set_styleUrl($styleUrl)  { $this->styleUrl = $styleUrl; }
    function add_StyleSelector($StyleSelector) { $this->StyleSelectors[] = $StyleSelector; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->name)) $X->appendChild(XML_create_text_element($doc, 'name', $this->name));
        if (isset($this->visibility)) $X->appendChild(XML_create_text_element($doc, 'visibility', $this->visibility));
        if (isset($this->open)) $X->appendChild(XML_create_text_element($doc, 'open', $this->open));
        if (isset($this->address)) $X->appendChild(XML_create_text_element($doc, 'address', $this->address));
        if (isset($this->addressDetails)) $X->appendChild(XML_create_text_element($doc, 'addressDetails', $this->addressDetails));
        if (isset($this->phoneNumber)) $X->appendChild(XML_create_text_element($doc, 'phoneNumber', $this->phoneNumber));
        if (isset($this->Snippet)) { $X->appendChild($this->Snippet->render($doc)); }
        if (isset($this->description)) $X->appendChild(XML_create_text_element($doc, 'description', $this->description));
        if (isset($this->LookAt)) $X->appendChild($this->LookAt->render($doc));
        if (isset($this->TimePrimitive)) $X->appendChild($this->TimePrimitive->render($doc));
        if (isset($this->styleUrl)) $X->appendChild(XML_create_text_element($doc, 'styleUrl', $this->styleUrl));
        if (isset($this->StyleSelectors))
            foreach($this->StyleSelectors as $ss)
                $X->appendChild($ss->render($doc));

        if (isset($this->Region)) $X->appendChild($this->Region->Render($doc));

        return $X;
    }
}



/*
$a = new kml_Feature('a feature');
$a->dump(false);
*/
