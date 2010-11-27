<?php
    function define_if_not($k, $v) {
        if (!defined($k)) define($k, $v);
    }

    function die503($msg) {
          header("HTTP/1.1 503 Service Temporarily Unavailable");
          header("Status: 503 Service Temporarily Unavailable");
          die($msg);
    }
?>
