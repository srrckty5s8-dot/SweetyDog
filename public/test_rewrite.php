<?php
// Vérifier si mod_rewrite est activé
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "<h2>Apache Modules</h2>";
    echo implode("<br>", $modules);
    
    echo "<h2>mod_rewrite Status</h2>";
    if (in_array('mod_rewrite', $modules)) {
        echo "✅ mod_rewrite est ACTIVÉ";
    } else {
        echo "❌ mod_rewrite est DÉSACTIVÉ";
    }
} else {
    echo "❌ apache_get_modules() non disponible - vous utilisez peut-être FastCGI ou PHP-FPM";
}

echo "<h2>SERVER INFO</h2>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
?>
