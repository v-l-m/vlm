<?php

class kml_Schema extends kml_root {

    protected $tagName = 'Schema';

    var $name;
    var $parent;
    var $SchemaFields;


    /* Constructor */
    function kml_Schema($name) {
        parent::kml_root();
        $this->set_name($name);
    }


    /* Assignments */
    function set_name($name) { $this->name = $name; }
    function set_parent($parent) { $this->parent = $parent; }
    function add_SchemaField($SchemaField) { $this->SchemaFields[] = $SchemaField; }


   /* Render */
   function render($doc) {
        $X = $doc->createElement($this->tagName);

        $X->setAttribute('name', $this->name);
        if (isset($this->parent)) $X->setAttribute('parent', $this->parent);
        if (isset($this->SchemaFields))
            foreach($this->SchemaFields as $SF)
                $X->appendChild($SF->render($doc));

        return $X;
    }
}


/*

include_once('kml_LookAt.php');
$b = new kml_LookAt();

$a = new kml_Schema();
$a->set_LookAt($b);
$a->set_linkName('slim');
$a->dump(false);
*/
