<?php
    function define_if_not($k, $v) {
        if (!defined($k)) define($k, $v);
    }

    function die503($msg) {
          header("HTTP/1.1 503 Service Temporarily Unavailable");
          header("Status: 503 Service Temporarily Unavailable");
          die($msg);
    }

    function get_cgi_var($name, $default_value = null) {
        //From phpmyedit.org
        static $magic_quotes_gpc = null;
        if ($magic_quotes_gpc === null) {
            $magic_quotes_gpc = get_magic_quotes_gpc();
        }
        $var = @$_GET[$name];
        if (! isset($var)) {
            $var = @$_POST[$name];
        }
        if (isset($var)) {
            if ($magic_quotes_gpc) {
                if (is_array($var)) {
                    foreach (array_keys($var) as $key) {
                        $var[$key] = stripslashes($var[$key]);
                    }
                } else {
                    $var = stripslashes($var);
                }
            }
        } else {
            $var = @$default_value;
        }
        return $var;
    }

?>
