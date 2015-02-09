<?php

class kml_Icon extends kml_Object {

    protected $tagName = 'Icon';

    var $href;
    var $refreshMode;
    var $refreshInterval;
    var $viewRefreshMode;
    var $viewRefreshTime;
    var $viewBoundScale;
    var $viewFormat;
    var $httpQuery;

    /* Constructor */
    function kml_Icon($href) {
        parent::kml_Object();
        $this->set_href($href);
    }

    /* Assignments */
    function set_href($href) { $this->href = $href; }
    function set_refreshMode($refreshMode) { $this->refreshMode = $refreshMode; }
    function set_refreshInterval($refreshInterval) { $this->refreshInterval = $refreshInterval; }
    function set_viewRefreshMode($viewRefreshMode) { $this->viewRefreshMode = $viewRefreshMode; }
    function set_viewRefreshTime($viewRefreshTime) { $this->viewRefreshTime = $viewRefreshTime; }
    function set_viewBoundScale($viewBoundScale) { $this->viewBoundScale = $viewBoundScale; }
    function set_viewFormat($viewFormat) { $this->viewFormat = $viewFormat; }
    function set_httpQuery($httpQuery) { $this->httpQuery = $httpQuery; }


   /* Render */
   function render($doc) {
        $X = parent::render($doc);

        $X->appendChild(XML_create_text_element($doc, 'href', $this->href));
        if (isset($this->refreshMode)) $X->appendChild(XML_create_text_element($doc, 'refreshMode', $this->refreshMode));
        if (isset($this->refreshInterval)) $X->appendChild(XML_create_text_element($doc, 'refreshInterval', $this->refreshInterval));
        if (isset($this->viewRefreshMode)) $X->appendChild(XML_create_text_element($doc, 'viewRefreshMode', $this->viewRefreshMode));
        if (isset($this->viewRefreshTime)) $X->appendChild(XML_create_text_element($doc, 'viewRefreshTime', $this->viewRefreshTime));
        if (isset($this->viewBoundScale)) $X->appendChild(XML_create_text_element($doc, 'viewBoundScale', $this->viewBoundScale));
        if (isset($this->viewFormat)) $X->appendChild(XML_create_text_element($doc, 'viewFormat', $this->viewFormat));
        if (isset($this->httpQuery)) $X->appendChild(XML_create_text_element($doc, 'httpQuery', $this->httpQuery));

        return $X;
    }
}

/*
$a = new kml_Icon('http://whatever.com');
$a->dump(false);
*/
