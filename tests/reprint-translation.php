<?php

    include "../site/includes/strings.inc";

    foreach ($strings as $k => $v) {
        ksort($v);
        $strings[$k] = $v;
    }

    echo '<?php\n$strings = ';
    var_export($strings);
    echo ';\n\n?>';

?>
    
