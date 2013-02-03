<?php

class kml_NetworkLinkControl extends kml_root {

    protected $tagName = 'NetworkLinkControl';

    var $minRefreshPeriod;
    var $cookie;
    var $message;
    var $linkName;
    var $linkDescription;
    var $linkSnippet;
    var $expires;
    var $LookAt;


    /* Constructor */
    function kml_NetworkLinkControl() {
        parent::kml_root();
    }


    /* Assignments */
    function set_minRefreshPeriod($minRefreshPeriod) { $this->minRefreshPeriod = $minRefreshPeriod; }
    function set_cookie($cookie) { $this->cookie = $cookie; }
    function set_message($message) { $this->message = $message; }
    function set_linkName($linkName) { $this->linkName = $linkName; }
    function set_linkDescription($linkDescription) { $this->linkDescription = $linkDescription; }
    function set_linkSnippet($Snippet) {
        $Snippet->tagName = 'linkSnippet';
        $this->linkSnippet = $Snippet;
    }
    function set_expires($expires) { $this->expires = $expires; }
    function set_LookAt($LookAt) { $this->LookAt = $LookAt; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        if (isset($this->minRefreshPeriod)) $X->appendChild(XML_create_text_element($doc, 'minRefreshPeriod', $this->minRefreshPeriod));
        if (isset($this->cookie)) $X->appendChild(XML_create_text_element($doc, 'cookie', $this->cookie));
        if (isset($this->message)) $X->appendChild(XML_create_text_element($doc, 'message', $this->message));
        if (isset($this->linkName)) $X->appendChild(XML_create_text_element($doc, 'linkName', $this->linkName));
        if (isset($this->linkDescription)) $X->appendChild(XML_create_text_element($doc, 'linkDescription', $this->linkDescription));
        if (isset($this->linkSnippet)) {
            $s = XML_create_text_element($doc, 'linkSnippet', $this->linkSnippet);
            if (isset($this->linkSnippet_maxLines)) $s->setAttribute('maxLines', $this->linkSnippet_maxLines);
            $X->appendChild($s);
        }
        if (isset($this->expires)) $X->appendChild(XML_create_text_element($doc, 'expires', $this->expires));
        if (isset($this->LookAt)) $X->appendChild($this->LookAt->render($doc));

        return $X;
    }
}


/*
include_once('kml_LookAt.php');
$b = new kml_LookAt();

$a = new kml_NetworkLinkControl();
$a->set_LookAt($b);
$a->set_linkName('slim');
$a->dump(false);
*/
