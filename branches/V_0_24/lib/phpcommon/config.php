<?php

    // This file is a global wrapper
    // You should better call specifics config files

    // Constant defined in param.php have precedence over constant below.
    require_once("config-funcs.php");
    include_once("param.php");

    // Specific (and reusable) config-sets
    include_once("config-mysql.php");
    include_once("config-defines.php");

?>
