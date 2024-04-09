<?php

$here = __DIR__;
require_once "$here/config/config.php";

$table_prefix  = 'wp_';
require_once "$here/wp-settings.php";

// must happen after wp-settings
if (WP_DEBUG) {
    error_reporting(E_ALL & ~(E_DEPRECATED|E_NOTICE|E_WARNING));
}
